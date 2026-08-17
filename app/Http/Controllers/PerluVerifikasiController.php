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

        // 1. Keamanan Hak Akses (Dosen, SPV, Admin, Admin Prodi, Superadmin)
        if (!$user->hasAnyRole(['dosen', 'spv', 'admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        // ==========================================
        // 2. ANTREAN VERIFIKASI LOGBOOK
        // ==========================================
        $queryLogbooks = Logbook::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan'
        ]);

        if ($user->hasRole('spv')) {
            // SPV LAPANGAN: Mendukung Jalur Reguler & Jalur Mandiri
            $spvProdiId = $user->spvProfile?->prodi_id;
            $spvPerusahaanId = $user->spvProfile?->perusahaan_id;
            $namaPerusahaanSpv = $user->spvProfile?->perusahaan?->nama_perusahaan;

            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv'])
                ->whereHas('user.mahasiswaProfile', function($m) use ($spvProdiId) {
                    if ($spvProdiId) {
                        $m->where('prodi_id', $spvProdiId);
                    }
                })
                ->whereHas('pendaftaran', function($p) use ($spvPerusahaanId, $namaPerusahaanSpv, $user) {
                    $p->where(function($q) use ($spvPerusahaanId, $namaPerusahaanSpv, $user) {
                        // Jalur Reguler (melalui Lowongan)
                        if ($spvPerusahaanId) {
                            $q->whereHas('lowongan', fn($l) => $l->where('perusahaan_id', $spvPerusahaanId));
                        }

                        // Jalur Mandiri (Berdasarkan nama instansi atau kecocokan link pembimbing/spv)
                        if ($namaPerusahaanSpv) {
                            $q->orWhere('nama_instansi_mandiri', 'like', "%{$namaPerusahaanSpv}%");
                        }
                    });
                });

        } elseif ($user->hasRole('dosen')) {
            // DOSEN PEMBIMBING: Tampilkan logbook bimbingan yang SUDAH DI-APPROVE SPV ('approved_spv')
            $queryLogbooks->where('status_asistensi', 'approved_spv')
                ->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));

        } elseif ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv', 'approved_spv'])
                ->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                    if ($adminProdiId) {
                        $m->where('prodi_id', $adminProdiId);
                    }
                });
        } else {
            // Admin / Superadmin: Tampilkan seluruh antrean yang butuh verifikasi
            $queryLogbooks->whereIn('status_asistensi', ['pending', 'pending_spv', 'approved_spv']);
        }

        $pendingLogbooks = $queryLogbooks->latest()->get();

        // ==========================================
        // 3. ANTREAN VERIFIKASI ABSENSI / IZIN
        // ==========================================
        $queryAbsensis = Absensi::with([
            'user.mahasiswaProfile.prodi', 
            'pendaftaran.lowongan.perusahaan'
        ])->where('status_verifikasi', 'pending');

        if ($user->hasRole('spv')) {
            $spvProdiId = $user->spvProfile?->prodi_id;
            $spvPerusahaanId = $user->spvProfile?->perusahaan_id;
            $namaPerusahaanSpv = $user->spvProfile?->perusahaan?->nama_perusahaan;

            $queryAbsensis->whereHas('user.mahasiswaProfile', function($m) use ($spvProdiId) {
                if ($spvProdiId) {
                    $m->where('prodi_id', $spvProdiId);
                }
            })
            ->whereHas('pendaftaran', function($p) use ($spvPerusahaanId, $namaPerusahaanSpv) {
                $p->where(function($q) use ($spvPerusahaanId, $namaPerusahaanSpv) {
                    if ($spvPerusahaanId) {
                        $q->whereHas('lowongan', fn($l) => $l->where('perusahaan_id', $spvPerusahaanId));
                    }
                    if ($namaPerusahaanSpv) {
                        $q->orWhere('nama_instansi_mandiri', 'like', "%{$namaPerusahaanSpv}%");
                    }
                });
            });

        } elseif ($user->hasRole('dosen')) {
            $queryAbsensis->whereHas('pendaftaran', fn($q) => $q->where('dosen_id', $user->id));
        } elseif ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            $queryAbsensis->whereHas('user.mahasiswaProfile', function($m) use ($adminProdiId) {
                if ($adminProdiId) {
                    $m->where('prodi_id', $adminProdiId);
                }
            });
        }

        $pendingAbsensis = $queryAbsensis->latest()->get();

        return view('dashboard.perlu-verifikasi.index', compact('pendingLogbooks', 'pendingAbsensis', 'user'));
    }

    /**
     * Verification Action untuk LOGBOOK (Dosen & SPV)
     */
    public function verifyLogbook(Request $request, $id)
    {
        $isTestingMode = true;

        if ($isTestingMode) {
            return $this->verifyLogbookTesting($request, $id);
        }

        return $this->verifyLogbookProduction($request, $id);
    }

    /**
     * 1. MODE TESTING: Dilengkapi toleransi presensi saat uji coba
     */
    private function verifyLogbookTesting(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'action'        => 'required|in:approve,revisi',
            'catatan_dosen' => 'nullable|string',
        ]);

        // JIKA USER ADALAH SPV LAPANGAN MITRA
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

        // JIKA USER ADALAH DOSEN PEMBIMBING / ADMIN
        if ($request->action === 'approve') {
            $tglLogbook = \Carbon\Carbon::parse($logbook->tanggal)->format('Y-m-d');

            $absensi = Absensi::where('user_id', $logbook->user_id)
                ->whereDate('tanggal', $tglLogbook)
                ->first();

            // Kunci Kuota 8 Jam pada Absensi jika ada
            if ($absensi) {
                $absensi->jam_diperoleh     = 8;
                $absensi->status_verifikasi = 'approved';
                $absensi->save();
            }

            // Set Status Logbook Final
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

    /**
     * 2. MODE PRODUCTION: Ketat
     */
    private function verifyLogbookProduction(Request $request, $id)
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

            $absensi = Absensi::where('user_id', $logbook->user_id)
                ->whereDate('tanggal', $tglLogbook)
                ->first();

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