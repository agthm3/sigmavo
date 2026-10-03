<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerluVerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $targetUser = $currentUser;
        $isMonitoring = false;

        // 1. LOGIKA IMPERSONATE UNTUK ADMIN/SUPERADMIN
        if ($currentUser->hasAnyRole(['admin', 'superadmin']) && $request->filled('impersonate_user_id')) {
            $targetUser = User::findOrFail($request->impersonate_user_id);
            $isMonitoring = true; 
        }

        if (!$targetUser->hasAnyRole(['dosen', 'spv', 'admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        // ==========================================
        // 2. ANTREAN VERIFIKASI LOGBOOK (PARALEL)
        // ==========================================
        $queryLogbooks = Logbook::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan'
        ]);

        if ($targetUser->hasRole('spv')) {
            // SPV: Lihat logbook yang status_spv nya masih pending
            $queryLogbooks->where('status_spv', 'pending')
                ->whereHas('pendaftaran.lowongan', function($l) use ($targetUser) {
                    $l->where('spv_id', $targetUser->id);
                });

        } elseif ($targetUser->hasRole('dosen')) {
            // DOSEN: Lihat logbook yang status_dosen nya masih pending (TIDAK PERLU NUNGGU SPV LAGI)
            $queryLogbooks->where('status_dosen', 'pending')
                ->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $targetUser->id));

        } elseif ($targetUser->hasRole('admin_prodi')) {
            $adminProdiId = $targetUser->adminProdiProfile?->prodi_id;
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'revisi', 'approved'])
                ->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                    if ($adminProdiId) $m->where('prodi_id', $adminProdiId);
                });
        } else {
            // ADMIN / SUPERADMIN melihat yang master statusnya belum approved
            $queryLogbooks->where('status_asistensi', '!=', 'approved');
        }

        $pendingLogbooks = $queryLogbooks->latest()->get();

        // ==========================================
        // 3. ANTREAN VERIFIKASI ABSENSI / IZIN / SAKIT
        // ==========================================
        $queryAbsensis = Absensi::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan'
        ])
        ->where('status_verifikasi', 'pending')
        ->whereIn('tipe_kehadiran', ['izin', 'sakit']); 

        if ($targetUser->hasRole('spv')) {
            $queryAbsensis->whereHas('pendaftaran.lowongan', function($l) use ($targetUser) {
                $l->where('spv_id', $targetUser->id);
            });
        } elseif ($targetUser->hasRole('dosen')) {
            $queryAbsensis->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $targetUser->id));
        } elseif ($targetUser->hasRole('admin_prodi')) {
            $adminProdiId = $targetUser->adminProdiProfile?->prodi_id;
            $queryAbsensis->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                if ($adminProdiId) $m->where('prodi_id', $adminProdiId);
            });
        }

        $pendingAbsensis = $queryAbsensis->latest()->get();

        if ($isMonitoring) {
            return view('dashboard.monitoring.perlu-verifikasi', compact('pendingLogbooks', 'pendingAbsensis', 'targetUser'));
        }

        return view('dashboard.perlu-verifikasi.index', compact('pendingLogbooks', 'pendingAbsensis', 'currentUser'));
    }

    /**
     * Verification Action untuk LOGBOOK (Dosen & SPV PARALEL)
     */
    public function verifyLogbook(Request $request, $id)
    {
        return $this->verifyLogbookProduction($request, $id);
    }

    private function verifyLogbookProduction(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'action'  => 'required|in:approve,revisi',
            'catatan' => 'nullable|string',
        ]);

        $inputCatatan = $request->catatan;

        // JIKA YANG KLIK ADALAH SPV LAPANGAN
        if ($user->hasRole('spv')) {
            if ($request->action === 'approve') {
                $logbook->status_spv  = 'approved';
                $logbook->catatan_spv = $inputCatatan ?? 'Disetujui Pembimbing Lapangan.';
                // Jika sebelumnya status_asistensi revisi karena SPV, kembalikan ke pending jika dosen belum approve
                if ($logbook->status_dosen !== 'revisi') {
                    $logbook->status_asistensi = 'pending';
                }
            } else {
                $logbook->status_spv       = 'revisi';
                $logbook->status_asistensi = 'revisi'; // Master switch agar mhs merevisi
                $logbook->catatan_spv      = $inputCatatan ?? 'Mohon perbaiki kegiatan Anda.';
            }
        } 
        // JIKA YANG KLIK ADALAH DOSEN PEMBIMBING (Atau Admin)
        else {
            if ($request->action === 'approve') {
                $logbook->status_dosen   = 'approved';
                $logbook->catatan_dosen  = $inputCatatan ?? 'Telah disetujui Dosen Pembimbing.';
                $logbook->verifikator_id = $user->id; // Dosen pencatat
                // Jika sebelumnya status_asistensi revisi karena Dosen, kembalikan ke pending jika SPV belum revisi
                if ($logbook->status_spv !== 'revisi') {
                    $logbook->status_asistensi = 'pending';
                }
            } else {
                $logbook->status_dosen     = 'revisi';
                $logbook->status_asistensi = 'revisi'; // Master switch agar mhs merevisi
                $logbook->catatan_dosen    = $inputCatatan ?? 'Mohon perbaiki laporan ini.';
            }
        }

        $logbook->save();

        // ==================================================
        // PENGECEKAN DUAL APPROVAL (TRIGGER +8 JAM)
        // ==================================================
        if ($logbook->status_spv === 'approved' && $logbook->status_dosen === 'approved' && $logbook->status_asistensi !== 'approved') {
            
            $tglLogbook = Carbon::parse($logbook->tanggal)->format('Y-m-d');
            $absensi = Absensi::where('user_id', $logbook->user_id)->whereDate('tanggal', $tglLogbook)->first();

            // Kunci logbook menjadi approved
            $logbook->status_asistensi = 'approved';
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            // Set jam absensi ke 8 jam (pasti 8 jam, tidak akan dobel meski ada 2 logbook di hari yang sama)
            if ($absensi && $absensi->waktu_pulang) {
                $absensi->jam_diperoleh     = 8;
                $absensi->status_verifikasi = 'approved';
                $absensi->save();

                return redirect()->back()->with('success', "Logbook disetujui! Karena kedua pembimbing (SPV & Dosen) telah menyetujui, Kuota jam magang mahasiswa otomatis bertambah +8 Jam.");
            }

            return redirect()->back()->with('success', "Logbook disetujui oleh kedua pembimbing (SPV & Dosen).");
        }

        $statusRole = $user->hasRole('spv') ? 'Supervisor' : 'Dosen Pembimbing';
        $statusAction = $request->action === 'approve' ? 'disetujui' : 'dikembalikan (Revisi)';

        return redirect()->back()->with('success', "Logbook berhasil {$statusAction} oleh Anda ({$statusRole}). Sistem menunggu pihak lainnya melakukan verifikasi.");
    }

    public function verifyAbsensi(Request $request, $id)
    {
        $user = Auth::user();
        $absensi = Absensi::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]); 

        if ($request->action === 'approve') {
            $absensi->status_verifikasi = 'approved';
            $absensi->jam_diperoleh = ($absensi->tipe_kehadiran === 'hadir') ? 8 : 0;
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