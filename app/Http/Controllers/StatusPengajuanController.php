<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class StatusPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Pendaftaran::with(['lowongan.perusahaan', 'dosen'])
            ->where('user_id', $userId);

        // Filter Status Seleksi (menunggu, diterima, selesai, ditolak)
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_seleksi', $request->status);
        }

        $pendaftarans = $query->latest()->get();

        return view('dashboard.status-pengajuan.index', compact('pendaftarans'));
    }

    /**
     * Membatalkan pengajuan magang yang masih berstatus 'menunggu'
     */
    public function cancel($id)
    {
        $pendaftaran = Pendaftaran::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($pendaftaran->status_seleksi !== 'menunggu' && $pendaftaran->status_seleksi !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dapat dibatalkan karena sudah dalam proses seleksi/selesai.');
        }

        if ($pendaftaran->surat_balasan && Storage::disk('public')->exists($pendaftaran->surat_balasan)) {
            Storage::disk('public')->delete($pendaftaran->surat_balasan);
        }

        $pendaftaran->delete();

        return redirect()->back()->with('success', 'Pengajuan magang berhasil dibatalkan.');
    }

    /**
     * Download Surat Pengantar Magang PDF
     */
    public function downloadSurat($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftaran = Pendaftaran::with(['user.mahasiswaProfile.prodi', 'lowongan.perusahaan'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status_surat', 'terbit')
            ->firstOrFail();

        $pdf = Pdf::loadView('dashboard.status-pengajuan.pdf-surat-pengantar', compact('pendaftaran', 'user'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => false,
                  ]);

        $fileName = 'Surat_Pengantar_Magang_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name) . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Upload Surat Balasan dari Perusahaan / Instansi oleh Mahasiswa
     */
    public function uploadSuratBalasan(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'surat_balasan' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('surat_balasan')) {
            if ($pendaftaran->surat_balasan && Storage::disk('public')->exists($pendaftaran->surat_balasan)) {
                Storage::disk('public')->delete($pendaftaran->surat_balasan);
            }

            $path = $request->file('surat_balasan')->store('surat_balasan_perusahaan', 'public');
            $pendaftaran->update([
                'surat_balasan' => $path,
            ]);
        }

        return redirect()->back()->with('success', 'Surat balasan dari perusahaan berhasil diunggah. Pengelola akan segera memverifikasi status magang Anda.');
    }
}