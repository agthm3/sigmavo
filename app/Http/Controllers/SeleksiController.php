<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;

class SeleksiController extends Controller
{
    public function index(Request $request)
    {
        // Menggunakan relasi 'user' yang terhubung ke MahasiswaProfile
        $query = Pendaftaran::with(['user.mahasiswaProfile.prodi', 'lowongan.perusahaan', 'dosen']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan', fn($l) => $l->where('judul_posisi', 'like', "%{$search}%"));
            });
        }

        // Filter Lowongan
        if ($request->filled('lowongan_id') && $request->lowongan_id !== 'semua') {
            $query->where('lowongan_id', $request->lowongan_id);
        }

        // Filter Status Seleksi
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_seleksi', $request->status);
        }

        $pendaftarans = $query->latest()->paginate(10)->withQueryString();

        // Data Dropdown
        $lowongans = Lowongan::with('perusahaan')->get();
        $dosens = User::role('dosen')->get();

        // Ringkasan Statistik
        $totalPelamar = Pendaftaran::count();
        $totalMenunggu = Pendaftaran::where('status_seleksi', 'menunggu')->count();
        $totalDiterima = Pendaftaran::where('status_seleksi', 'diterima')->count();
        $totalDitolak = Pendaftaran::where('status_seleksi', 'ditolak')->count();

        return view('dashboard.seleksi.index', compact(
            'pendaftarans',
            'lowongans',
            'dosens',
            'totalPelamar',
            'totalMenunggu',
            'totalDiterima',
            'totalDitolak'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $request->validate([
            'status_seleksi'  => 'required|in:menunggu,diterima,ditolak,wawancara',
            'dosen_id'        => 'nullable|exists:users,id',
            'catatan_seleksi' => 'nullable|string',
        ]);

        $statusLama = $pendaftaran->status_seleksi;

        $pendaftaran->update([
            'status_seleksi'  => $request->status_seleksi,
            'dosen_id'        => $request->dosen_id,
            'catatan_seleksi' => $request->catatan_seleksi,
        ]);

        // Jika status berubah menjadi diterima dan sebelumnya bukan diterima, tambahkan kuota terisi
        if ($request->status_seleksi === 'diterima' && $statusLama !== 'diterima') {
            $pendaftaran->lowongan?->increment('kuota_terisi');
        }

        return redirect()->back()->with('success', 'Keputusan seleksi & Dosen Pendamping berhasil disimpan.');
    }
}