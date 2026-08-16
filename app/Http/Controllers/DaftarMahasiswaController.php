<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Absensi;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DaftarMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Inisialisasi Query Pendaftaran Mahasiswa Aktif / Terdaftar
        $query = Pendaftaran::has('user')
            ->with([
                'user.mahasiswaProfile.prodi',
                'lowongan.perusahaan',
                'dosen'
            ]);

        // 2. Filter Hak Akses Berdasarkan Role Pengguna
        if ($user->hasRole('dosen')) {
            $query->where('dosen_id', $user->id)
                  ->whereIn('status_seleksi', ['diterima', 'selesai']);
        } elseif ($user->hasRole('spv')) {
            $spvProdiId = $user->spvProfile?->prodi_id;
            $spvPerusahaanId = $user->spvProfile?->perusahaan_id;

            $query->whereIn('status_seleksi', ['diterima', 'selesai'])
                  ->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $spvProdiId))
                  ->whereHas('lowongan', fn($l) => $l->where('perusahaan_id', $spvPerusahaanId));
        } elseif ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            if ($adminProdiId) {
                $query->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $adminProdiId));
            }
        }

        // 3. Filter Pencarian Teks (Nama, NIM, Perusahaan, Posisi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($m) => $m->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        // 4. Filter Status Magang
        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'aktif') {
                $query->where('status_seleksi', 'diterima');
            } elseif ($request->status === 'selesai') {
                $query->where('status_seleksi', 'selesai');
            } elseif ($request->status === 'menunggu') {
                $query->whereIn('status_seleksi', ['menunggu', 'pending']);
            }
        }

        // Export CSV jika diminta
        if ($request->get('export') === 'csv') {
            return $this->exportCsv($query->latest()->get());
        }

        $mahasiswas = $query->latest()->paginate(10)->withQueryString();

        $targetJamDefault = 900;

        // Transformasi Data: Menghitung Akumulasi Jam Kehadiran & Status Logbook
        $mahasiswas->getCollection()->transform(function ($item) use ($targetJamDefault) {
            $userId = $item->user_id;

            // Hitung Jam Presensi
            $absensis = Absensi::where('user_id', $userId)->get();
            $totalJam = 0;
            foreach ($absensis as $absen) {
                if (isset($absen->jam_diperoleh) && (float)$absen->jam_diperoleh > 0) {
                    $totalJam += (float)$absen->jam_diperoleh;
                } elseif (isset($absen->durasi_jam) && (float)$absen->durasi_jam > 0) {
                    $totalJam += (float)$absen->durasi_jam;
                } else {
                    $totalJam += 8;
                }
            }

            $targetJam = $item->user?->mahasiswaProfile?->prodi?->target_jam_magang ?? $targetJamDefault;
            $persentase = $targetJam > 0 ? min(100, round(($totalJam / $targetJam) * 100, 1)) : 0;
            $sisaJam = max(0, $targetJam - $totalJam);

            // Hitung Logbook
            $logbookApproved = Logbook::where('user_id', $userId)->where('status_asistensi', 'approved')->count();
            $logbookWaitingDosen = Logbook::where('user_id', $userId)->where('status_asistensi', 'approved_spv')->count();
            $logbookWaitingSpv = Logbook::where('user_id', $userId)->whereIn('status_asistensi', ['pending', 'pending_spv'])->count();

            $item->total_jam = round($totalJam, 1);
            $item->target_jam = $targetJam;
            $item->persentase_jam = $persentase;
            $item->sisa_jam = round($sisaJam, 1);
            $item->logbook_approved = $logbookApproved;
            $item->logbook_waiting_dosen = $logbookWaitingDosen;
            $item->logbook_waiting_spv = $logbookWaitingSpv;

            return $item;
        });

        // Hitung Statistik Ringkasan Atas
        $countQuery = clone $query;
        $totalSemua = $countQuery->count();
        
        $countAktifQuery = clone $query;
        $totalAktif = $countAktifQuery->where('status_seleksi', 'diterima')->count();
        
        $countSelesaiQuery = clone $query;
        $totalSelesai = $countSelesaiQuery->where('status_seleksi', 'selesai')->count();

        return view('dashboard.daftar-mahasiswa.index', compact(
            'mahasiswas',
            'totalSemua',
            'totalAktif',
            'totalSelesai'
        ));
    }

    /**
     * Export data direktori mahasiswa ke CSV
     */
    private function exportCsv($data)
    {
        $fileName = 'Direktori_Mahasiswa_Magang_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama Mahasiswa', 'NIM', 'Program Studi', 'Instansi Magang', 'Posisi / Stase', 'Jalur', 'Dosen Pembimbing', 'Status Magang'];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $index => $row) {
                $isMandiri = $row->jalur_magang === 'mandiri';
                fputcsv($file, [
                    $index + 1,
                    $row->user?->name ?? '-',
                    $row->user?->mahasiswaProfile?->nim ?? '-',
                    $row->user?->mahasiswaProfile?->prodi?->nama_prodi ?? '-',
                    $isMandiri ? $row->nama_instansi_mandiri : ($row->lowongan?->perusahaan?->nama_perusahaan ?? '-'),
                    $isMandiri ? ($row->divisi_mandiri ?? 'Mandiri') : ($row->lowongan?->judul_posisi ?? '-'),
                    strtoupper($row->jalur_magang ?? 'REGULER'),
                    $row->dosen?->name ?? 'Belum Ditugaskan',
                    strtoupper($row->status_seleksi ?? 'PROSES')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}