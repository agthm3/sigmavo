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
        if ($currentUser->hasRole('admin_prodi')) {
            $prodiId = $currentUser->prodi_id;
            $prodis = Prodi::where('id', $prodiId)->with(['cpmks'])->withCount(['mahasiswaProfiles', 'dosenProfiles'])->get();
            $cpmks  = Cpmk::where('prodi_id', $prodiId)->with('prodi')->latest()->get();
        } else {
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
        // KUNCI: Hanya Superadmin yang boleh mengubah Setting Global
        if (!Auth::user()->hasRole('superadmin')) {
            return redirect()->back()->with('error', 'Akses ditolak! Hanya Superadmin yang berhak mengubah parameter global.');
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
        // KUNCI: Hanya Superadmin yang boleh membuat Master Prodi baru
        if (!Auth::user()->hasRole('superadmin')) {
            return redirect()->back()->with('error', 'Akses ditolak! Hanya Superadmin yang berhak membuat Master Program Studi.');
        }

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

    public function updateProdi(Request $request, $id)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            return redirect()->back()->with('error', 'Akses ditolak! Hanya Superadmin yang berhak memperbarui Master Program Studi.');
        }

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

    public function destroyProdi($id)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            return redirect()->back()->with('error', 'Akses ditolak! Hanya Superadmin yang berhak menghapus Master Program Studi.');
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