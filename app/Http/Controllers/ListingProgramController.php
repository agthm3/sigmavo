<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ListingProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::with(['perusahaan', 'prodi'])
            ->withCount([
                'pendaftarans as total_pelamar',
                'pendaftarans as total_diterima' => function ($q) {
                    $q->where('status_seleksi', 'diterima');
                }
            ]);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_posisi', 'like', "%{$search}%")
                  ->orWhereHas('perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhereHas('prodi', fn($pr) => $pr->where('nama_prodi', 'like', "%{$search}%"));
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter Mode Kerja
        if ($request->filled('mode_kerja') && $request->mode_kerja !== 'semua') {
            $query->where('mode_kerja', $request->mode_kerja);
        }

        $lowongans = $query->latest()->paginate(10)->withQueryString();
        $perusahaans = Perusahaan::all();
        $prodis = Prodi::all();

        // Statistik
        $totalLowongan = Lowongan::count();
        $totalPublished = Lowongan::where('status', 'published')->count();
        $totalKuota = Lowongan::sum('kuota');
        $totalDraftClosed = Lowongan::whereIn('status', ['draft', 'closed'])->count();

        return view('dashboard.listing-program.index', compact(
            'lowongans', 'perusahaans', 'prodis', 
            'totalLowongan', 'totalPublished', 'totalKuota', 'totalDraftClosed'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perusahaan_id'     => 'required|exists:perusahaans,id',
            'judul_posisi'      => 'required|string|max:255',
            'prodi_id'          => 'nullable',
            'mode_kerja'        => 'required|in:WFO,Hybrid,WFH',
            'kuota'             => 'required|integer|min:1',
            'batas_pendaftaran' => 'required|date',
            'durasi'            => 'nullable|string',
            'deskripsi'         => 'required|string',
            'status'            => 'required|in:published,draft,closed',
        ]);

        Lowongan::create([
            'perusahaan_id'     => $request->perusahaan_id,
            'prodi_id'          => $request->prodi_id === 'all' ? null : $request->prodi_id,
            'judul_posisi'      => $request->judul_posisi,
            'mode_kerja'        => $request->mode_kerja,
            'kuota'             => $request->kuota, // Kuota Penerimaan
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'durasi'            => $request->durasi ?? '6 Bulan',
            'deskripsi'         => $request->deskripsi,
            'status'            => $request->status,
        ]);

        return redirect()->back()->with('success', 'Lowongan magang baru berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $lowongan = Lowongan::findOrFail($id);

        $request->validate([
            'perusahaan_id'     => 'required|exists:perusahaans,id',
            'judul_posisi'      => 'required|string|max:255',
            'prodi_id'          => 'nullable',
            'mode_kerja'        => 'required|in:WFO,Hybrid,WFH',
            'kuota'             => 'required|integer|min:1',
            'batas_pendaftaran' => 'required|date',
            'durasi'            => 'nullable|string',
            'deskripsi'         => 'required|string',
            'status'            => 'required|in:published,draft,closed',
        ]);

        $lowongan->update([
            'perusahaan_id'     => $request->perusahaan_id,
            'prodi_id'          => $request->prodi_id === 'all' ? null : $request->prodi_id,
            'judul_posisi'      => $request->judul_posisi,
            'mode_kerja'        => $request->mode_kerja,
            'kuota'             => $request->kuota, // Kuota Penerimaan
            'batas_pendaftaran' => $request->batas_pendaftaran,
            'durasi'            => $request->durasi ?? '6 Bulan',
            'deskripsi'         => $request->deskripsi,
            'status'            => $request->status,
        ]);

        return redirect()->back()->with('success', "Lowongan '{$lowongan->judul_posisi}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->delete();

        return redirect()->back()->with('success', 'Lowongan magang berhasil dihapus.');
    }

    public function togglePublish($id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->status = $lowongan->status === 'published' ? 'draft' : 'published';
        $lowongan->save();

        $msg = $lowongan->status === 'published' ? 'dipublikasikan' : 'dijadikan draft';
        return redirect()->back()->with('success', "Status lowongan berhasil {$msg}.");
    }
}