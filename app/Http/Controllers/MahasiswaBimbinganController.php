<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Absensi;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MahasiswaBimbinganController extends Controller
{
    public function index(Request $request)
    {
        $dosenId = Auth::id();

        // Query Mahasiswa yang dibimbing oleh Dosen yang sedang login
        $query = Pendaftaran::has('user')
            ->with([
                'user.mahasiswaProfile.prodi',
                'lowongan.perusahaan'
            ])
            ->where('dosen_id', $dosenId)
            ->whereIn('status_seleksi', ['diterima', 'selesai']);

        // Filter Pencarian (Nama, NIM, Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($m) => $m->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        $bimbingans = $query->latest()->get();

        $targetJamDefault = 900;

        // Transform data untuk menghitung kalkulasi jam, progress, dan pemisahan status SPV vs Dosen
        $bimbingans->transform(function ($item) use ($targetJamDefault) {
            $userId = $item->user_id;

            // 1. Ambil absensi mahasiswa secara aman
            $absensis = Absensi::where('user_id', $userId)->get();

            $totalJam = 0;
            foreach ($absensis as $absen) {
                if (isset($absen->jam_diperoleh) && (float)$absen->jam_diperoleh > 0) {
                    $totalJam += (float) $absen->jam_diperoleh;
                } elseif (isset($absen->durasi_jam) && (float)$absen->durasi_jam > 0) {
                    $totalJam += (float) $absen->durasi_jam;
                } else {
                    $totalJam += 8;
                }
            }

            $targetJam = $item->user?->mahasiswaProfile?->prodi?->target_jam_magang ?? $targetJamDefault;
            $persentase = $targetJam > 0 ? min(100, round(($totalJam / $targetJam) * 100, 1)) : 0;
            $sisaJam = max(0, $targetJam - $totalJam);

            // 2. KLASIFIKASI STATUS LOGBOOK (Berdasarkan kolom `status_asistensi`)
            
            // a. Logbook yang SUDAH DI-APPROVE SPV dan SIAP DIASISTENSI DOSEN
            $logbookReadyDosen = Logbook::where('user_id', $userId)
                ->where('status_asistensi', 'approved_spv')
                ->count();

            // b. Logbook yang MASIH MENUNGGU APPROVAL SPV
            $logbookWaitingSpv = Logbook::where('user_id', $userId)
                ->whereIn('status_asistensi', ['pending', 'pending_spv'])
                ->count();

            // c. Logbook yang statusnya REVISI
            $logbookRevisi = Logbook::where('user_id', $userId)
                ->where('status_asistensi', 'revisi')
                ->count();

            // 3. Status Keaktifan Presensi Hari Ini
            $absenHariIni = $absensis->first(function($a) {
                if (isset($a->tanggal)) {
                    return Carbon::parse($a->tanggal)->isToday();
                }
                return $a->created_at ? $a->created_at->isToday() : false;
            });

            $item->total_jam = round($totalJam, 1);
            $item->target_jam = $targetJam;
            $item->persentase_jam = $persentase;
            $item->sisa_jam = round($sisaJam, 1);
            $item->logbook_ready_dosen = $logbookReadyDosen;
            $item->logbook_waiting_spv = $logbookWaitingSpv;
            $item->logbook_revisi = $logbookRevisi;
            $item->absen_hari_ini = $absenHariIni;

            return $item;
        });

        // Filter Toolbar
        if ($request->filled('status_laporan') && $request->status_laporan !== 'semua') {
            if ($request->status_laporan === 'ready') {
                $bimbingans = $bimbingans->where('logbook_ready_dosen', '>', 0);
            } elseif ($request->status_laporan === 'waiting_spv') {
                $bimbingans = $bimbingans->where('logbook_waiting_spv', '>', 0);
            } elseif ($request->status_laporan === 'uptodate') {
                $bimbingans = $bimbingans->where('logbook_ready_dosen', 0)->where('logbook_waiting_spv', 0);
            }
        }

        // Statistik Cepat Dosen
        $totalBimbingan = $bimbingans->count();
        $siapAsistensi = $bimbingans->where('logbook_ready_dosen', '>', 0)->count();

        return view('dashboard.mahasiswa-bimbingan.index', compact(
            'bimbingans', 
            'totalBimbingan', 
            'siapAsistensi'
        ));
    }
}