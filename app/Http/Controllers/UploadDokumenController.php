<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadDokumenController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        $laporans = LaporanAkhir::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard.upload-dokumen.index', compact('pendaftaran', 'laporans', 'user'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        $request->validate([
            'jenis_dokumen' => 'required|string',
            'judul_laporan' => 'required|string|max:255',
            'file_dokumen'  => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048', // Batas di server 2MB karena file sudah terkompres <500KB
        ]);

        $filePath = null;
        if ($request->hasFile('file_dokumen')) {
            $filePath = $request->file('file_dokumen')->store('laporan_akhir', 'public');
        }

        $judulLengkap = '[' . strtoupper(str_replace('_', ' ', $request->jenis_dokumen)) . '] ' . $request->judul_laporan;

        LaporanAkhir::create([
            'user_id'           => $user->id,
            'pendaftaran_id'    => $pendaftaran?->id,
            'judul_laporan'     => $judulLengkap,
            'file_laporan'      => $filePath,
            'catatan'           => $request->keterangan,
            'status_verifikasi' => 'pending',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Dokumen berhasil diunggah.']);
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah dan disimpan di repositori pelaporan.');
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $laporan = LaporanAkhir::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($laporan->status_verifikasi === 'approved') {
            return redirect()->back()->with('error', 'Dokumen yang telah disetujui/sah tidak dapat dihapus.');
        }

        if ($laporan->file_laporan && Storage::disk('public')->exists($laporan->file_laporan)) {
            Storage::disk('public')->delete($laporan->file_laporan);
        }

        $laporan->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus dari repositori.');
    }
}