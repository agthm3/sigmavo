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
use App\Http\Controllers\JenisRoleController;
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
    //AKUN
Route::get('/dashboard-mahasiswa/akun', [DashboardMahasiswaAkunController::class, 'index'])->name('dashboard-mahasiswa-akun');

    //RIWAYAT MAGANG
Route::get('/dashboard-mahasiswa/riwayat-magang', [RiwayatMagangController::class, 'index'])->name('dashboard-mahasiswa-riwayat-magang');

    //PROGRAM MAGANG
Route::get('/dashboard-mahasiswa/program-magang', [ProgramMagangController::class, 'index'])->name('dashboard-mahasiswa-program-magang');


//PENGAJUAN MAGANG
    //DAFTAR LOWONGAN
Route::get('/pengajuan-magang/daftar-lowongan', [DaftarLowonganController::class, 'index'])->name('dashboard-mahasiswa-daftar-lowongan');
    //AJUKAN MANDIRI
Route::get('/pengajuan-magang/ajukan-mandiri', [AjukanMandiriController::class, 'index'])->name('dashboard-mahasiswa-ajukan-mandiri');

    //STATUS PENGAJUAN
Route::get('/pengajuan-magang/status-pengajuan', [StatusPengajuanController::class, 'index'])->name('dashboard-mahasiswa-status-pengajuan');


//PELAKSANAAN MAGANG
    //PEMBEKALAN MAGANG
Route::get('/pelaksanaan-magang/pembekalan-magang', [PembekalanMagangController::class, 'index'])->name('dashboard-mahasiswa-pembekalan-magang');
    //ABSENSI
Route::get('/pelaksanaan-magang/absensi', [AbsensiController::class, 'index'])->name('dashboard-mahasiswa-absensi');
    //LOGBOOK
Route::get('/pelaksanaan-magang/logbook', [LogbookController::class, 'index'])->name('dashboard-mahasiswa-logbook');
    //SEMINAR
Route::get('/pelaksanaan-magang/seminar', [SeminarController::class, 'index'])->name('dashboard-mahasiswa-seminar');

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
    //DAFTAR MAHASISWA PERLU VERIFIKASI
Route::get('/verifikasi/daftar-mahasiswa-perlu-verifikasi', [PerluVerifikasiController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi');
    //DAFTAR MAHASISWA  SEMUA LAPORAN
Route::get('/verifikasi/daftar-mahasiswa-semua-laporan', [SemuaLaporanController::class, 'index'])->name('dashboard-verifikasi-daftar-mahasiswa-semua-laporan');


//PENILAIAN
    //LISTING MAHASISWA
Route::get('/penilaian/listing-mahasiswa', [PenilaianListingMahasiswaController::class, 'index'])->name('dashboard-penilaian-listing-mahasiswa');


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
    //PENGAJUAN MAGANG
Route::get('/daftar-lowongan/pengajuan-magang', [PengajuanMagangController::class, 'index'])->name('dashboard-daftar-lowongan-pengajuan-magang');   


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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
