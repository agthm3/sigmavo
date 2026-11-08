<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cpmk;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Absensi;
use App\Models\Setting;
use App\Models\Pembekalan;
use App\Models\PembekalanPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil pendaftaran magang aktif mahasiswa (status 'diterima' atau 'selesai')
        $pendaftaran = Pendaftaran::with(['lowongan.perusahaan', 'lowongan.spv', 'dosen'])
            ->where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        // 2. Cek Kehadiran Pembekalan
        $latestPembekalan = Pembekalan::latest()->first();
        $sudahPembekalan = true;

        if ($latestPembekalan) {
            $cekPresensi = PembekalanPresensi::where('pembekalan_id', $latestPembekalan->id)
                ->where('user_id', $user->id)
                ->where('is_hadir', true)
                ->exists();
            
            if (!$cekPresensi) {
                $sudahPembekalan = false;
            }
        }

        // JIKA BELUM DITERIMA MAGANG ATAU BELUM IKUT PEMBEKALAN -> Kunci Akses (isLocked = true)
        $isLocked = !$pendaftaran || !$sudahPembekalan;

        if ($isLocked) {
            return view('dashboard.logbook.index', [
                'isLocked'              => true,
                'sudahPembekalan'       => $sudahPembekalan, 
                'hasAbsenHariIni'       => false,
                'hasAbsenPulangHariIni' => false,
                'statusAbsensi'         => 'belum_absen_masuk',
                'jamTerlambat'          => 0,
                'logbooks'              => collect(),
                'pendaftaran'           => $pendaftaran,
                'daftarCpmk'            => [],
                'user'                  => $user,
                'totalLogbook'          => 0,
                'totalApproved'         => 0,
                'totalPending'          => 0,
                'totalRevisi'           => 0,
            ]);
        }

        // 3. Cek Absensi Hari Ini untuk Proteksi Logbook Reguler
        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();
        
        $hasAbsenHariIni = $absenHariIni ? true : false;
        $hasAbsenPulangHariIni = ($absenHariIni && !empty($absenHariIni->waktu_pulang)) ? true : false;

        // Klasifikasi status absensi untuk feedback UX
        $statusAbsensi = 'belum_absen_masuk';
        if ($absenHariIni && empty($absenHariIni->waktu_pulang)) {
            $statusAbsensi = 'belum_absen_pulang';
        } elseif ($hasAbsenPulangHariIni) {
            $statusAbsensi = 'lengkap';
        }

        $jamTerlambat = 0;

        // Jika belum absen masuk, hitung estimasi jam keterlambatan (asumsi masuk 08:00)
        if (!$hasAbsenHariIni) {
            $jamMulai = 8;
            $jamSekarang = (int) Carbon::now()->format('H');
            if ($jamSekarang > $jamMulai) {
                $jamTerlambat = $jamSekarang - $jamMulai;
            }
        }

        // 4. Hitung Statistik Ringkasan Logbook Mahasiswa (Untuk Verifikasi Cepat QA)
        $allUserLogs = Logbook::where('user_id', $user->id)->get();
        $totalLogbook = $allUserLogs->count();
        $totalApproved = $allUserLogs->filter(function ($item) {
            return $item->status_asistensi === 'approved' || ($item->status_spv === 'approved' && $item->status_dosen === 'approved');
        })->count();
        $totalRevisi = $allUserLogs->filter(function ($item) {
            return $item->status_asistensi === 'revisi' || $item->status_spv === 'revisi' || $item->status_dosen === 'revisi';
        })->count();
        $totalPending = $totalLogbook - $totalApproved - $totalRevisi;

        // 5. Query Logbook dengan Filter Bulan & Filter Status
        $query = Logbook::with(['pendaftaran.lowongan.perusahaan', 'pendaftaran.lowongan.spv', 'pendaftaran.dosen'])
            ->where('user_id', $user->id);

        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('tanggal', $parts[0])
                      ->whereMonth('tanggal', $parts[1]);
            }
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $statusFilter = $request->status;
            if ($statusFilter === 'approved') {
                $query->where(function ($q) {
                    $q->where('status_asistensi', 'approved')
                      ->orWhere(function ($sub) {
                          $sub->where('status_spv', 'approved')
                              ->where('status_dosen', 'approved');
                      });
                });
            } elseif ($statusFilter === 'revisi') {
                $query->where(function ($q) {
                    $q->where('status_asistensi', 'revisi')
                      ->orWhere('status_spv', 'revisi')
                      ->orWhere('status_dosen', 'revisi');
                });
            } elseif ($statusFilter === 'pending') {
                $query->where(function ($q) {
                    $q->where('status_asistensi', '!=', 'approved')
                      ->where('status_asistensi', '!=', 'revisi')
                      ->where(function ($sub) {
                          $sub->where('status_spv', '!=', 'approved')
                              ->orWhere('status_dosen', '!=', 'approved');
                      });
                });
            }
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->latest('id')->paginate(10)->withQueryString();

        // 6. AMBIL DATA CPMK BERDASARKAN PRODI MAHASISWA
        $mahasiswaProdiId = $user->mahasiswaProfile?->prodi_id;

        if ($mahasiswaProdiId) {
            $daftarCpmk = Cpmk::where('prodi_id', $mahasiswaProdiId)
                ->get()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        } else {
            $daftarCpmk = Cpmk::all()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        }

        if (empty($daftarCpmk)) {
            $daftarCpmk = [
                'CPMK-01 - Mampu menerapkan analisis kriteria proyek industri',
                'CPMK-02 - Mampu mempraktikkan etika profesi & keselamatan kerja K3',
                'CPMK-03 - Mampu mengimplementasikan teknologi rekayasa terapan',
                'CPMK-04 - Mampu menyusun laporan kerja dan manajemen tim'
            ];
        }

        return view('dashboard.logbook.index', [
            'isLocked'              => false,
            'sudahPembekalan'       => true,
            'hasAbsenHariIni'       => $hasAbsenHariIni,
            'hasAbsenPulangHariIni' => $hasAbsenPulangHariIni,
            'statusAbsensi'         => $statusAbsensi,
            'jamTerlambat'          => $jamTerlambat,
            'logbooks'              => $logbooks,
            'pendaftaran'           => $pendaftaran,
            'daftarCpmk'            => $daftarCpmk,
            'user'                  => $user,
            'totalLogbook'          => $totalLogbook,
            'totalApproved'         => $totalApproved,
            'totalPending'          => $totalPending,
            'totalRevisi'           => $totalRevisi,
        ]);
    }

    /**
     * Menyimpan entry logbook harian reguler
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima di program magang aktif.');
        }

        $latestPembekalan = Pembekalan::latest()->first();
        if ($latestPembekalan) {
            $cekPresensi = PembekalanPresensi::where('pembekalan_id', $latestPembekalan->id)
                ->where('user_id', $user->id)
                ->where('is_hadir', true)
                ->exists();
                
            if (!$cekPresensi) {
                return redirect()->back()->with('error', 'Akses Ditolak. Anda wajib melakukan konfirmasi kehadiran pada menu Pembekalan Magang terlebih dahulu.');
            }
        }

        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$absenHariIni || empty($absenHariIni->waktu_pulang)) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda wajib melakukan Absen Pulang hari ini terlebih dahulu sebelum dapat mengisi dan menyimpan Logbook harian.');
        }

        $request->validate([
            'uraian_kegiatan'  => 'required|string|min:10',
            'mata_kuliah'      => 'nullable|array',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('logbook_dokumentasi', 'public');
        }

        Logbook::create([
            'user_id'          => $user->id,
            'pendaftaran_id'   => $pendaftaran->id,
            'tanggal'          => now()->toDateString(),
            'uraian_kegiatan'  => $request->uraian_kegiatan,
            'mata_kuliah'      => $request->mata_kuliah ?? [],
            'foto_dokumentasi' => $fotoPath,
            'status_asistensi' => 'pending',
            'status_spv'       => 'pending',
            'status_dosen'     => 'pending',
            'is_susulan'       => false,
        ]);

        return redirect()->back()->with('success', 'Logbook harian berhasil disimpan beserta keterkaitan CPMK.');
    }

    /**
     * Memperbarui entri logbook
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima di program magang aktif.');
        }

        $logbook = Logbook::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($logbook->status_asistensi === 'approved') {
            return redirect()->back()->with('error', 'Logbook yang sudah disetujui tidak dapat diubah.');
        }

        $request->validate([
            'uraian_kegiatan'  => 'required|string|min:10',
            'mata_kuliah'      => 'nullable|array',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('foto_dokumentasi')) {
            if ($logbook->foto_dokumentasi && Storage::disk('public')->exists($logbook->foto_dokumentasi)) {
                Storage::disk('public')->delete($logbook->foto_dokumentasi);
            }
            $logbook->foto_dokumentasi = $request->file('foto_dokumentasi')->store('logbook_dokumentasi', 'public');
        }

        $logbook->uraian_kegiatan = $request->uraian_kegiatan;
        $logbook->mata_kuliah = $request->mata_kuliah ?? [];

        if ($logbook->status_asistensi === 'revisi') {
            $logbook->status_asistensi = 'pending';
            $logbook->status_spv       = 'pending';
            $logbook->status_dosen     = 'pending';
        }
        
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook harian berhasil diperbarui dan diserahkan kembali untuk diverifikasi.');
    }

    /**
     * Menghapus entri logbook
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima di program magang aktif.');
        }

        $logbook = Logbook::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($logbook->status_asistensi === 'approved') {
            return redirect()->back()->with('error', 'Logbook yang telah disetujui tidak dapat dihapus.');
        }

        if ($logbook->foto_dokumentasi && Storage::disk('public')->exists($logbook->foto_dokumentasi)) {
            Storage::disk('public')->delete($logbook->foto_dokumentasi);
        }

        $logbook->delete();

        return redirect()->back()->with('success', 'Entri logbook berhasil dihapus.');
    }

    /**
     * Export Logbook Pribadi Mahasiswa ke Dokumen Microsoft Word (.doc)
     */
    public function exportWord()
    {
        // Alokasikan memori dan waktu eksekusi ekstra untuk proses pembuatan dokumen besar
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima di program magang aktif.');
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $logbooks = Logbook::where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($logbooks->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data logbook untuk di-export.');
        }

        foreach ($logbooks as $logbook) {
            $logbook->foto_base64 = $this->getOptimizedBase64Image($logbook->foto_dokumentasi);
        }

        $htmlContent = view('dashboard.logbook.word-export', compact('user', 'pendaftaran', 'logbooks'))->render();
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name);
        $fileName = 'Logbook_Magang_' . $cleanName . '.doc';

        return response($htmlContent, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Expires'             => '0',
        ]);
    }

    /**
     * Optimasi Foto untuk Export Dokumen (Downsampling on-the-fly)
     * Mengurangi pemakaian memori dari 2-10 MB per foto menjadi ~20-40 KB per foto
     */
    private function getOptimizedBase64Image(?string $path, int $maxWidth = 500, int $quality = 70): ?string
    {
        if (empty($path)) {
            return null;
        }

        try {
            $fullPath = null;
            if (Storage::disk('public')->exists($path)) {
                $fullPath = Storage::disk('public')->path($path);
            } else {
                $localPath = storage_path('app/public/' . ltrim($path, '/'));
                if (file_exists($localPath) && !is_dir($localPath)) {
                    $fullPath = $localPath;
                }
            }

            if (!$fullPath || !file_exists($fullPath)) {
                return null;
            }

            // Downsampling menggunakan GD jika ekstensi tersedia
            if (extension_loaded('gd')) {
                $info = @getimagesize($fullPath);
                if ($info && in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'])) {
                    $width = $info[0];
                    $height = $info[1];
                    $mime = $info['mime'];

                    $src = null;
                    switch ($mime) {
                        case 'image/jpeg':
                            $src = @imagecreatefromjpeg($fullPath);
                            break;
                        case 'image/png':
                            $src = @imagecreatefrompng($fullPath);
                            break;
                        case 'image/webp':
                            $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null;
                            break;
                    }

                    if ($src) {
                        if ($width > $maxWidth) {
                            $newWidth = $maxWidth;
                            $newHeight = (int) round(($height * $maxWidth) / $width);
                            $dst = imagecreatetruecolor($newWidth, $newHeight);

                            if ($mime === 'image/png') {
                                imagealphablending($dst, false);
                                imagesavealpha($dst, true);
                            }

                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagedestroy($src);
                            $target = $dst;
                        } else {
                            $target = $src;
                        }

                        ob_start();
                        imagejpeg($target, null, $quality);
                        $compressedData = ob_get_clean();
                        imagedestroy($target);

                        if ($compressedData) {
                            return 'data:image/jpeg;base64,' . base64_encode($compressedData);
                        }
                    }
                }
            }

            // Fallback aman: jika GD tidak dapat memproses, baca file langsung
            $fileData = @file_get_contents($fullPath);
            if ($fileData) {
                $mimeType = @mime_content_type($fullPath) ?: 'image/jpeg';
                return 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal optimasi foto logbook untuk export: " . $e->getMessage());
        }

        return null;
    }
}