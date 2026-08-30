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
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
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
                'isLocked'        => true,
                'sudahPembekalan' => $sudahPembekalan, 
                'hasAbsenHariIni' => false,
                'jamTerlambat'    => 0,
                'logbooks'        => collect(),
                'pendaftaran'     => $pendaftaran,
                'daftarCpmk'      => [],
                'user'            => $user
            ]);
        }

        // 3. Cek Absensi Hari Ini untuk Proteksi Logbook Reguler
        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();
        
        $hasAbsenHariIni = $absenHariIni ? true : false;
        $jamTerlambat = 0;

        // Jika belum absen, hitung estimasi jam keterlambatan (asumsi masuk 08:00)
        if (!$hasAbsenHariIni) {
            $jamMulai = 8;
            $jamSekarang = (int) Carbon::now()->format('H');
            if ($jamSekarang > $jamMulai) {
                $jamTerlambat = $jamSekarang - $jamMulai;
            }
        }

        // 4. Query Logbook Mahasiswa (Menampilkan semua logbook: reguler + susulan)
        $query = Logbook::where('user_id', $user->id);

        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('tanggal', $parts[0])
                      ->whereMonth('tanggal', $parts[1]);
            }
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // 5. AMBIL DATA CPMK BERDASARKAN PRODI MAHASISWA
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
            'isLocked'        => false,
            'sudahPembekalan' => true,
            'hasAbsenHariIni' => $hasAbsenHariIni,
            'jamTerlambat'    => $jamTerlambat,
            'logbooks'        => $logbooks,
            'pendaftaran'     => $pendaftaran,
            'daftarCpmk'      => $daftarCpmk,
            'user'            => $user
        ]);
    }

    /**
     * Menyimpan entry logbook harian reguler
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Proteksi Backend 1: Cek Pendaftaran Aktif
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima di program magang aktif.');
        }

        // Proteksi Backend 2: Cek Kehadiran Pembekalan
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

        // Proteksi Backend 3: WAJIB ABSEN HARI INI
        $absenHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$absenHariIni) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum mengisi absen kehadiran hari ini.');
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
        }
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook harian berhasil diperbarui.');
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

        // Ambil semua logbook (termasuk yang susulan) berurutan dari tanggal paling awal
        $logbooks = Logbook::where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($logbooks->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data logbook untuk di-export.');
        }

        foreach ($logbooks as $logbook) {
            $logbook->foto_base64 = null;

            if (!empty($logbook->foto_dokumentasi)) {
                $path = $logbook->foto_dokumentasi;
                if (Storage::disk('public')->exists($path)) {
                    $fileData = Storage::disk('public')->get($path);
                    $mimeType = Storage::disk('public')->mimeType($path);
                    $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
                } else {
                    $localPath = storage_path('app/public/' . ltrim($path, '/'));
                    if (file_exists($localPath) && !is_dir($localPath)) {
                        $fileData = file_get_contents($localPath);
                        $mimeType = mime_content_type($localPath);
                        $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
                    }
                }
            }
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
}