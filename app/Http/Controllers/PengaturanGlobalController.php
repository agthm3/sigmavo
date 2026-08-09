<?php

namespace App\Http\Controllers;

use App\Models\Cpmk;
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

        // Hak akses ke halaman pengaturan: Superadmin, Admin, atau Admin Prodi
        if (!$currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki akses ke pengaturan global.');
        }

        // Jika yang login Admin Prodi, filter prodi & cpmk untuk prodi dia sendiri
        if ($currentUser->hasAnyRole(['admin_prodi', 'admin-prodi'])) {
            $prodiId = $currentUser->adminProdiProfile?->prodi_id ?? $currentUser->prodi_id;
            $prodis = Prodi::where('id', $prodiId)->with(['cpmks'])->withCount(['mahasiswaProfiles', 'dosenProfiles'])->get();
            $cpmks  = Cpmk::where('prodi_id', $prodiId)->with('prodi')->latest()->get();
        } else {
            // Admin & Superadmin melihat seluruh prodi
            $prodis = Prodi::with(['cpmks'])->withCount(['mahasiswaProfiles', 'dosenProfiles'])->get();
            $cpmks  = Cpmk::with('prodi')->latest()->get();
        }

        // Ambil data settings global
        $settings = [
            'tahun_akademik' => Setting::getByKey('tahun_akademik', '2025/2026 Ganjil'),
            'min_jam_magang' => Setting::getByKey('min_jam_magang', '900'),
            'max_pengajuan'  => Setting::getByKey('max_pengajuan', '3'),
            'email_resmi'    => Setting::getByKey('email_resmi', 'vokasi@unhas.ac.id'),
            'lokasi'         => Setting::getByKey('lokasi', 'Gedung Dekanat Vokasi, Kampus Tamalanrea Makassar'),
        ];

        return view('dashboard.manajemen-akun.pengaturan', compact('prodis', 'cpmks', 'settings', 'currentUser'));
    }

    public function updateSettings(Request $request)
    {
        // Admin & Superadmin berhak mengubah Setting Global
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berhak mengubah parameter global.');
        }

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

   public function storeProdi(Request $request)
    {
        // Admin & Superadmin berhak membuat Master Prodi baru
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berhak membuat Master Program Studi.');
        }

        $request->validate([
            'kode_prodi' => 'required|string|max:50|unique:prodis,kode_prodi', // <-- Diubah max:10 menjadi max:50
            'nama_prodi' => 'required|string|max:255',
            'jenjang'    => 'required|string|in:D3,D4,S1,S2',
        ], [
            'kode_prodi.max'    => 'Kode Prodi tidak boleh lebih dari 50 karakter.',
            'kode_prodi.unique' => 'Kode Prodi sudah terdaftar.',
        ]);

        Prodi::create([
            'kode_prodi' => strtoupper($request->kode_prodi),
            'nama_prodi' => $request->nama_prodi,
            'jenjang'    => $request->jenjang,
        ]);

        return redirect()->back()->with('success', 'Program Studi baru berhasil ditambahkan.');
    }

    public function updateProdi(Request $request, $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berhak memperbarui Master Program Studi.');
        }

        $prodi = Prodi::findOrFail($id);

        $request->validate([
            'kode_prodi' => 'required|string|max:50|unique:prodis,kode_prodi,' . $id, // <-- Diubah max:10 menjadi max:50
            'nama_prodi' => 'required|string|max:255',
            'jenjang'    => 'required|string|in:D3,D4,S1,S2',
        ], [
            'kode_prodi.max'    => 'Kode Prodi tidak boleh lebih dari 50 karakter.',
            'kode_prodi.unique' => 'Kode Prodi sudah terdaftar.',
        ]);

        $prodi->update([
            'kode_prodi' => strtoupper($request->kode_prodi),
            'nama_prodi' => $request->nama_prodi,
            'jenjang'    => $request->jenjang,
        ]);

        return redirect()->back()->with('success', "Data Program Studi '{$prodi->nama_prodi}' berhasil diperbarui.");
    }

    public function destroyProdi($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berhak menghapus Master Program Studi.');
        }

        $prodi = Prodi::findOrFail($id);

        if ($prodi->mahasiswaProfiles()->count() > 0 || $prodi->dosenProfiles()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Masih ada mahasiswa/dosen yang terikat di prodi ini.');
        }

        $prodi->delete();

        return redirect()->back()->with('success', 'Program Studi berhasil dihapus.');
    }

    public function storeCpmk(Request $request)
    {
        $request->validate([
            'prodi_id'       => 'required|exists:prodis,id',
            'kode_cpmk'      => 'required|string|max:50',
            'deskripsi_cpmk' => 'required|string',
        ]);

        Cpmk::create($request->all());

        return redirect()->back()->with('success', 'Master CPMK berhasil ditambahkan.');
    }

    public function updateCpmk(Request $request, $id)
    {
        $cpmk = Cpmk::findOrFail($id);

        $request->validate([
            'prodi_id'       => 'required|exists:prodis,id',
            'kode_cpmk'      => 'required|string|max:50',
            'deskripsi_cpmk' => 'required|string',
        ]);

        $cpmk->update($request->all());

        return redirect()->back()->with('success', "CPMK '{$cpmk->kode_cpmk}' berhasil diperbarui.");
    }

    public function destroyCpmk($id)
    {
        $cpmk = Cpmk::findOrFail($id);
        $cpmk->delete();

        return redirect()->back()->with('success', 'CPMK berhasil dihapus.');
    }
}