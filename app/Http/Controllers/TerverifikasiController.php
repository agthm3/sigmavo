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
            $mahasiswaIds = \App\Models\Pendaftaran::where('dosen_id', $currentUser->id)->pluck('user_id')->toArray();
            
            if (empty($mahasiswaIds)) {
                $prodiId = $currentUser->dosenProfile?->prodi_id;
                if ($prodiId) {
                    $mahasiswaIds = \App\Models\MahasiswaProfile::where('prodi_id', $prodiId)->pluck('user_id')->toArray();
                }
            }
        }

        // --- QUERY 1: LOGBOOK TERVERIFIKASI ---
        $logbookQuery = Logbook::with(['user.mahasiswaProfile.prodi', 'pendaftaran.lowongan.perusahaan'])
            ->whereIn('status_asistensi', ['approved', 'revisi']);

        if (!empty($mahasiswaIds)) {
            $logbookQuery->whereIn('user_id', $mahasiswaIds);
        }

        $logbooks = $logbookQuery->get()->map(function ($item) {
            $namaPerusahaan = $item->pendaftaran?->lowongan?->perusahaan?->nama_perusahaan 
                ?? $item->pendaftaran?->nama_instansi_mandiri 
                ?? '-';

            return (object) [
                'id'                => 'logbook_' . $item->id,
                'user'              => $item->user,
                'nama_perusahaan'   => $namaPerusahaan,
                'jenis_laporan'     => 'Logbook Harian',
                'tanggal'           => $item->tanggal,
                'uraian'            => $item->uraian_kegiatan,
                'catatan'           => $item->catatan_dosen,
                'status_verifikasi' => $item->status_asistensi,
                'foto'              => $item->foto_dokumentasi,
                'foto_pulang'       => null,
                'tipe_data'         => 'logbook',
                'waktu_masuk'       => null,
                'waktu_pulang'      => null,
                'latitude_masuk'    => null,
                'longitude_masuk'   => null,
                'latitude_pulang'   => null,
                'longitude_pulang'  => null,
            ];
        });

        // --- QUERY 2: ABSENSI & IZIN TERVERIFIKASI ---
        $absensiQuery = Absensi::with(['user.mahasiswaProfile.prodi', 'pendaftaran.lowongan.perusahaan'])
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

            $namaPerusahaan = $item->pendaftaran?->lowongan?->perusahaan?->nama_perusahaan 
                ?? $item->pendaftaran?->nama_instansi_mandiri 
                ?? '-';

            return (object) [
                'id'                => 'absensi_' . $item->id,
                'user'              => $item->user,
                'nama_perusahaan'   => $namaPerusahaan,
                'jenis_laporan'     => $jenis,
                'tanggal'           => $item->tanggal,
                'uraian'            => $item->alasan_izin ?: "Presensi Kehadiran Rutin Magang",
                'catatan'           => null,
                'status_verifikasi' => $item->status_verifikasi,
                'foto'              => $item->surat_izin ?: $item->foto_masuk,
                'foto_pulang'       => $item->foto_pulang,
                'tipe_data'         => 'absensi',
                'waktu_masuk'       => $item->waktu_masuk,
                'waktu_pulang'      => $item->waktu_pulang,
                'latitude_masuk'    => $item->latitude_masuk,
                'longitude_masuk'   => $item->longitude_masuk,
                'latitude_pulang'   => $item->latitude_pulang,
                'longitude_pulang'  => $item->longitude_pulang,
            ];
        });

        // --- COMBINE & SORT DATA ---
        $allCollection = $logbooks->concat($absensis)->sortByDesc('tanggal');

        // Filter Search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $allCollection = $allCollection->filter(function ($item) use ($search) {
                $name = strtolower($item->user->name ?? '');
                $nim = strtolower($item->user->mahasiswaProfile?->nim ?? '');
                return str_contains($name, $search) || str_contains($nim, $search);
            });
        }

        // Filter Jenis
        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $jenis = $request->jenis;
            $allCollection = $allCollection->filter(function ($item) use ($jenis) {
                if ($jenis === 'logbook') return $item->tipe_data === 'logbook';
                if ($jenis === 'izin') return in_array($item->jenis_laporan, ['Izin Sakit', 'Izin Tidak Hadir']);
                if ($jenis === 'presensi') return $item->jenis_laporan === 'Presensi Hadir';
                return true;
            });
        }

        // Pagination
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