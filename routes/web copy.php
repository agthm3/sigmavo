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

// Route::get('/', function () {
//     return view('welcome');
// });
//DASHBOARD ANALITIK
Route::get('/', [DashboardAnalitik::class, 'index'])->name('dashboard-analitik');

// DASHBOARD MAHASISWA
// DASHBOARD MAHASISWA - AKUN & PROFIL
Route::get('/dashboard/mahasiswa/akun', [DashboardMahasiswaAkunController::class, 'index'])->name('dashboard-mahasiswa-akun');
Route::post('/dashboard/mahasiswa/akun/update-profile', [DashboardMahasiswaAkunController::class, 'updateProfile'])->name('dashboard-mahasiswa-akun-update-profile');
Route::post('/dashboard/mahasiswa/akun/update-password', [DashboardMahasiswaAkunController::class, 'updatePassword'])->name('dashboard-mahasiswa-akun-update-password');

    //RIWAYAT MAGANG
Route::get('/dashboard-mahasiswa/riwayat-magang', [RiwayatMagangController::class, 'index'])->name('dashboard-mahasiswa-riwayat-magang');

// DASHBOARD MAHASISWA
    // PROGRAM MAGANG
Route::get('/dashboard-mahasiswa/program-magang', [ProgramMagangController::class, 'index'])->name('dashboard-mahasiswa-program-magang');
Route::post('/dashboard-mahasiswa/program-magang/izin', [ProgramMagangController::class, 'ajukanIzin'])->name('dashboard-mahasiswa-program-magang-izin');

// PENGAJUAN MAGANG
    // DAFTAR LOWONGAN
Route::get('/pengajuan-magang/daftar-lowongan', [DaftarLowonganController::class, 'index'])->name('dashboard-mahasiswa-daftar-lowongan');
Route::post('/pengajuan-magang/daftar-lowongan/{id}/lamar', [DaftarLowonganController::class, 'lamar'])->name('dashboard-mahasiswa-daftar-lowongan-lamar');
    //AJUKAN MANDIRI
Route::get('/pengajuan-magang/ajukan-mandiri', [AjukanMandiriController::class, 'index'])->name('dashboard-mahasiswa-ajukan-mandiri');
// PENGAJUAN MAGANG
    // STATUS PENGAJUAN
Route::get('/pengajuan-magang/status-pengajuan', [StatusPengajuanController::class, 'index'])->name('dashboard-mahasiswa-status-pengajuan');
Route::delete('/pengajuan-magang/status-pengajuan/{id}/cancel', [StatusPengajuanController::class, 'cancel'])->name('dashboard-mahasiswa-status-pengajuan-cancel');
Route::get('/pengajuan-magang/status-pengajuan/{id}/download-surat', [StatusPengajuanController::class, 'downloadSurat'])->name('dashboard-mahasiswa-status-pengajuan-download-surat');
// MAHASISWA: Upload Surat Balasan Perusahaan
Route::post('/pengajuan-magang/status-pengajuan/{id}/upload-surat-balasan', [StatusPengajuanController::class, 'uploadSuratBalasan'])
    ->name('dashboard-mahasiswa-status-pengajuan-upload-surat-balasan');
// ADMIN: Verifikasi Surat Balasan & Keputusan Akhir Seleksi
Route::put('/pengajuan-magang/{id}/verifikasi-balasan', [PengajuanMagangController::class, 'verifikasiSuratBalasan'])
    ->name('dashboard-daftar-lowongan-pengajuan-magang-verifikasi-balasan');

//PELAKSANAAN MAGANG
    // PEMBEKALAN MAGANG
Route::get('/pelaksanaan-magang/pembekalan-magang', [PembekalanMagangController::class, 'index'])->name('dashboard-mahasiswa-pembekalan-magang');
Route::post('/pelaksanaan-magang/pembekalan-magang', [PembekalanMagangController::class, 'store'])->name('dashboard-mahasiswa-pembekalan-magang-store');
Route::post('/pelaksanaan-magang/pembekalan-magang/{id}/presensi', [PembekalanMagangController::class, 'presensi'])->name('dashboard-mahasiswa-pembekalan-magang-presensi');
Route::patch('/pelaksanaan-magang/pembekalan-magang/{pembekalanId}/manual/{userId}', [PembekalanMagangController::class, 'togglePresensiManual'])->name('dashboard-mahasiswa-pembekalan-magang-manual');
Route::post('/pelaksanaan-magang/pembekalan-magang/{id}/materi', [PembekalanMagangController::class, 'storeMateri'])->name('dashboard-mahasiswa-pembekalan-magang-materi-store');
// PELAKSANAAN MAGANG
    // ABSENSI
Route::get('/pelaksanaan-magang/absensi', [AbsensiController::class, 'index'])->name('dashboard-mahasiswa-absensi');
Route::post('/pelaksanaan-magang/absensi', [AbsensiController::class, 'storeAbsensi'])->name('dashboard-mahasiswa-absensi-store');
Route::post('/pelaksanaan-magang/absensi/izin', [AbsensiController::class, 'storeIzin'])->name('dashboard-mahasiswa-absensi-izin-store');
// PELAKSANAAN MAGANG
    // LOGBOOK
Route::get('/pelaksanaan-magang/logbook', [LogbookController::class, 'index'])->name('dashboard-mahasiswa-logbook');
Route::post('/pelaksanaan-magang/logbook', [LogbookController::class, 'store'])->name('dashboard-mahasiswa-logbook-store');
Route::put('/pelaksanaan-magang/logbook/{id}', [LogbookController::class, 'update'])->name('dashboard-mahasiswa-logbook-update');
Route::delete('/pelaksanaan-magang/logbook/{id}', [LogbookController::class, 'destroy'])->name('dashboard-mahasiswa-logbook-destroy');
Route::get('/mahasiswa/logbook/export-word', [LogbookController::class, 'exportWord'])->name('dashboard-mahasiswa-logbook-export-word');

// PELAKSANAAN MAGANG
    // SEMINAR HASIL
Route::get('/pelaksanaan-magang/seminar-hasil', [SeminarController::class, 'index'])->name('dashboard-mahasiswa-seminar');
Route::post('/pelaksanaan-magang/seminar-hasil', [SeminarController::class, 'storeOrUpdate'])->name('dashboard-mahasiswa-seminar-store');
Route::post('/pelaksanaan-magang/seminar-hasil/admin-set/{userId}', [SeminarController::class, 'setJadwalAdmin'])->name('dashboard-admin-seminar-set');
Route::delete('/pelaksanaan-magang/seminar-hasil/ppt', [SeminarController::class, 'destroyPpt'])->name('dashboard-mahasiswa-seminar-destroy-ppt');


//DASHBOARD DOSEN
    //PERLU VERIFIKASI
Route::get('/pelaksanaan-magang/perlu-verifikasi', [PerluVerifikasiController::class, 'index'])->name('dashboard-dosen-perlu-verifikasi');
    //TERVERIFIKASI
Route::get('/pelaksanaan-magang/terverifikasi', [TerverifikasiController::class, 'index'])->name('dashboard-dosen-terverifikasi');
    //Mahasiswa Bimbingan
Route::get('/dashboard-dosen/mahasiswa-bimbingan', [MahasiswaBimbinganController::class, 'index'])->name('dashboard-dosen-mahasiswa-bimbingan');
    //DAFTR MAHASISWA
Route::get('/dashboard-dosen/daftar-mahasiswa', [DaftarMahasiswaController::class, 'index'])->name('dashboard-dosen-daftar-mahasiswa');

//VERIFIKASI
    //DAFTAR MAHASISWA TERVERFIKASI
Route::get('/verifikasi/daftar-mahasiswa-terverifikasi', [TerverifikasiController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-terverifikasi');
// VERIFIKASI & DASHBOARD DOSEN
Route::get('/pelaksanaan-magang/perlu-verifikasi', [PerluVerifikasiController::class, 'index'])
    ->name('dashboard-dosen-perlu-verifikasi');

Route::get('/verifikasi/daftar-mahasiswa/perlu-verifikasi', [PerluVerifikasiController::class, 'index'])
    ->name('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi');

Route::get('/verifikasi/daftar-mahasiswa-terverifikasi', [TerverifikasiController::class, 'index'])
    ->name('dashboard-verifikasi-daftar-mahasiswa-terverifikasi');

Route::post('/verifikasi/logbook/{id}', [PerluVerifikasiController::class, 'verifyLogbook'])
    ->name('dashboard-verifikasi-logbook-action');

Route::post('/verifikasi/absensi/{id}', [PerluVerifikasiController::class, 'verifyAbsensi'])
    ->name('dashboard-verifikasi-absensi-action');

Route::post('/verifikasi/logbook/{id}', [PerluVerifikasiController::class, 'verifyLogbook'])->name('dashboard-verifikasi-logbook-action');
Route::post('/verifikasi/absensi/{id}', [PerluVerifikasiController::class, 'verifyAbsensi'])->name('dashboard-verifikasi-absensi-action');
    //DAFTAR MAHASISWA  SEMUA LAPORAN
Route::get('/verifikasi/daftar-mahasiswa-semua-laporan', [SemuaLaporanController::class, 'index'])
    ->name('dashboard-verifikasi-daftar-mahasiswa-semua-laporan');
Route::put('/verifikasi/daftar-mahasiswa-semua-laporan/{id}', [SemuaLaporanController::class, 'updateStatus'])
    ->name('dashboard-verifikasi-daftar-mahasiswa-semua-laporan-update');

    // LOGBOOK & LAPORAN AKHIR MAHASISWA
Route::get('/pelaksanaan-magang/laporan-akhir', [LaporanAkhirMahasiswaController::class, 'index'])
    ->name('dashboard-mahasiswa-laporan-akhir');
Route::post('/pelaksanaan-magang/laporan-akhir', [LaporanAkhirMahasiswaController::class, 'store'])
    ->name('dashboard-mahasiswa-laporan-akhir-store');

// PENILAIAN MAGANG
Route::get('/penilaian/listing-mahasiswa', [PenilaianListingMahasiswaController::class, 'index'])->name('dashboard-penilaian-listing-mahasiswa');
Route::post('/penilaian/listing-mahasiswa/{pendaftaran_id}/store', [PenilaianListingMahasiswaController::class, 'storePenilaian'])->name('dashboard-penilaian-listing-mahasiswa-store');
Route::get('/penilaian/listing-mahasiswa/export-pdf', [PenilaianListingMahasiswaController::class, 'exportPdf'])->name('dashboard-penilaian-listing-mahasiswa-export');

// DAFTAR LOWONGAN
    // DAFTAR PERUSAHAAN
Route::get('/daftar-lowongan/daftar-perusahaan', [DaftarPerusahaanController::class, 'index'])->name('dashboard-daftar-lowongan-daftar-perusahaan');
Route::post('/daftar-lowongan/daftar-perusahaan', [DaftarPerusahaanController::class, 'store'])->name('dashboard-daftar-lowongan-daftar-perusahaan-store');
Route::put('/daftar-lowongan/daftar-perusahaan/{id}', [DaftarPerusahaanController::class, 'update'])->name('dashboard-daftar-lowongan-daftar-perusahaan-update');
Route::delete('/daftar-lowongan/daftar-perusahaan/{id}', [DaftarPerusahaanController::class, 'destroy'])->name('dashboard-daftar-lowongan-daftar-perusahaan-destroy');
// DAFTAR LOWONGAN
    // LISTING PROGRAM
Route::get('/daftar-lowongan/listing-program', [ListingProgramController::class, 'index'])->name('dashboard-daftar-lowongan-listing-program');
Route::post('/daftar-lowongan/listing-program', [ListingProgramController::class, 'store'])->name('dashboard-daftar-lowongan-listing-program-store');
Route::put('/daftar-lowongan/listing-program/{id}', [ListingProgramController::class, 'update'])->name('dashboard-daftar-lowongan-listing-program-update');
Route::delete('/daftar-lowongan/listing-program/{id}', [ListingProgramController::class, 'destroy'])->name('dashboard-daftar-lowongan-listing-program-destroy');
Route::patch('/daftar-lowongan/listing-program/{id}/toggle', [ListingProgramController::class, 'togglePublish'])->name('dashboard-daftar-lowongan-listing-program-toggle');
// DAFTAR LOWONGAN
    // SELEKSI
Route::get('/daftar-lowongan/seleksi', [SeleksiController::class, 'index'])->name('dashboard-daftar-lowongan-seleksi');
Route::put('/daftar-lowongan/seleksi/{id}', [SeleksiController::class, 'updateStatus'])->name('dashboard-daftar-lowongan-seleksi-update');
// PENGAJUAN MAGANG
Route::get('/daftar-lowongan/pengajuan-magang', [PengajuanMagangController::class, 'index'])->name('dashboard-daftar-lowongan-pengajuan-magang');
Route::put('/daftar-lowongan/pengajuan-magang/{id}/terbit-surat', [PengajuanMagangController::class, 'terbitSurat'])->name('dashboard-daftar-lowongan-pengajuan-magang-terbit');

//DOWNLOAD TEMPLATE
Route::get('/download-template', [DownloadTemplateController::class, 'index'])->name('dashboard-pelaporan-download-template');
//UPLOAD DOKUMEN
Route::get('/upload-dokumen', [UploadDokumenController::class, 'index'])->name('dashboard-pelaporan-upload-dokumen');


// MANAJEMEN AKUN
Route::get('/manajemen-akun/aktivasi-user', [AktivasiUserController::class, 'index'])
        ->name('dashboard-manajemen-aktivasi-user');
Route::post('/manajemen-akun/aktivasi-user', [AktivasiUserController::class, 'store'])
        ->name('dashboard-manajemen-aktivasi-store');

Route::patch('/manajemen-akun/aktivasi-user/{id}/toggle', [AktivasiUserController::class, 'toggleStatus'])
        ->name('dashboard-manajemen-aktivasi-toggle');

Route::get('/manajemen-akun/aktivasi-user/template-excel', [AktivasiUserController::class, 'downloadTemplateExcel'])
    ->name('dashboard-manajemen-aktivasi-template');

Route::post('/manajemen-akun/aktivasi-user/import-preview', [AktivasiUserController::class, 'previewImport'])
    ->name('dashboard-manajemen-aktivasi-import-preview');

Route::post('/manajemen-akun/aktivasi-user/import-store', [AktivasiUserController::class, 'storeImport'])
    ->name('dashboard-manajemen-aktivasi-import-store');

// MANAJEMEN AKUN (Jenis User / Role)
    Route::get('/manajemen-akun/jenis-role', [JenisRoleController::class, 'index'])
        ->name('dashboard-manajemen-jenis-role');

    Route::post('/manajemen-akun/jenis-role', [JenisRoleController::class, 'store'])
        ->name('dashboard-manajemen-jenis-role-store');

    Route::put('/manajemen-akun/jenis-role/{id}/permissions', [JenisRoleController::class, 'updatePermissions'])
        ->name('dashboard-manajemen-jenis-role-permissions');

    Route::delete('/manajemen-akun/jenis-role/{id}', [JenisRoleController::class, 'destroy'])
        ->name('dashboard-manajemen-jenis-role-destroy');
        
// MANAJEMEN AKUN (Pengaturan Global)
    Route::get('/manajemen-akun/pengaturan', [PengaturanGlobalController::class, 'index'])
        ->name('dashboard-manajemen-pengaturan');

    Route::put('/manajemen-akun/pengaturan/settings', [PengaturanGlobalController::class, 'updateSettings'])
        ->name('dashboard-manajemen-pengaturan-settings-update');

    Route::post('/manajemen-akun/pengaturan/prodi', [PengaturanGlobalController::class, 'storeProdi'])
        ->name('dashboard-manajemen-pengaturan-prodi-store');

    Route::put('/manajemen-akun/pengaturan/prodi/{id}', [PengaturanGlobalController::class, 'updateProdi'])
        ->name('dashboard-manajemen-pengaturan-prodi-update');

    Route::delete('/manajemen-akun/pengaturan/prodi/{id}', [PengaturanGlobalController::class, 'destroyProdi'])
        ->name('dashboard-manajemen-pengaturan-prodi-destroy');


// ROUTE MASTER CPMK
Route::post('/manajemen-akun/pengaturan/cpmk', [PengaturanGlobalController::class, 'storeCpmk']) ->name('dashboard-manajemen-pengaturan-cpmk-store');
Route::put('/manajemen-akun/pengaturan/cpmk/{id}', [PengaturanGlobalController::class, 'updateCpmk'])->name('dashboard-manajemen-pengaturan-cpmk-update');
Route::delete('/manajemen-akun/pengaturan/cpmk/{id}', [PengaturanGlobalController::class, 'destroyCpmk'])->name('dashboard-manajemen-pengaturan-cpmk-destroy');

// MANAJEMEN AKUN (RUBRIK PENILAIAN GLOBAL)
Route::get('/manajemen-akun/rubrik-penilaian', [RubrikPenilaianController::class, 'index'])->name('dashboard-manajemen-rubrik-penilaian');
Route::post('/manajemen-akun/rubrik-penilaian', [RubrikPenilaianController::class, 'store'])->name('dashboard-manajemen-rubrik-penilaian-store');
Route::put('/manajemen-akun/rubrik-penilaian/{id}', [RubrikPenilaianController::class, 'update'])->name('dashboard-manajemen-rubrik-penilaian-update');
Route::delete('/manajemen-akun/rubrik-penilaian/{id}', [RubrikPenilaianController::class, 'destroy'])->name('dashboard-manajemen-rubrik-penilaian-destroy');

Route::prefix('manajemen-akun/error-handling')->group(function () {
        Route::get('/', [ErrorHandlingController::class, 'index'])->name('dashboard-manajemen-error-handling');
        Route::post('/toggle', [ErrorHandlingController::class, 'updateToggle'])->name('dashboard-manajemen-error-handling-toggle');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
