<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarLowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::with([
                'perusahaan', 
                'prodi', 
                'pendaftarans.user.mahasiswaProfile' => function ($q) {
                    $q->latest();
                }
            ])
            ->withCount([
                'pendaftarans as total_pelamar',
                'pendaftarans as total_diterima' => function ($q) {
                    $q->where('status_seleksi', 'diterima');
                },
                'pendaftarans as total_ditolak' => function ($q) {
                    $q->where('status_seleksi', 'ditolak');
                },
                'pendaftarans as total_menunggu' => function ($q) {
                    $q->whereIn('status_seleksi', ['menunggu', 'pending']);
                }
            ])
            ->where('status', 'published'); // Hanya tampilkan lowongan yang dipublikasikan

        // Filter Pencarian (Posisi / Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_posisi', 'like', "%{$search}%")
                  ->orWhereHas('perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        // Filter Lokasi
        if ($request->filled('lokasi') && $request->lokasi !== '') {
            $lokasi = $request->lokasi;
            $query->whereHas('perusahaan', function ($p) use ($lokasi) {
                $p->where('alamat', 'like', "%{$lokasi}%");
            });
        }

        $lowongans = $query->latest()->paginate(8)->withQueryString();

        // 1. Ambil ID Lowongan yang SEDANG AKTIF / PROSES (menunggu, pending, diterima)
        $activePendaftaranIds = [];
        // 2. Ambil ID Lowongan yang SUDAH SELESAI (riwayat stase sebelumnya)
        $completedPendaftaranIds = [];

        if (Auth::check()) {
            $userId = Auth::id();

            $activePendaftaranIds = Pendaftaran::where('user_id', $userId)
                ->whereIn('status_seleksi', ['menunggu', 'pending', 'diterima'])
                ->pluck('lowongan_id')
                ->filter()
                ->toArray();

            $completedPendaftaranIds = Pendaftaran::where('user_id', $userId)
                ->where('status_seleksi', 'selesai')
                ->pluck('lowongan_id')
                ->filter()
                ->toArray();
        }

        return view('dashboard.daftar-lowongan.index', compact('lowongans', 'activePendaftaranIds', 'completedPendaftaranIds'));
    }

    /**
     * Mahasiswa Melamar Lowongan Magang
     */
    public function lamar(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $lowongan = Lowongan::findOrFail($id);

        // 1. Cek Batas Waktu Pendaftaran
        if ($lowongan->batas_pendaftaran && \Carbon\Carbon::parse($lowongan->batas_pendaftaran)->endOfDay()->isPast()) {
            return redirect()->back()->with('error', 'Maaf, masa pendaftaran untuk posisi ini sudah berakhir.');
        }

        // 2. Cek apakah ada pendaftaran yang sedang aktif/proses pada lowongan ini
        $existingActive = Pendaftaran::where('user_id', $user->id)
            ->where('lowongan_id', $lowongan->id)
            ->whereIn('status_seleksi', ['menunggu', 'pending', 'diterima'])
            ->first();

        if ($existingActive) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan lamaran dan prosesnya masih aktif pada posisi ini.');
        }

        // 3. Cek apakah mahasiswa sedang berstatus magang aktif di tempat lain yang belum diselesaikan Admin
        $otherActive = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->first();

        if ($otherActive) {
            return redirect()->back()->with('error', 'Anda masih memiliki program magang aktif yang sedang berjalan. Minta Admin Prodi menyelesaikan periode stase/magang Anda saat ini sebelum mendaftar posisi baru.');
        }

        // 4. Simpan Pendaftaran Baru
        Pendaftaran::create([
            'user_id'        => $user->id,
            'lowongan_id'    => $lowongan->id,
            'jalur_magang'   => 'reguler',
            'status_seleksi' => 'menunggu',
            'status_surat'   => 'menunggu',
        ]);

        return redirect()->back()->with('success', "Berhasil mendaftar posisi '{$lowongan->judul_posisi}'! Pendaftaran Anda akan diproses dan diverifikasi oleh Admin Prodi.");
    }
}