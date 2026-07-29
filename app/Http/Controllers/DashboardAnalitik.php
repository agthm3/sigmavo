<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Logbook;
use App\Models\MataKuliah;
use App\Models\Pendaftaran;
use App\Models\Perusahaan;
use App\Models\Prodi;
use App\Models\Seminar;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardAnalitik extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Target jam standar dari setting
        $targetJam = (int) Setting::getByKey('min_jam_magang', 900);

        // 1. STATISTIK UTAMA (METRICS)
        $queryMahasiswa = User::role('mahasiswa');
        $queryPendaftaran = Pendaftaran::where('status_seleksi', 'diterima');

        // Filter Scope jika Admin Prodi
        if ($user->hasRole('admin_prodi') && $user->adminProdiProfile?->prodi_id) {
            $prodiId = $user->adminProdiProfile->prodi_id;
            $queryMahasiswa->whereHas('mahasiswaProfile', fn($q) => $q->where('prodi_id', $prodiId));
            $queryPendaftaran->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $prodiId));
        }

        $totalMahasiswa = $queryMahasiswa->count();
        $totalPerusahaan = Perusahaan::count();
        $totalPendaftaranAktif = $queryPendaftaran->count();

        // Total Jam Magang Terakumulasi dari Absensi Approved
        $queryAbsensiApproved = Absensi::where('status_verifikasi', 'approved');
        if ($user->hasRole('admin_prodi') && isset($prodiId)) {
            $queryAbsensiApproved->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $prodiId));
        }
        $totalJamTerakumulasi = $queryAbsensiApproved->sum('jam_diperoleh');

        // Rata-rata Pemenuhan Jam Magang Mahasiswa (%)
        $rataRataJam = $totalMahasiswa > 0 ? round(($totalJamTerakumulasi / ($totalMahasiswa * $targetJam)) * 100, 1) : 0;
        $rataRataJam = min(100, $rataRataJam);

        // Total Antrean Pending (Logbook + Absensi)
        $queryPendingLogbook = Logbook::where('status_asistensi', 'pending');
        $queryPendingAbsensi = Absensi::where('status_verifikasi', 'pending');

        if ($user->hasRole('dosen')) {
            $queryPendingLogbook->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));
            $queryPendingAbsensi->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));
        }

        $totalPendingLogbook = $queryPendingLogbook->count();
        $totalPendingAbsensi = $queryPendingAbsensi->count();
        $totalPending = $totalPendingLogbook + $totalPendingAbsensi;

        // Total Seminar Selesai
        $totalSeminarSelesai = Seminar::where('status_seminar', 'selesai')->count();

        // 2. DATA CHART 1: PERSEBARAN MAHASISWA PER PRODI
        $prodiStats = Prodi::withCount(['mahasiswaProfiles as total_mhs'])
            ->get()
            ->map(function ($p) {
                return [
                    'nama' => $p->nama_prodi,
                    'total' => $p->total_mhs ?? 0
                ];
            });

        $chartProdiLabels = $prodiStats->pluck('nama')->toArray();
        $chartProdiData   = $prodiStats->pluck('total')->toArray();

        // 3. DATA CHART 2: PERSEBARAN MITRA PER SEKTOR INDUSTRI
        $industriStats = Perusahaan::select('sektor_industri', DB::raw('count(*) as total'))
            ->groupBy('sektor_industri')
            ->get();

        $chartIndustriLabels = $industriStats->pluck('sektor_industri')->map(fn($item) => $item ?? 'Lainnya')->toArray();
        $chartIndustriData   = $industriStats->pluck('total')->toArray();

        // 4. ANTREAN VERIFIKASI TERBARU (5 ITEM RECENT)
        $recentPendingLogbooks = $queryPendingLogbook->with(['user.mahasiswaProfile.prodi', 'pendaftaran.perusahaan'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.dashboard-analitik.index', compact(
            'user',
            'targetJam',
            'totalMahasiswa',
            'totalPerusahaan',
            'totalPendaftaranAktif',
            'totalJamTerakumulasi',
            'rataRataJam',
            'totalPending',
            'totalPendingLogbook',
            'totalPendingAbsensi',
            'totalSeminarSelesai',
            'chartProdiLabels',
            'chartProdiData',
            'chartIndustriLabels',
            'chartIndustriData',
            'recentPendingLogbooks'
        ));
    }
}