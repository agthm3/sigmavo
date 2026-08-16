<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PengajuanMagangController extends Controller
{
   public function index(Request $request)
    {
        // Hanya ambil pendaftaran yang memiliki akun user aktif
        $query = Pendaftaran::has('user')
            ->with(['user.mahasiswaProfile.prodi', 'lowongan.perusahaan']);

        // Filter Pencarian Teks (Nama, NIM, Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($m) => $m->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        // Filter Jalur Magang
        if ($request->filled('jalur') && $request->jalur !== 'semua') {
            $query->where('jalur_magang', $request->jalur);
        }

        // Filter Status Surat
        if ($request->filled('status_surat') && $request->status_surat !== 'semua') {
            $query->where('status_surat', $request->status_surat);
        }

        $pendaftarans = $query->latest()->paginate(10)->withQueryString();

        // Ringkasan Statistik
        $totalPengajuan  = Pendaftaran::has('user')->count();
        $totalPerluSurat = Pendaftaran::has('user')->where('status_surat', 'menunggu')->count();
        $totalSuratTerbit = Pendaftaran::has('user')->where('status_surat', 'terbit')->count();
        $totalMandiri    = Pendaftaran::has('user')->where('jalur_magang', 'mandiri')->count();

        return view('dashboard.pengajuan-magang.index', compact(
            'pendaftarans',
            'totalPengajuan',
            'totalPerluSurat',
            'totalSuratTerbit',
            'totalMandiri'
        ));
    }

    /**
     * Terbitkan / Update Surat Pengantar Magang
     */
    public function terbitSurat(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $request->validate([
            'nomor_surat'        => 'required|string|max:100',
            'perihal_surat'      => 'required|string|max:255',
            'tgl_mulai_magang'   => 'required|date',
            'tgl_selesai_magang' => 'required|date|after_or_equal:tgl_mulai_magang',
        ]);

        $pendaftaran->update([
            'nomor_surat'        => $request->nomor_surat,
            'perihal_surat'      => $request->perihal_surat,
            'tgl_mulai_magang'   => $request->tgl_mulai_magang,
            'tgl_selesai_magang' => $request->tgl_selesai_magang,
            'status_surat'       => 'terbit',
        ]);

        $namaMahasiswa = $pendaftaran->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Surat pengantar untuk {$namaMahasiswa} berhasil diterbitkan.");
    }

 
    public function verifikasiSuratBalasan(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::with('lowongan')->findOrFail($id);

        $request->validate([
            'status_seleksi'  => 'required|in:diterima,ditolak',
            'catatan_seleksi' => 'nullable|string|max:255',
        ]);

        // Cek Kuota Penerimaan jika admin memilih 'diterima'
        if ($request->status_seleksi === 'diterima' && $pendaftaran->lowongan_id) {
            $lowongan = $pendaftaran->lowongan;
            $jumlahDiterima = Pendaftaran::where('lowongan_id', $lowongan->id)
                ->where('status_seleksi', 'diterima')
                ->count();

            if ($jumlahDiterima >= $lowongan->kuota) {
                return redirect()->back()->with('error', "Gagal menerima: Kuota penerimaan untuk lowongan '{$lowongan->judul_posisi}' sudah penuh ({$lowongan->kuota} Mahasiswa).");
            }
        }

        $pendaftaran->update([
            'status_seleksi'  => $request->status_seleksi,
            'catatan_seleksi' => $request->catatan_seleksi,
        ]);

        $statusText = $request->status_seleksi === 'diterima' ? 'DITERIMA MAGANG' : 'DITOLAK';
        $namaMhs = $pendaftaran->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Berhasil memperbarui status pendaftaran {$namaMhs} menjadi {$statusText}.");
    }

    public function selesaikanMagang(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $pendaftaran->update([
            'status_seleksi'     => 'selesai',
            'tgl_selesai_magang' => now()->toDateString(),
            'catatan_seleksi'    => $request->catatan ?? 'Telah menyelesaikan periode magang / stase poli.',
        ]);

        $namaMhs = $pendaftaran->user?->name ?? 'Mahasiswa';
        $posisi = $pendaftaran->lowongan?->judul_posisi ?? 'Magang';

        return redirect()->back()->with('success', "Periode magang ({$posisi}) untuk {$namaMhs} telah diselesaikan. Mahasiswa kini dapat mendaftar ke stase/poli berikutnya.");
    }
}