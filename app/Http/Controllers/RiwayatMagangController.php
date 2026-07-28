<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatMagangController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Pendaftaran::with(['lowongan.perusahaan', 'dosen'])
            ->where('user_id', $userId);

        // Filter Status Seleksi / Progres
        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'berjalan') {
                $query->where('status_seleksi', 'diterima')
                      ->where(function ($q) {
                          $q->whereNull('tgl_selesai_magang')
                            ->orWhere('tgl_selesai_magang', '>=', now());
                      });
            } elseif ($request->status === 'selesai') {
                $query->where('status_seleksi', 'diterima')
                      ->where('tgl_selesai_magang', '<', now());
            } else {
                $query->where('status_seleksi', $request->status);
            }
        }

        $riwayats = $query->latest()->get();

        return view('dashboard.riwayat-magang.index', compact('riwayats'));
    }
}