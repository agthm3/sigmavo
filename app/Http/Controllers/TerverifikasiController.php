<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class TerverifikasiController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 1. Dapatkan daftar ID Mahasiswa yang relevan dengan Dosen/Admin Prodi
        $mahasiswaIds = [];

        if ($currentUser->hasRole('admin_prodi')) {
            $prodiId = $currentUser->adminProdiProfile?->prodi_id;
            if ($prodiId) {
                $mahasiswaIds = \App\Models\MahasiswaProfile::where('prodi_id', $prodiId)->pluck('user_id')->toArray();
            }
        } elseif ($currentUser->hasRole('dosen')) {
            // Mengambil mahasiswa yang dibimbing dosen ini
            $mahasiswaIds = \App\Models\Pendaftaran::where('dosen_id', $currentUser->id)->pluck('user_id')->toArray();
            
            // Fallback: Jika belum di-assign, ambil mahasiswa di prodi yang sama
            if (empty($mahasiswaIds)) {
                $prodiId = $currentUser->dosenProfile?->prodi_id;
                if ($prodiId) {
                    $mahasiswaIds = \App\Models\MahasiswaProfile::where('prodi_id', $prodiId)->pluck('user_id')->toArray();
                }
            }
        }

        // --- QUERY 1: LOGBOOK TERVERIFIKASI (Approved / Revisi) ---
        $logbookQuery = Logbook::with(['user.mahasiswaProfile'])
            ->whereIn('status_asistensi', ['approved', 'revisi']);

        if (!empty($mahasiswaIds)) {
            $logbookQuery->whereIn('user_id', $mahasiswaIds);
        }

        $logbooks = $logbookQuery->get()->map(function ($item) {
            return (object) [
                'id'               => 'logbook_' . $item->id,
                'user'             => $item->user,
                'jenis_laporan'    => 'Logbook Harian',
                'tanggal'          => $item->tanggal,
                'uraian'           => $item->uraian_kegiatan,
                'catatan'          => $item->catatan_dosen,
                'status_verifikasi'=> $item->status_asistensi, // approved / revisi
                'foto'             => $item->foto_dokumentasi,
                'tipe_data'        => 'logbook',
            ];
        });

        // --- QUERY 2: ABSENSI & IZIN TERVERIFIKASI ---
        $absensiQuery = Absensi::with(['user.mahasiswaProfile'])
            ->where('status_verifikasi', '!=', 'pending');

        if (!empty($mahasiswaIds)) {
            $absensiQuery->whereIn('user_id', $mahasiswaIds);
        }

        $absensis = $absensiQuery->get()->map(function ($item) {
            $jenis = 'Presensi Hadir';
            if ($item->tipe_kehadiran === 'sakit') {
                $jenis = 'Izin Sakit';
            } elseif ($item->tipe_kehadiran === 'izin') {
                $jenis = 'Izin Tidak Hadir';
            }

            return (object) [
                'id'               => 'absensi_' . $item->id,
                'user'             => $item->user,
                'jenis_laporan'    => $jenis,
                'tanggal'          => $item->tanggal,
                'uraian'           => $item->alasan_izin ?: "Presensi Masuk: {$item->waktu_masuk} | Pulang: " . ($item->waktu_pulang ?: '-'),
                'catatan'          => null,
                'status_verifikasi'=> $item->status_verifikasi, // approved / rejected
                'foto'             => $item->surat_izin ?: $item->foto_masuk,
                'tipe_data'        => 'absensi',
            ];
        });

        // --- COMBINE & SORT DATA ---
        $allCollection = $logbooks->concat($absensis)->sortByDesc('tanggal');

        // Filter Search (Nama / NIM)
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $allCollection = $allCollection->filter(function ($item) use ($search) {
                $name = strtolower($item->user->name ?? '');
                $nim = strtolower($item->user->mahasiswaProfile?->nim ?? '');
                return str_contains($name, $search) || str_contains($nim, $search);
            });
        }

        // Filter Jenis Laporan
        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $jenis = $request->jenis;
            $allCollection = $allCollection->filter(function ($item) use ($jenis) {
                if ($jenis === 'logbook') return $item->tipe_data === 'logbook';
                if ($jenis === 'izin') return in_array($item->jenis_laporan, ['Izin Sakit', 'Izin Tidak Hadir']);
                if ($jenis === 'presensi') return $item->jenis_laporan === 'Presensi Hadir';
                return true;
            });
        }

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentPageItems = $allCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $riwayats = new LengthAwarePaginator(
            $currentPageItems,
            $allCollection->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('dashboard.terverifikasi.index', compact('riwayats', 'currentUser'));
    }
}