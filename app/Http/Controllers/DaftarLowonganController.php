<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DaftarLowonganController extends Controller
{
    public function index(Request $request)
    {
        // Tambahkan relasi dosen dan spvProfiles agar datanya bisa dibaca
        $query = Lowongan::with([
                'perusahaan.spvProfiles.user', 
                'prodi', 
                'pendaftarans' => function ($q) {
                    $q->latest();
                },
                'pendaftarans.user.mahasiswaProfile',
                'pendaftarans.dosen'
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
                    $q->whereIn('status_seleksi', ['menunggu', 'pending', 'wawancara']);
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

        $lowongans = $query->latest()->paginate(9)->withQueryString();

        $activePendaftaranIds = [];
        $completedPendaftaranIds = [];

        if (Auth::check()) {
            $userId = Auth::id();

            $activePendaftaranIds = Pendaftaran::where('user_id', $userId)
                ->whereIn('status_seleksi', ['menunggu', 'pending', 'diterima', 'wawancara'])
                ->pluck('lowongan_id')
                ->filter()
                ->toArray();

            $completedPendaftaranIds = Pendaftaran::where('user_id', $userId)
                ->where('status_seleksi', 'selesai')
                ->pluck('lowongan_id')
                ->filter()
                ->toArray();
        }

        // Jika request dari AJAX (untuk Live Search Alpine), kirimkan JSON
        if ($request->ajax() || $request->wantsJson()) {
            $lowonganItems = $lowongans->map(function ($job) use ($activePendaftaranIds, $completedPendaftaranIds) {
                
                // Ambil daftar pelamar untuk Log Pelamar (Batasi 20 agar JSON tidak terlalu berat)
                $pelamars = $job->pendaftarans->take(20)->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nama' => $p->user->name ?? 'Mahasiswa',
                        'nim' => $p->user->mahasiswaProfile->nim ?? '-',
                        'status' => $p->status_seleksi,
                        'dosen' => $p->dosen->name ?? 'Belum ditugaskan',
                    ];
                })->toArray();

                return [
                    'id' => $job->id,
                    'judul_posisi' => $job->judul_posisi,
                    'perusahaan_nama' => $job->perusahaan->nama_perusahaan ?? '-',
                    'inisial' => $job->perusahaan->inisial ?? 'PT',
                    'alamat' => Str::limit($job->perusahaan->alamat ?? '-', 28),
                    'mode_kerja' => $job->mode_kerja,
                    'prodi_nama' => $job->prodi->nama_prodi ?? 'Semua Prodi',
                    'batas_pendaftaran' => $job->batas_pendaftaran ? $job->batas_pendaftaran->format('d M Y') : '-',
                    'kuota' => $job->kuota,
                    'total_pelamar' => $job->total_pelamar ?? 0,
                    'total_diterima' => $job->total_diterima ?? 0,
                    'total_menunggu' => $job->total_menunggu ?? 0,
                    'total_ditolak' => $job->total_ditolak ?? 0,
                    'is_active' => in_array($job->id, $activePendaftaranIds),
                    'is_completed' => in_array($job->id, $completedPendaftaranIds),
                    'deskripsi' => $job->deskripsi,
                    'durasi' => $job->durasi,
                    'perusahaan_sektor' => $job->perusahaan->sektor_industri ?? '-',
                    'spv_nama' => $job->perusahaan->spvProfiles->first()->user->name ?? 'Belum ada SPV',
                    'pelamars' => $pelamars
                ];
            });

            return response()->json([
                'data' => $lowonganItems,
                'links' => (string) $lowongans->links()
            ]);
        }

        return view('dashboard.daftar-lowongan.index', compact('lowongans', 'activePendaftaranIds', 'completedPendaftaranIds'));
    }

    public function lamar(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $lowongan = Lowongan::findOrFail($id);

        if ($lowongan->batas_pendaftaran && \Carbon\Carbon::parse($lowongan->batas_pendaftaran)->endOfDay()->isPast()) {
            return redirect()->back()->with('error', 'Maaf, masa pendaftaran untuk posisi ini sudah berakhir.');
        }

        $existingActive = Pendaftaran::where('user_id', $user->id)
            ->where('lowongan_id', $lowongan->id)
            ->whereIn('status_seleksi', ['menunggu', 'pending', 'wawancara', 'diterima'])
            ->first();

        if ($existingActive) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan lamaran dan prosesnya masih aktif pada posisi ini.');
        }

        $otherActive = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->first();

        if ($otherActive) {
            return redirect()->back()->with('error', 'Anda masih memiliki program magang aktif yang sedang berjalan. Minta Admin Prodi menyelesaikan periode stase Anda sebelum mendaftar posisi baru.');
        }

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