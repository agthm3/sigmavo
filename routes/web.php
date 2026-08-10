<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AjukanMandiriController;
use App\Http\Controllers\AktivasiUserController;
use App\Http\Controllers\DaftarLowonganController;
use App\Http\Controllers\DaftarMahasiswaController;
use App\Http\Controllers\DaftarPerusahaanController;
use App\Http\Controllers\DashboardAnalitik;
use App\Http\Controllers\DashboardMahasiswaAkunController;
use App\Http\Controllers\DownloadTemplateController;
use App\Http\Controllers\ErrorHandlingController;
use App\Http\Controllers\JenisRoleController;
use App\Http\Controllers\LaporanAkhirMahasiswaController;
use App\Http\Controllers\ListingProgramController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MahasiswaBimbinganController;
use App\Http\Controllers\PembekalanMagangController;
use App\Http\Controllers\PengajuanMagangController;
use App\Http\Controllers\PengaturanGlobalController;
use App\Http\Controllers\PenilaianListingMahasiswaController;
use App\Http\Controllers\PerluVerifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramMagangController;
use App\Http\Controllers\RiwayatMagangController;
use App\Http\Controllers\RubrikPenilaianController;
use App\Http\Controllers\SeleksiController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\SemuaLaporanController;
use App\Http\Controllers\StatusPengajuanController;
use App\Http\Controllers\TerverifikasiController;
use App\Http\Controllers\UploadDokumenController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES & AUTH
// ==========================================
require __DIR__.'/auth.php';

// Route Bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ==========================================
// 2. DASHBOARD & SYSTEM (Membutuhkan Login)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Utama Analitik
    Route::get('/', [DashboardAnalitik::class, 'index'])->name('dashboard-analitik');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ==========================================
    // 3. FITUR MAHASISWA (Role: mahasiswa)
    // ==========================================
    Route::middleware(['role:mahasiswa'])->group(function () {
        
        // Akun & Profil Mahasiswa
        Route::prefix('dashboard/mahasiswa/akun')->group(function () {
            Route::get('/', [DashboardMahasiswaAkunController::class, 'index'])->name('dashboard-mahasiswa-akun');
            Route::post('/update-profile', [DashboardMahasiswaAkunController::class, 'updateProfile'])->name('dashboard-mahasiswa-akun-update-profile');
            Route::post('/update-password', [DashboardMahasiswaAkunController::class, 'updatePassword'])->name('dashboard-mahasiswa-akun-update-password');
        });

        // Riwayat & Program Magang
        Route::prefix('dashboard-mahasiswa')->group(function () {
            Route::get('/riwayat-magang', [RiwayatMagangController::class, 'index'])->name('dashboard-mahasiswa-riwayat-magang');
            Route::get('/program-magang', [ProgramMagangController::class, 'index'])->name('dashboard-mahasiswa-program-magang');
            Route::post('/program-magang/izin', [ProgramMagangController::class, 'ajukanIzin'])->name('dashboard-mahasiswa-program-magang-izin');
        });

        // Pengajuan Magang (Mahasiswa)
        Route::prefix('pengajuan-magang')->group(function () {
            Route::get('/daftar-lowongan', [DaftarLowonganController::class, 'index'])->name('dashboard-mahasiswa-daftar-lowongan');
            Route::post('/daftar-lowongan/{id}/lamar', [DaftarLowonganController::class, 'lamar'])->name('dashboard-mahasiswa-daftar-lowongan-lamar');
            
            Route::get('/ajukan-mandiri', [AjukanMandiriController::class, 'index'])->name('dashboard-mahasiswa-ajukan-mandiri');
            
            Route::get('/status-pengajuan', [StatusPengajuanController::class, 'index'])->name('dashboard-mahasiswa-status-pengajuan');
            Route::delete('/status-pengajuan/{id}/cancel', [StatusPengajuanController::class, 'cancel'])->name('dashboard-mahasiswa-status-pengajuan-cancel');
            Route::get('/status-pengajuan/{id}/download-surat', [StatusPengajuanController::class, 'downloadSurat'])->name('dashboard-mahasiswa-status-pengajuan-download-surat');
            Route::post('/status-pengajuan/{id}/upload-surat-balasan', [StatusPengajuanController::class, 'uploadSuratBalasan'])->name('dashboard-mahasiswa-status-pengajuan-upload-surat-balasan');
        });

        // Pelaksanaan Magang (Mahasiswa)
        Route::prefix('pelaksanaan-magang')->group(function () {
            Route::post('/pembekalan-magang', [PembekalanMagangController::class, 'store'])->name('dashboard-mahasiswa-pembekalan-magang-store');
            Route::post('/pembekalan-magang/{id}/presensi', [PembekalanMagangController::class, 'presensi'])->name('dashboard-mahasiswa-pembekalan-magang-presensi');
            Route::post('/pembekalan-magang/{id}/materi', [PembekalanMagangController::class, 'storeMateri'])->name('dashboard-mahasiswa-pembekalan-magang-materi-store');

            Route::post('/absensi', [AbsensiController::class, 'storeAbsensi'])->name('dashboard-mahasiswa-absensi-store');
            Route::post('/absensi/izin', [AbsensiController::class, 'storeIzin'])->name('dashboard-mahasiswa-absensi-izin-store');

            Route::post('/logbook', [LogbookController::class, 'store'])->name('dashboard-mahasiswa-logbook-store');
            Route::put('/logbook/{id}', [LogbookController::class, 'update'])->name('dashboard-mahasiswa-logbook-update');
            Route::delete('/logbook/{id}', [LogbookController::class, 'destroy'])->name('dashboard-mahasiswa-logbook-destroy');
            Route::get('/logbook/export-word', [LogbookController::class, 'exportWord'])->name('dashboard-mahasiswa-logbook-export-word'); // Note: Perbaikan path agar konsisten

            Route::post('/seminar-hasil', [SeminarController::class, 'storeOrUpdate'])->name('dashboard-mahasiswa-seminar-store');
            Route::delete('/seminar-hasil/ppt', [SeminarController::class, 'destroyPpt'])->name('dashboard-mahasiswa-seminar-destroy-ppt');
            
            Route::post('/laporan-akhir', [LaporanAkhirMahasiswaController::class, 'store'])->name('dashboard-mahasiswa-laporan-akhir-store');
        });
    });

    // ==========================================
    // 4. AKSES GABUNGAN (Mahasiswa, Dosen, SPV, Admin)
    // ==========================================
    Route::prefix('pelaksanaan-magang')->group(function () {
        Route::get('/pembekalan-magang', [PembekalanMagangController::class, 'index'])->name('dashboard-mahasiswa-pembekalan-magang');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('dashboard-mahasiswa-absensi');
        Route::get('/logbook', [LogbookController::class, 'index'])->name('dashboard-mahasiswa-logbook');
        Route::get('/seminar-hasil', [SeminarController::class, 'index'])->name('dashboard-mahasiswa-seminar');
        Route::get('/laporan-akhir', [LaporanAkhirMahasiswaController::class, 'index'])->name('dashboard-mahasiswa-laporan-akhir');
    });

    // Download & Upload Dokumen Umum
    Route::get('/download-template', [DownloadTemplateController::class, 'index'])->name('dashboard-pelaporan-download-template');
    Route::get('/upload-dokumen', [UploadDokumenController::class, 'index'])->name('dashboard-pelaporan-upload-dokumen');
    Route::post('/upload-dokumen', [UploadDokumenController::class, 'store'])->name('dashboard-pelaporan-upload-dokumen-store');
    Route::delete('/upload-dokumen/{id}', [UploadDokumenController::class, 'destroy'])->name('dashboard-pelaporan-upload-dokumen-destroy');

    // ==========================================
    // 5. FITUR DOSEN & SPV MITRA (Verifikasi & Penilaian)
    // ==========================================
    Route::middleware(['role:dosen|spv|admin_prodi|admin|superadmin'])->group(function () {
        
        Route::prefix('dashboard-dosen')->group(function () {
            Route::get('/mahasiswa-bimbingan', [MahasiswaBimbinganController::class, 'index'])->name('dashboard-dosen-mahasiswa-bimbingan');
            Route::get('/daftar-mahasiswa', [DaftarMahasiswaController::class, 'index'])->name('dashboard-dosen-daftar-mahasiswa');
        });

        Route::prefix('verifikasi')->group(function () {
            Route::get('/daftar-mahasiswa/perlu-verifikasi', [PerluVerifikasiController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi');
            Route::get('/daftar-mahasiswa-terverifikasi', [TerverifikasiController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-terverifikasi');
            Route::get('/daftar-mahasiswa-semua-laporan', [SemuaLaporanController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-semua-laporan');
            
            Route::put('/daftar-mahasiswa-semua-laporan/{id}', [SemuaLaporanController::class, 'updateStatus'])->name('dashboard-verifikasi-daftar-mahasiswa-semua-laporan-update');
            Route::post('/logbook/{id}', [PerluVerifikasiController::class, 'verifyLogbook'])->name('dashboard-verifikasi-logbook-action');
            Route::post('/absensi/{id}', [PerluVerifikasiController::class, 'verifyAbsensi'])->name('dashboard-verifikasi-absensi-action');
        });

        // Duplikat path yang ada di original route (Dosen view)
        Route::get('/pelaksanaan-magang/perlu-verifikasi', [PerluVerifikasiController::class, 'index'])->name('dashboard-dosen-perlu-verifikasi');
        Route::get('/pelaksanaan-magang/terverifikasi', [TerverifikasiController::class, 'index'])->name('dashboard-dosen-terverifikasi');
        Route::patch('/pelaksanaan-magang/pembekalan-magang/{pembekalanId}/manual/{userId}', [PembekalanMagangController::class, 'togglePresensiManual'])->name('dashboard-mahasiswa-pembekalan-magang-manual');
        
        // Penilaian
        Route::prefix('penilaian')->group(function () {
            Route::get('/listing-mahasiswa', [PenilaianListingMahasiswaController::class, 'index'])->name('dashboard-penilaian-listing-mahasiswa');
            Route::post('/listing-mahasiswa/{pendaftaran_id}/store', [PenilaianListingMahasiswaController::class, 'storePenilaian'])->name('dashboard-penilaian-listing-mahasiswa-store');
            Route::get('/listing-mahasiswa/export-pdf', [PenilaianListingMahasiswaController::class, 'exportPdf'])->name('dashboard-penilaian-listing-mahasiswa-export');
        });
    });


    // ==========================================
    // 6. FITUR ADMIN (Admin Prodi, Admin, Superadmin)
    // ==========================================
    Route::middleware(['role:admin_prodi|admin|superadmin'])->group(function () {
        
        // Master Lowongan & Perusahaan
        Route::prefix('daftar-lowongan')->group(function () {
            // Perusahaan
            Route::get('/daftar-perusahaan', [DaftarPerusahaanController::class, 'index'])->name('dashboard-daftar-lowongan-daftar-perusahaan');
            Route::post('/daftar-perusahaan', [DaftarPerusahaanController::class, 'store'])->name('dashboard-daftar-lowongan-daftar-perusahaan-store');
            Route::put('/daftar-perusahaan/{id}', [DaftarPerusahaanController::class, 'update'])->name('dashboard-daftar-lowongan-daftar-perusahaan-update');
            Route::delete('/daftar-perusahaan/{id}', [DaftarPerusahaanController::class, 'destroy'])->name('dashboard-daftar-lowongan-daftar-perusahaan-destroy');
            
            // Program
            Route::get('/listing-program', [ListingProgramController::class, 'index'])->name('dashboard-daftar-lowongan-listing-program');
            Route::post('/listing-program', [ListingProgramController::class, 'store'])->name('dashboard-daftar-lowongan-listing-program-store');
            Route::put('/listing-program/{id}', [ListingProgramController::class, 'update'])->name('dashboard-daftar-lowongan-listing-program-update');
            Route::delete('/listing-program/{id}', [ListingProgramController::class, 'destroy'])->name('dashboard-daftar-lowongan-listing-program-destroy');
            Route::patch('/listing-program/{id}/toggle', [ListingProgramController::class, 'togglePublish'])->name('dashboard-daftar-lowongan-listing-program-toggle');
            
            // Seleksi & Pengajuan
            Route::get('/seleksi', [SeleksiController::class, 'index'])->name('dashboard-daftar-lowongan-seleksi');
            Route::put('/seleksi/{id}', [SeleksiController::class, 'updateStatus'])->name('dashboard-daftar-lowongan-seleksi-update');
            
            Route::get('/pengajuan-magang', [PengajuanMagangController::class, 'index'])->name('dashboard-daftar-lowongan-pengajuan-magang');
            Route::put('/pengajuan-magang/{id}/terbit-surat', [PengajuanMagangController::class, 'terbitSurat'])->name('dashboard-daftar-lowongan-pengajuan-magang-terbit');
        });

        // Verifikasi Surat Balasan Mahasiswa
        Route::put('/pengajuan-magang/{id}/verifikasi-balasan', [PengajuanMagangController::class, 'verifikasiSuratBalasan'])->name('dashboard-daftar-lowongan-pengajuan-magang-verifikasi-balasan');
        
        // Penjadwalan Seminar Admin
        Route::post('/pelaksanaan-magang/seminar-hasil/admin-set/{userId}', [SeminarController::class, 'setJadwalAdmin'])->name('dashboard-admin-seminar-set');

        // Manajemen Akun Induk
        Route::prefix('manajemen-akun')->group(function () {
            
            // Aktivasi User & Import Massal
            Route::get('/aktivasi-user', [AktivasiUserController::class, 'index'])->name('dashboard-manajemen-aktivasi-user');
            Route::post('/aktivasi-user', [AktivasiUserController::class, 'store'])->name('dashboard-manajemen-aktivasi-store');
            Route::patch('/aktivasi-user/{id}/toggle', [AktivasiUserController::class, 'toggleStatus'])->name('dashboard-manajemen-aktivasi-toggle');
            
            Route::get('/aktivasi-user/template-excel', [AktivasiUserController::class, 'downloadTemplateExcel'])->name('dashboard-manajemen-aktivasi-template');
            Route::post('/aktivasi-user/import-preview', [AktivasiUserController::class, 'previewImport'])->name('dashboard-manajemen-aktivasi-import-preview');
            Route::post('/aktivasi-user/import-store', [AktivasiUserController::class, 'storeImport'])->name('dashboard-manajemen-aktivasi-import-store');
            
            // Pengaturan Global
            Route::get('/pengaturan', [PengaturanGlobalController::class, 'index'])->name('dashboard-manajemen-pengaturan');
            Route::put('/pengaturan/settings', [PengaturanGlobalController::class, 'updateSettings'])->name('dashboard-manajemen-pengaturan-settings-update');
            
            // Rubrik Penilaian
            Route::get('/rubrik-penilaian', [RubrikPenilaianController::class, 'index'])->name('dashboard-manajemen-rubrik-penilaian');
            Route::post('/rubrik-penilaian', [RubrikPenilaianController::class, 'store'])->name('dashboard-manajemen-rubrik-penilaian-store');
            Route::put('/rubrik-penilaian/{id}', [RubrikPenilaianController::class, 'update'])->name('dashboard-manajemen-rubrik-penilaian-update');
            Route::delete('/rubrik-penilaian/{id}', [RubrikPenilaianController::class, 'destroy'])->name('dashboard-manajemen-rubrik-penilaian-destroy');
            
            // Pengaturan CPMK (Diakses oleh admin prodi juga)
            Route::post('/pengaturan/cpmk', [PengaturanGlobalController::class, 'storeCpmk'])->name('dashboard-manajemen-pengaturan-cpmk-store');
            Route::put('/pengaturan/cpmk/{id}', [PengaturanGlobalController::class, 'updateCpmk'])->name('dashboard-manajemen-pengaturan-cpmk-update');
            Route::delete('/pengaturan/cpmk/{id}', [PengaturanGlobalController::class, 'destroyCpmk'])->name('dashboard-manajemen-pengaturan-cpmk-destroy');
        });

    });

    // ==========================================
    // 7. FITUR SUPER ADMIN & ADMIN (STRICT)
    // ==========================================
    Route::middleware(['role:admin|superadmin'])->prefix('manajemen-akun')->group(function () {
        
        // Kelola Master Role
        Route::get('/jenis-role', [JenisRoleController::class, 'index'])->name('dashboard-manajemen-jenis-role');
        Route::post('/jenis-role', [JenisRoleController::class, 'store'])->name('dashboard-manajemen-jenis-role-store');
        Route::put('/jenis-role/{id}/permissions', [JenisRoleController::class, 'updatePermissions'])->name('dashboard-manajemen-jenis-role-permissions');
        Route::delete('/jenis-role/{id}', [JenisRoleController::class, 'destroy'])->name('dashboard-manajemen-jenis-role-destroy');
        
        // Kelola Error Handling
        Route::get('/error-handling', [ErrorHandlingController::class, 'index'])->name('dashboard-manajemen-error-handling');
        Route::post('/error-handling/toggle', [ErrorHandlingController::class, 'updateToggle'])->name('dashboard-manajemen-error-handling-toggle');

        // CRUD Prodi (Hanya Superadmin & Admin)
        Route::post('/pengaturan/prodi', [PengaturanGlobalController::class, 'storeProdi'])->name('dashboard-manajemen-pengaturan-prodi-store');
        Route::put('/pengaturan/prodi/{id}', [PengaturanGlobalController::class, 'updateProdi'])->name('dashboard-manajemen-pengaturan-prodi-update');
        Route::delete('/pengaturan/prodi/{id}', [PengaturanGlobalController::class, 'destroyProdi'])->name('dashboard-manajemen-pengaturan-prodi-destroy');
    });

});