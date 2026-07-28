<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramMagangController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil program magang aktif mahasiswa (status_seleksi = 'diterima')
        $activeMagang = Pendaftaran::with(['lowongan.perusahaan', 'dosen.dosenProfile'])
            ->where('user_id', $userId)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // Ambil standar jam magang dari Pengaturan Global (default 900 jam)
        $targetJam = (int) Setting::getByKey('min_jam_magang', 900);

        // Contoh akumulasi jam terisi (nantinya dihitung dinamis dari logbook/absensi)
        $jamTerisi = $activeMagang ? 120 : 0; 
        $sisaJam = max(0, $targetJam - $jamTerisi);
        $percentage = $targetJam > 0 ? min(100, round(($jamTerisi / $targetJam) * 100, 1)) : 0;

        return view('dashboard.program-magang.index', compact(
            'activeMagang',
            'targetJam',
            'jamTerisi',
            'sisaJam',
            'percentage'
        ));
    }

    /**
     * Memproses pengajuan izin/sakit mahasiswa
     */
    public function ajukanIzin(Request $request)
    {
        $request->validate([
            'jenis_izin'  => 'required|in:sakit,izin',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan'      => 'required|string',
        ]);

        return redirect()->back()->with('success', 'Permohonan izin/sakit berhasil dikirim ke Dosen Pendamping dan Supervisor.');
    }
}