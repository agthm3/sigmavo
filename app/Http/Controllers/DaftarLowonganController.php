<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarLowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::with(['perusahaan', 'prodi'])
            ->where('status', 'published'); // Hanya tampilkan lowongan yang dipublikasikan

        // Filter Pencarian (Posisi / Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_posisi', 'like', "%{$search}%")
                  ->orWhereHas('perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        // Filter Lokasi
        if ($request->filled('lokasi') && $request->lokasi !== '') {
            $lokasi = $request->lokasi;
            $query->whereHas('perusahaan', function ($p) use ($lokasi) {
                $p->where('alamat', 'like', "%{$lokasi}%");
            });
        }

        $lowongans = $query->latest()->paginate(8)->withQueryString();

        // Ambil daftar ID lowongan yang sudah dilamar oleh mahasiswa yang sedang login
        $userPendaftaranIds = [];
        if (Auth::check()) {
            $userPendaftaranIds = Pendaftaran::where('user_id', Auth::id())
                ->pluck('lowongan_id')
                ->toArray();
        }

        return view('dashboard.daftar-lowongan.index', compact('lowongans', 'userPendaftaranIds'));
    }

    /**
     * Mahasiswa Melamar Lowongan Magang
     */
    public function lamar(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $lowongan = Lowongan::findOrFail($id);

        // Cek Kuota
        if ($lowongan->kuota_terisi >= $lowongan->kuota) {
            return redirect()->back()->with('error', 'Maaf, kuota untuk posisi ini sudah terpenuhi.');
        }

        // Cek apakah sudah pernah melamar
        $existing = Pendaftaran::where('user_id', $user->id)
            ->where('lowongan_id', $lowongan->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah pernah mengajukan lamaran untuk posisi ini.');
        }

        // Simpan Pendaftaran Baru
        Pendaftaran::create([
            'user_id'        => $user->id,
            'lowongan_id'    => $lowongan->id,
            'jalur_magang'   => 'reguler',
            'status_seleksi' => 'menunggu',
            'status_surat'   => 'menunggu',
        ]);

        return redirect()->back()->with('success', "Berhasil melamar posisi '{$lowongan->judul_posisi}'! Pantau status pendaftaran Anda di menu Status Pengajuan.");
    }
}