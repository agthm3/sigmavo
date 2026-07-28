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
        $query = Pendaftaran::with(['mahasiswa.mahasiswaProfile.prodi', 'lowongan.perusahaan', 'dosen']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', fn($m) => $m->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('mahasiswa.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
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

        $pendaftaran->update([
            'status_seleksi'  => $request->status_seleksi,
            'dosen_id'        => $request->dosen_id,
            'catatan_seleksi' => $request->catatan_seleksi,
        ]);

        // Jika status diterima, tambahkan kuota terisi di lowongan
        if ($request->status_seleksi === 'diterima') {
            $pendaftaran->lowongan->increment('kuota_terisi');
        }

        return redirect()->back()->with('success', 'Keputusan seleksi & Dosen Pendamping berhasil disimpan.');
    }
}