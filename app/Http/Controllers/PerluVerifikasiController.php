<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerluVerifikasiController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Keamanan Hak Akses (Hanya Dosen & Admin)
        if (!$user->hasAnyRole(['dosen', 'admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        // 1. Ambil Antrean Logbook yang PENDING
        $queryLogbooks = Logbook::with(['user.mahasiswaProfile.prodi', 'pendaftaran.perusahaan'])
            ->where('status_asistensi', 'pending');

        // Jika Dosen, filter mahasiswa bimbingannya saja
        if ($user->hasRole('dosen')) {
            $queryLogbooks->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));
        }

        $pendingLogbooks = $queryLogbooks->latest()->get();

        // 2. Ambil Antrean Absensi (Izin/Sakit/Flag Lupa Pulang) yang PENDING
        $queryAbsensis = Absensi::with(['user.mahasiswaProfile.prodi', 'pendaftaran.perusahaan'])
            ->where('status_verifikasi', 'pending');

        if ($user->hasRole('dosen')) {
            $queryAbsensis->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));
        }

        $pendingAbsensis = $queryAbsensis->latest()->get();

        return view('dashboard.perlu-verifikasi.index', compact('pendingLogbooks', 'pendingAbsensis', 'user'));
    }

    /**
     * Verification Action untuk LOGBOOK (Approve / Revisi)
     */
    public function verifyLogbook(Request $request, $id)
    {
        $user = Auth::user();
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'action'        => 'required|in:approve,revisi',
            'catatan_dosen' => 'nullable|string',
        ]);

        if ($request->action === 'approve') {
            $logbook->status_asistensi = 'approved';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Telah disetujui.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            // Jika ada record absensi pada tanggal logbook tersebut, otomatis berikan 8 Jam
            $absensi = Absensi::where('user_id', $logbook->user_id)
                ->where('tanggal', $logbook->tanggal)
                ->first();

            if ($absensi) {
                $absensi->jam_diperoleh     = 8;
                $absensi->status_verifikasi = 'approved';
                $absensi->save();
            } else {
                // Buat record absensi otomatis dari logbook yang diapprove
                Absensi::create([
                    'user_id'           => $logbook->user_id,
                    'pendaftaran_id'    => $logbook->pendaftaran_id,
                    'tanggal'           => $logbook->tanggal,
                    'tipe_kehadiran'    => 'hadir',
                    'jam_diperoleh'     => 8,
                    'status_verifikasi' => 'approved',
                ]);
            }

            $message = "Logbook '{$logbook->user->name}' berhasil di-approve. Kuota jam magang bertambah +8 Jam.";
        } else {
            $logbook->status_asistensi = 'revisi';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Mohon perbaiki uraian kegiatan.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            $message = "Logbook '{$logbook->user->name}' dikembalikan untuk revisi.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Verification Action untuk ABSENSI (Izin / Sakit / Flag Lupa Pulang)
     */
    public function verifyAbsensi(Request $request, $id)
    {
        $user = Auth::user();
        $absensi = Absensi::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $absensi->status_verifikasi = 'approved';

            // Jika tipe_kehadiran adalah 'hadir' (contoh flag lupa pulang yang disahkan) beri 8 jam
            if ($absensi->tipe_kehadiran === 'hadir') {
                $absensi->jam_diperoleh = 8;
            } else {
                // Untuk Izin / Sakit yang disetujui, alokasi jam 0 (Izin sah, tapi tidak dihitung jam magang)
                $absensi->jam_diperoleh = 0;
            }

            $absensi->save();
            $message = "Pengajuan absensi/izin '{$absensi->user->name}' disetujui.";
        } else {
            $absensi->status_verifikasi = 'rejected';
            $absensi->jam_diperoleh     = 0;
            $absensi->save();

            $message = "Pengajuan absensi/izin '{$absensi->user->name}' ditolak.";
        }

        return redirect()->back()->with('success', $message);
    }
}