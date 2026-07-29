<?php

namespace App\Http\Controllers;

use App\Models\Pembekalan;
use App\Models\PembekalanMateri;
use App\Models\PembekalanPresensi;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembekalanMagangController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil agenda pembekalan terbaru
        $pembekalan = Pembekalan::with(['materis', 'prodi'])->latest()->first();

        // 2. Cek status presensi mandiri untuk user yang sedang login
        $presensi = null;
        if ($pembekalan && $user) {
            $presensi = PembekalanPresensi::where('pembekalan_id', $pembekalan->id)
                ->where('user_id', $user->id)
                ->first();
        }

        // 3. Data Rekap Presensi & Mahasiswa (Untuk Admin / Admin Prodi / Superadmin)
        $mahasiswas = collect();
        $totalMhs = 0;
        $totalHadir = 0;
        $totalBelum = 0;

        if ($pembekalan && $user->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            $queryMhs = User::role('mahasiswa')->with(['mahasiswaProfile.prodi']);

            // Filter prodi jika admin prodi
            if ($user->hasRole('admin_prodi') && $user->adminProdiProfile?->prodi_id) {
                $prodiId = $user->adminProdiProfile->prodi_id;
                $queryMhs->whereHas('mahasiswaProfile', fn($q) => $q->where('prodi_id', $prodiId));
            }

            // Pencarian nama/NIM
            if ($request->filled('search')) {
                $search = $request->search;
                $queryMhs->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('mahasiswaProfile', fn($p) => $p->where('nim', 'like', "%{$search}%"));
                });
            }

            $mahasiswas = $queryMhs->get();

            // Peta status presensi per user ID
            $presensiMap = PembekalanPresensi::where('pembekalan_id', $pembekalan->id)
                ->get()
                ->keyBy('user_id');

            $mahasiswas->transform(function ($mhs) use ($presensiMap) {
                $mhs->presensi_pembekalan = $presensiMap->get($mhs->id);
                return $mhs;
            });

            $totalMhs = $mahasiswas->count();
            $totalHadir = $mahasiswas->filter(fn($m) => $m->presensi_pembekalan?->is_hadir)->count();
            $totalBelum = $totalMhs - $totalHadir;
        }

        $prodis = Prodi::all();

        return view('dashboard.pembekalan-magang.index', compact(
            'pembekalan',
            'presensi',
            'mahasiswas',
            'totalMhs',
            'totalHadir',
            'totalBelum',
            'prodis',
            'user'
        ));
    }

    /**
     * Konfirmasi Presensi Mandiri oleh Mahasiswa
     */
    public function presensi(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Silakan login terlebih dahulu.');
        }

        $pembekalan = Pembekalan::findOrFail($id);

        PembekalanPresensi::updateOrCreate(
            [
                'pembekalan_id' => $pembekalan->id,
                'user_id'       => $user->id,
            ],
            [
                'is_hadir'       => true,
                'waktu_presensi' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Kehadiran pembekalan Anda berhasil dikonfirmasi.');
    }

    /**
     * Manual Override Kehadiran Mahasiswa oleh Admin / Admin Prodi
     */
    public function togglePresensiManual(Request $request, $pembekalanId, $userId)
    {
        $presensi = PembekalanPresensi::where('pembekalan_id', $pembekalanId)
            ->where('user_id', $userId)
            ->first();

        if ($presensi && $presensi->is_hadir) {
            $presensi->update(['is_hadir' => false]);
            $msg = 'Status kehadiran mahasiswa berhasil dibatalkan.';
        } else {
            PembekalanPresensi::updateOrCreate(
                ['pembekalan_id' => $pembekalanId, 'user_id' => $userId],
                ['is_hadir' => true, 'waktu_presensi' => now()]
            );
            $msg = 'Mahasiswa berhasil ditandai HADIR secara manual.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Buat Agenda Pembekalan Baru (Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'waktu_mulai'   => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'lokasi'        => 'required|string',
            'link_zoom'     => 'nullable|url',
            'pemateri'      => 'required|string',
            'topik_utama'   => 'nullable|string',
        ]);

        Pembekalan::create([
            'judul'         => $request->judul,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'lokasi'        => $request->lokasi,
            'link_zoom'     => $request->link_zoom,
            'pemateri'      => $request->pemateri,
            'topik_utama'   => $request->topik_utama,
            'status'        => 'mendatang',
        ]);

        return redirect()->back()->with('success', 'Agenda Pembekalan Magang berhasil diterbitkan.');
    }

    /**
     * Tambah Berkas Materi Pembekalan Baru
     */
    public function storeMateri(Request $request, $id)
    {
        $request->validate([
            'judul_materi' => 'required|string|max:255',
            'tipe_file'    => 'required|string',
            'ukuran_file'  => 'nullable|string',
        ]);

        PembekalanMateri::create([
            'pembekalan_id' => $id,
            'judul_materi'  => $request->judul_materi,
            'tipe_file'     => $request->tipe_file,
            'ukuran_file'   => $request->ukuran_file ?? '1.2 MB',
        ]);

        return redirect()->back()->with('success', 'Materi pembekalan berhasil ditambahkan.');
    }
}