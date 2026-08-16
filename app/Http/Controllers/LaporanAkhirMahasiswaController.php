<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\Pendaftaran;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanAkhirMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // JIKA BUKAN ROLE MAHASISWA -> TAMPILKAN LISTING REKAPITULASI LAPORAN
        if ($user->hasAnyRole(['admin', 'superadmin', 'admin_prodi', 'dosen', 'spv'])) {
            $query = LaporanAkhir::with([
                'user.mahasiswaProfile.prodi',
                'pendaftaran.lowongan.perusahaan',
                'pendaftaran.dosen'
            ]);

            // Filter Scope Hak Akses
            if ($user->hasRole('dosen')) {
                // Dosen hanya melihat laporan mahasiswa yang dibimbingnya
                $query->whereHas('pendaftaran', fn($p) => $p->where('dosen_id', $user->id));
            } elseif ($user->hasRole('admin_prodi')) {
                // Admin Prodi melihat mahasiswa di prodinya
                $adminProdiId = $user->adminProdiProfile?->prodi_id;
                if ($adminProdiId) {
                    $query->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $adminProdiId));
                }
            } elseif ($user->hasRole('spv')) {
                // SPV Mitra melihat mahasiswa di perusahaannya
                $spvPerusahaanId = $user->spvProfile?->perusahaan_id;
                $query->whereHas('pendaftaran.lowongan', fn($l) => $l->where('perusahaan_id', $spvPerusahaanId));
            }

            // Filter Pencarian Teks
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul_laporan', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('user.mahasiswaProfile', fn($m) => $m->where('nim', 'like', "%{$search}%"));
                });
            }

            // Filter Status Verifikasi
            if ($request->filled('status') && $request->status !== 'semua') {
                $query->where('status_verifikasi', $request->status);
            }

            // Filter Prodi (Khusus Superadmin/Admin)
            if ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
                $query->whereHas('user.mahasiswaProfile', fn($m) => $m->where('prodi_id', $request->prodi_id));
            }

            $laporans = $query->latest()->paginate(10)->withQueryString();
            $prodis = Prodi::orderBy('nama_prodi', 'asc')->get();

            // Statistik Ringkasan
            $countQuery = clone $query;
            $totalLaporan = $countQuery->count();

            $countPending = clone $query;
            $totalPending = $countPending->where('status_verifikasi', 'pending')->count();

            $countApproved = clone $query;
            $totalApproved = $countApproved->where('status_verifikasi', 'approved')->count();

            $countRevisi = clone $query;
            $totalRevisi = $countRevisi->where('status_verifikasi', 'revisi')->count();

            return view('dashboard.laporan-akhir.index-admin', compact(
                'laporans',
                'prodis',
                'totalLaporan',
                'totalPending',
                'totalApproved',
                'totalRevisi',
                'user'
            ));
        }

        // ==========================================
        // KHUSUS MAHASISWA: FORM UPLOAD & STATUS
        // ==========================================
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        $laporan = LaporanAkhir::where('user_id', $user->id)->latest()->first();

        return view('dashboard.mahasiswa.laporan-akhir', compact('pendaftaran', 'laporan', 'user'));
    }

    /**
     * Mahasiswa Mengunggah / Revisi Dokumen Laporan Akhir
     */
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
                'status_verifikasi' => 'pending',
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

    /**
     * Dosen / Admin / SPV Melakukan Verifikasi Laporan Akhir
     */
    public function verifikasi(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin', 'admin_prodi', 'dosen'])) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:approved,revisi',
            'catatan'           => 'nullable|string',
        ]);

        $laporan = LaporanAkhir::findOrFail($id);

        $laporan->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan'           => $request->catatan,
        ]);

        $statusText = $request->status_verifikasi === 'approved' ? 'DISETUJUI (APPROVED)' : 'DIKEMBALIKAN UNTUK REVISI';
        $namaMhs = $laporan->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Laporan akhir {$namaMhs} berhasil diubah statusnya menjadi {$statusText}.");
    }
}