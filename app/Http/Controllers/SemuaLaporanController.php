<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SemuaLaporanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Otorisasi Akses (Dosen, Admin, Admin Prodi)
        if (!$currentUser->hasAnyRole(['admin', 'admin_prodi', 'dosen'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki wewenang mengakses halaman verifikasi laporan akhir.');
        }

        $query = LaporanAkhir::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan',
            'verifikator'
        ]);

        // Filter Scope Berdasarkan Role
        if ($currentUser->hasRole('admin_prodi')) {
            $prodiId = $currentUser->prodi_id;
            $query->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId));
        } elseif ($currentUser->hasRole('dosen')) {
            $query->whereHas('pendaftaran', fn($p) => $p->where('dosen_id', $currentUser->id));
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($m) => $m->where('nim', 'like', "%{$search}%"))
                  ->orWhere('judul_laporan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
            $query->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $request->prodi_id));
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_verifikasi', $request->status);
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        // Hitung Ringkasan Statistik
        $statsQuery = LaporanAkhir::query();
        if ($currentUser->hasRole('admin_prodi')) {
            $statsQuery->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $currentUser->prodi_id));
        } elseif ($currentUser->hasRole('dosen')) {
            $statsQuery->whereHas('pendaftaran', fn($p) => $p->where('dosen_id', $currentUser->id));
        }

        $totalLaporan   = (clone $statsQuery)->count();
        $totalMenunggu  = (clone $statsQuery)->where('status_verifikasi', 'pending')->count();
        $totalApproved  = (clone $statsQuery)->where('status_verifikasi', 'approved')->count();
        $totalRevisi    = (clone $statsQuery)->where('status_verifikasi', 'revisi')->count();

        $prodis = Prodi::all();

        return view('dashboard.semua-laporan.index', compact(
            'laporans',
            'prodis',
            'currentUser',
            'totalLaporan',
            'totalMenunggu',
            'totalApproved',
            'totalRevisi'
        ));
    }

    /**
     * Verifikasi Langsung oleh Dosen Pembimbing
     */
    public function updateStatus(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $laporan = LaporanAkhir::findOrFail($id);

        $request->validate([
            'status_verifikasi' => 'required|in:approved,revisi,pending',
            'catatan'           => 'nullable|string',
        ]);

        $laporan->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan'           => $request->catatan,
            'verifikator_id'    => $currentUser->id,
            'waktu_verifikasi'  => now(),
        ]);

        $statusText = $request->status_verifikasi === 'approved' ? 'disetujui Dosen Pembimbing.' : 'dikembalikan ke mahasiswa untuk revisi.';

        return redirect()->back()->with('success', "Laporan akhir mahasiswa '{$laporan->user->name}' berhasil {$statusText}");
    }
}