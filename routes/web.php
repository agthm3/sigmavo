<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AjukanMandiriController;
use App\Http\Controllers\DaftarLowonganController;
use App\Http\Controllers\DaftarMahasiswaController;
use App\Http\Controllers\DaftarPerusahaanController;
use App\Http\Controllers\DashboardAnalitik;
use App\Http\Controllers\DashboardMahasiswaAkunController;
use App\Http\Controllers\ListingProgramController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MahasiswaBimbinganController;
use App\Http\Controllers\PembekalanMagangController;
use App\Http\Controllers\PengajuanMagangController;
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


//DAFTAR LOWONGAN
    //DAFTAR PERUSAHAAN
Route::get('/daftar-lowongan/daftar-perusahaan', [DaftarPerusahaanController::class, 'index'])->name('dashboard-daftar-lowongan-daftar-perusahaan');
    //LISTING PROGRAM
Route::get('/daftar-lowongan/listing-program', [ListingProgramController::class, 'index'])->name('dashboard-daftar-lowongan-listing-program');
    //SELEKSI
Route::get('/daftar-lowongan/seleksi', [SeleksiController::class, 'index'])->name('dashboard-daftar-lowongan-seleksi');
    //PENGAJUAN MAGANG
Route::get('/daftar-lowongan/pengajuan-magang', [PengajuanMagangController::class, 'index'])->name('dashboard-daftar-lowongan-pengajuan-magang');   


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
