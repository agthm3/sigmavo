<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cpmk;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Table;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil pendaftaran magang aktif mahasiswa
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // 2. Query Logbook Mahasiswa
        $query = Logbook::where('user_id', $user->id);

        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('tanggal', $parts[0])
                      ->whereMonth('tanggal', $parts[1]);
            }
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // 3. AMBIL DATA CPMK BERDASARKAN PRODI MAHASISWA
        $mahasiswaProdiId = $user->mahasiswaProfile?->prodi_id;

        if ($mahasiswaProdiId) {
            $daftarCpmk = Cpmk::where('prodi_id', $mahasiswaProdiId)
                ->get()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        } else {
            // Fallback: Jika prodi_id belum diset, ambil seluruh CPMK
            $daftarCpmk = Cpmk::all()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        }

        // Fallback sampel jika DB CPMK masih kosong
        if (empty($daftarCpmk)) {
            $daftarCpmk = [
                'CPMK-01 - Mampu menerapkan analisis kriteria proyek industri',
                'CPMK-02 - Mampu mempraktikkan etika profesi & keselamatan kerja K3',
                'CPMK-03 - Mampu mengimplementasikan teknologi rekayasa terapan',
                'CPMK-04 - Mampu menyusun laporan kerja dan manajemen tim'
            ];
        }

        return view('dashboard.logbook.index', compact('logbooks', 'pendaftaran', 'daftarCpmk', 'user'));
    }

    /**
     * Menyimpan entry logbook harian baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'uraian_kegiatan'  => 'required|string',
            'mata_kuliah'      => 'nullable|array', // Tetap menggunakan nama kolom DB mata_kuliah (array CPMK)
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // Simpan Gambar
        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('logbook_dokumentasi', 'public');
        }

        Logbook::create([
            'user_id'          => $user->id,
            'pendaftaran_id'   => $pendaftaran?->id,
            'tanggal'          => now()->toDateString(),
            'uraian_kegiatan'  => $request->uraian_kegiatan,
            'mata_kuliah'      => $request->mata_kuliah ?? [], // Menyimpan pilihan CPMK
            'foto_dokumentasi' => $fotoPath,
            'status_asistensi' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Logbook harian berhasil disimpan beserta keterkaitan CPMK.');
    }

    /**
     * Memperbarui entri logbook (Edit / Perbaikan Revisi)
     */
    public function update(Request $request, $id)
    {
        $logbook = Logbook::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($logbook->status_asistensi === 'approved') {
            return redirect()->back()->with('error', 'Logbook yang sudah disetujui tidak dapat diubah.');
        }

        $request->validate([
            'uraian_kegiatan'  => 'required|string',
            'mata_kuliah'      => 'nullable|array',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
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
        $logbook = Logbook::where('id', $id)
            ->where('user_id', Auth::id())
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
        // 1. Bersihkan output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Ambil Data Pendaftaran & Profile Mahasiswa
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // 3. Ambil Logbook Mahasiswa
        $logbooks = Logbook::where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($logbooks->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data logbook untuk di-export.');
        }

        // 4. Pre-processing Gambar ke Format Base64
        foreach ($logbooks as $logbook) {
            $logbook->foto_base64 = null;

            if (!empty($logbook->foto_dokumentasi)) {
                if (Storage::disk('public')->exists($logbook->foto_dokumentasi)) {
                    $fileData = Storage::disk('public')->get($logbook->foto_dokumentasi);
                    $mimeType = Storage::disk('public')->mimeType($logbook->foto_dokumentasi);
                    $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
                } else {
                    $localPath = storage_path('app/public/' . ltrim($logbook->foto_dokumentasi, '/'));
                    if (file_exists($localPath) && !is_dir($localPath)) {
                        $fileData = file_get_contents($localPath);
                        $mimeType = mime_content_type($localPath);
                        $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
                    }
                }
            }
        }

        // 5. Render HTML View khusus Word
        $htmlContent = view('dashboard.logbook.word-export', compact('user', 'pendaftaran', 'logbooks'))->render();

        // 6. Nama File Download
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name);
        $fileName = 'Logbook_Magang_' . $cleanName . '.doc';

        // 7. Stream Response langsung sebagai Dokumen Word
        return response($htmlContent, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Expires'             => '0',
        ]);
    }
}