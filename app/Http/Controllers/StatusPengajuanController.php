<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Pendaftaran::with(['lowongan.perusahaan', 'dosen'])
            ->where('user_id', $userId);

        // Filter Status Seleksi
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

        if ($pendaftaran->status_seleksi !== 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dapat dibatalkan karena sudah dalam proses seleksi/selesai.');
        }

        $pendaftaran->delete();

        return redirect()->back()->with('success', 'Pengajuan magang berhasil dibatalkan.');
    }
}