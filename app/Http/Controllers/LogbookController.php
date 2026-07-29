<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\MataKuliah;
use App\Models\Pendaftaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // 3. AMBIL MATA KULIAH BERDASARKAN PRODI MAHASISWA
        // Menggunakan optional chaining dan jaminan query fleksibel
        $mahasiswaProdiId = $user->mahasiswaProfile?->prodi_id;

        if ($mahasiswaProdiId) {
            $daftarMatkul = MataKuliah::where('prodi_id', $mahasiswaProdiId)
                ->pluck('nama_mk')
                ->toArray();
        } else {
            // Fallback: Jika profil/prodi_id belum diset, tampilkan seluruh mata kuliah
            $daftarMatkul = MataKuliah::pluck('nama_mk')->toArray();
        }

        // Jika DB mata kuliah benar-benar masih kosong, beri fallback sampel
        if (empty($daftarMatkul)) {
            $daftarMatkul = ['Proyek Industri', 'Etika Profesi & K3', 'Praktikum Terapan', 'Manajemen Proyek'];
        }

        return view('dashboard.logbook.index', compact('logbooks', 'pendaftaran', 'daftarMatkul', 'user'));
    }

    /**
     * Menyimpan entry logbook harian baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'uraian_kegiatan'  => 'required|string',
            'mata_kuliah'      => 'nullable|array',
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
            'mata_kuliah'      => $request->mata_kuliah ?? [],
            'foto_dokumentasi' => $fotoPath,
            'status_asistensi' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Logbook harian berhasil disimpan beserta foto dokumentasi.');
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
}