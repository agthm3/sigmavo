<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanAkhirMahasiswaController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil data pendaftaran magang mahasiswa yang diterima
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // Ambil riwayat laporan akhir yang pernah diunggah
        $laporan = LaporanAkhir::where('user_id', $user->id)->latest()->first();

        return view('dashboard.mahasiswa.laporan-akhir', compact('pendaftaran', 'laporan', 'user'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'judul_laporan' => 'required|string|max:255',
            'file_laporan'  => 'required|file|mimes:pdf|max:10240', // Maksimal 10MB PDF
        ], [
            'judul_laporan.required' => 'Judul laporan wajib diisi.',
            'file_laporan.required'  => 'File dokumen PDF laporan akhir wajib diunggah.',
            'file_laporan.mimes'     => 'Format file laporan harus berupa dokumen PDF (.pdf).',
            'file_laporan.max'       => 'Ukuran file laporan maksimal adalah 10 MB.',
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // Simpan File PDF Laporan
        $filePath = null;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store('laporan_akhir', 'public');
        }

        // Cek jika sudah ada laporan sebelumnya (Upload Ulang / Revisi)
        $laporanExisting = LaporanAkhir::where('user_id', $user->id)->first();

        if ($laporanExisting) {
            if ($laporanExisting->file_laporan && Storage::disk('public')->exists($laporanExisting->file_laporan)) {
                Storage::disk('public')->delete($laporanExisting->file_laporan);
            }

            $laporanExisting->update([
                'pendaftaran_id'    => $pendaftaran?->id,
                'judul_laporan'     => $request->judul_laporan,
                'file_laporan'      => $filePath,
                'status_verifikasi' => 'pending', // Kembali ke pending agar diverifikasi ulang
                'catatan'           => null,
            ]);
        } else {
            LaporanAkhir::create([
                'user_id'           => $user->id,
                'pendaftaran_id'    => $pendaftaran?->id,
                'judul_laporan'     => $request->judul_laporan,
                'file_laporan'      => $filePath,
                'status_verifikasi' => 'pending',
            ]);
        }

        return redirect()->back()->with('success', 'Laporan akhir berhasil diunggah dan masuk antrean verifikasi.');
    }
}