<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanGlobalController extends Controller
{
   public function index()
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki akses ke pengaturan global.');
        }

        $prodis = Prodi::withCount(['mahasiswas', 'dosens'])->get();

        // Ambil data settings global (dengan default fallback)
        $settings = [
            'tahun_akademik' => Setting::getByKey('tahun_akademik', '2025/2026 Ganjil'),
            'min_jam_magang' => Setting::getByKey('min_jam_magang', '900'),
            'max_pengajuan'  => Setting::getByKey('max_pengajuan', '3'),
            'email_resmi'    => Setting::getByKey('email_resmi', 'vokasi@unhas.ac.id'),
            'lokasi'         => Setting::getByKey('lokasi', 'Gedung Dekanat Vokasi, Kampus Tamalanrea Makassar'),
        ];

        return view('dashboard.manajemen-akun.pengaturan', compact('prodis', 'settings'));
    }

    /**
     * Menyimpan Parameter Global ke Database
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'tahun_akademik' => 'required|string|max:100',
            'min_jam_magang' => 'required|numeric|min:1',
            'max_pengajuan'  => 'required|numeric|min:1|max:10',
            'email_resmi'    => 'required|email|max:255',
            'lokasi'         => 'nullable|string|max:255',
        ]);

        Setting::setKey('tahun_akademik', $request->tahun_akademik);
        Setting::setKey('min_jam_magang', $request->min_jam_magang);
        Setting::setKey('max_pengajuan', $request->max_pengajuan);
        Setting::setKey('email_resmi', $request->email_resmi);
        Setting::setKey('lokasi', $request->lokasi);

        return redirect()->back()->with('success', 'Parameter & Pengaturan Global berhasil disimpan.');
    }

    /**
     * Tambah Prodi Baru
     */
    public function storeProdi(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|string|max:10|unique:prodis,kode_prodi',
            'nama_prodi' => 'required|string|max:255',
            'jenjang'    => 'required|string|in:D3,D4,S1,S2',
        ]);

        Prodi::create([
            'kode_prodi' => strtoupper($request->kode_prodi),
            'nama_prodi' => $request->nama_prodi,
            'jenjang'    => $request->jenjang,
        ]);

        return redirect()->back()->with('success', 'Program Studi baru berhasil ditambahkan.');
    }

    /**
     * Update Prodi
     */
    public function updateProdi(Request $request, $id)
    {
        $prodi = Prodi::findOrFail($id);

        $request->validate([
            'kode_prodi' => 'required|string|max:10|unique:prodis,kode_prodi,' . $id,
            'nama_prodi' => 'required|string|max:255',
            'jenjang'    => 'required|string|in:D3,D4,S1,S2',
        ]);

        $prodi->update([
            'kode_prodi' => strtoupper($request->kode_prodi),
            'nama_prodi' => $request->nama_prodi,
            'jenjang'    => $request->jenjang,
        ]);

        return redirect()->back()->with('success', "Data Program Studi '{$prodi->nama_prodi}' berhasil diperbarui.");
    }

    /**
     * Hapus Prodi
     */
    public function destroyProdi($id)
    {
        $prodi = Prodi::findOrFail($id);

        if ($prodi->mahasiswas()->count() > 0 || $prodi->dosens()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Masih ada mahasiswa/dosen yang terikat di prodi ini.');
        }

        $prodi->delete();

        return redirect()->back()->with('success', 'Program Studi berhasil dihapus.');
    }
}