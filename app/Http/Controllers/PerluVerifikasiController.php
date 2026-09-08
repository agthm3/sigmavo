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
        $currentUser = Auth::user();
        $targetUser = $currentUser;
        $isMonitoring = false;

        // 1. LOGIKA IMPERSONATE UNTUK ADMIN/SUPERADMIN
        if ($currentUser->hasAnyRole(['admin', 'superadmin']) && $request->filled('impersonate_user_id')) {
            $targetUser = User::findOrFail($request->impersonate_user_id);
            $isMonitoring = true; // Flag penanda bahwa ini sedang dipantau
        }

        // Keamanan Hak Akses
        if (!$targetUser->hasAnyRole(['dosen', 'spv', 'admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        // ==========================================
        // 2. ANTREAN VERIFIKASI LOGBOOK
        // ==========================================
        $queryLogbooks = Logbook::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan'
        ]);

        if ($targetUser->hasRole('spv')) {
            // [LOGIKA BARU]: SPV HANYA melihat logbook dari Lowongan/Divisi yang spesifik menautkan ID-nya!
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv'])
                ->whereHas('pendaftaran', function($p) use ($targetUser) {
                    $p->whereHas('lowongan', function($l) use ($targetUser) {
                        $l->where('spv_id', $targetUser->id); // <-- KUNCI ISOLASI DATA
                    });
                });

        } elseif ($targetUser->hasRole('dosen')) {
            // DOSEN PEMBIMBING: Hanya tampilkan logbook bimbingan yang SUDAH DI-APPROVE SPV
            $queryLogbooks->where('status_asistensi', 'approved_spv')
                ->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $targetUser->id));

        } elseif ($targetUser->hasRole('admin_prodi')) {
            // ADMIN PRODI: Tampilkan logbook di lingkup program studinya
            $adminProdiId = $targetUser->adminProdiProfile?->prodi_id;
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv', 'approved_spv'])
                ->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                    if ($adminProdiId) {
                        $m->where('prodi_id', $adminProdiId);
                    }
                });
        } else {
            // ADMIN / SUPERADMIN
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv', 'approved_spv']);
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
            // [LOGIKA BARU]: Sama seperti logbook, Izin/Sakit diisolasi berdasarkan spv_id di lowongan
            $queryAbsensis->whereHas('pendaftaran', function($p) use ($targetUser) {
                $p->whereHas('lowongan', function($l) use ($targetUser) {
                    $l->where('spv_id', $targetUser->id); // <-- KUNCI ISOLASI DATA
                });
            });

        } elseif ($targetUser->hasRole('dosen')) {
            $queryAbsensis->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $targetUser->id));

        } elseif ($targetUser->hasRole('admin_prodi')) {
            $adminProdiId = $targetUser->adminProdiProfile?->prodi_id;
            $queryAbsensis->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                if ($adminProdiId) {
                    $m->where('prodi_id', $adminProdiId);
                }
            });
        }

        $pendingAbsensis = $queryAbsensis->latest()->get();

        // 4. PEMISAHAN VIEW
        if ($isMonitoring) {
            return view('dashboard.monitoring.perlu-verifikasi', compact('pendingLogbooks', 'pendingAbsensis', 'targetUser'));
        }

        return view('dashboard.perlu-verifikasi.index', compact('pendingLogbooks', 'pendingAbsensis', 'currentUser'));
    }

    /**
     * Verification Action untuk LOGBOOK (Dosen & SPV)
     */
    public function verifyLogbook(Request $request, $id)
    {
        // ... (Fungsi ini biarkan sama percis dengan milik Anda)
        $isTestingMode = true;

        if ($isTestingMode) {
            return $this->verifyLogbookTesting($request, $id);
        }

        return $this->verifyLogbookProduction($request, $id);
    }

    private function verifyLogbookTesting(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'action'        => 'required|in:approve,revisi',
            'catatan_dosen' => 'nullable|string',
        ]);

        if ($user->hasRole('spv')) {
            if ($request->action === 'approve') {
                $logbook->status_asistensi = 'approved_spv';
                $logbook->catatan_dosen    = '[SPV]: ' . ($request->catatan_dosen ?? 'Disetujui oleh Supervisor Lapangan.');
                $logbook->save();
                return redirect()->back()->with('success', "[TESTING] Logbook '{$logbook->user->name}' berhasil di-approve SPV dan diteruskan ke Dosen Pembimbing.");
            } else {
                $logbook->status_asistensi = 'revisi';
                $logbook->catatan_dosen    = '[Revisi SPV]: ' . ($request->catatan_dosen ?? 'Mohon perbaiki uraian kegiatan.');
                $logbook->save();
                return redirect()->back()->with('success', "[TESTING] Logbook '{$logbook->user->name}' dikembalikan ke mahasiswa untuk revisi.");
            }
        }

        if ($request->action === 'approve') {
            $tglLogbook = \Carbon\Carbon::parse($logbook->tanggal)->format('Y-m-d');
            $absensi = Absensi::where('user_id', $logbook->user_id)->whereDate('tanggal', $tglLogbook)->first();

            if ($absensi) {
                $absensi->jam_diperoleh     = 8;
                $absensi->status_verifikasi = 'approved';
                $absensi->save();
            }

            $logbook->status_asistensi = 'approved';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Telah disetujui Dosen Pembimbing.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            return redirect()->back()->with('success', "[TESTING] Logbook '{$logbook->user->name}' berhasil di-approve Dosen. Jam magang bertambah +8 Jam.");
        } else {
            $logbook->status_asistensi = 'revisi';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Mohon perbaiki uraian kegiatan.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            return redirect()->back()->with('success', "[TESTING] Logbook '{$logbook->user->name}' dikembalikan untuk revisi.");
        }
    }

    private function verifyLogbookProduction(Request $request, $id)
    {
        // ... (Sama percis seperti milik Anda)
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'action'        => 'required|in:approve,revisi',
            'catatan_dosen' => 'nullable|string',
        ]);

        if ($user->hasRole('spv')) {
            if ($request->action === 'approve') {
                $logbook->status_asistensi = 'approved_spv';
                $logbook->catatan_dosen    = '[SPV]: ' . ($request->catatan_dosen ?? 'Disetujui oleh Supervisor Lapangan.');
                $logbook->save();
                return redirect()->back()->with('success', "Logbook '{$logbook->user->name}' berhasil di-approve SPV dan diteruskan ke Dosen Pembimbing.");
            } else {
                $logbook->status_asistensi = 'revisi';
                $logbook->catatan_dosen    = '[Revisi SPV]: ' . ($request->catatan_dosen ?? 'Mohon perbaiki uraian kegiatan.');
                $logbook->save();
                return redirect()->back()->with('success', "Logbook '{$logbook->user->name}' dikembalikan ke mahasiswa untuk revisi.");
            }
        }

        if ($request->action === 'approve') {
            $tglLogbook = \Carbon\Carbon::parse($logbook->tanggal)->format('Y-m-d');
            $absensi = Absensi::where('user_id', $logbook->user_id)->whereDate('tanggal', $tglLogbook)->first();

            if (!$absensi) {
                return redirect()->back()->with('error', "Gagal Approve! Mahasiswa '{$logbook->user->name}' belum melengkapi Absen Datang & Absen Pulang pada tanggal " . \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y') . ".");
            }

            $logbook->status_asistensi = 'approved';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Telah disetujui Dosen Pembimbing.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            $absensi->jam_diperoleh     = 8;
            $absensi->status_verifikasi = 'approved';
            $absensi->save();

            return redirect()->back()->with('success', "Logbook '{$logbook->user->name}' berhasil di-approve Dosen. Kuota jam magang bertambah +8 Jam.");
        } else {
            $logbook->status_asistensi = 'revisi';
            $logbook->catatan_dosen    = $request->catatan_dosen ?? 'Mohon perbaiki uraian kegiatan.';
            $logbook->verifikator_id   = $user->id;
            $logbook->waktu_verifikasi = now();
            $logbook->save();

            return redirect()->back()->with('success', "Logbook '{$logbook->user->name}' dikembalikan untuk revisi.");
        }
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