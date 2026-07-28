<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PengajuanMagangController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['mahasiswa.mahasiswaProfile.prodi', 'lowongan.perusahaan']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', fn($m) => $m->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('mahasiswa.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        // Filter Jalur Magang
        if ($request->filled('jalur') && $request->jalur !== 'semua') {
            $query->where('jalur_magang', $request->jalur);
        }

        // Filter Status Surat
        if ($request->filled('status_surat') && $request->status_surat !== 'semua') {
            $query->where('status_surat', $request->status_surat);
        }

        $pendaftarans = $query->latest()->paginate(10)->withQueryString();

        // Ringkasan Statistik
        $totalPengajuan = Pendaftaran::count();
        $totalPerluSurat = Pendaftaran::where('status_surat', 'menunggu')->count();
        $totalSuratTerbit = Pendaftaran::where('status_surat', 'terbit')->count();
        $totalMandiri = Pendaftaran::where('jalur_magang', 'mandiri')->count();

        return view('dashboard.pengajuan-magang.index', compact(
            'pendaftarans',
            'totalPengajuan',
            'totalPerluSurat',
            'totalSuratTerbit',
            'totalMandiri'
        ));
    }

    /**
     * Terbitkan Surat Pengantar Magang
     */
    public function terbitSurat(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $request->validate([
            'nomor_surat'        => 'required|string|max:100',
            'perihal_surat'      => 'required|string|max:255',
            'tgl_mulai_magang'   => 'required|date',
            'tgl_selesai_magang' => 'required|date|after_or_equal:tgl_mulai_magang',
        ]);

        $pendaftaran->update([
            'nomor_surat'        => $request->nomor_surat,
            'perihal_surat'      => $request->perihal_surat,
            'tgl_mulai_magang'   => $request->tgl_mulai_magang,
            'tgl_selesai_magang' => $request->tgl_selesai_magang,
            'status_surat'       => 'terbit',
        ]);

        return redirect()->back()->with('success', "Surat pengantar untuk {$pendaftaran->mahasiswa->name} berhasil diterbitkan.");
    }
}