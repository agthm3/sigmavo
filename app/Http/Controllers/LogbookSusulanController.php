<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Absensi;
use App\Models\Cpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookSusulanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        // Keamanan: Tolak jika dosen belum membuka akses
        if (!$pendaftaran || !$pendaftaran->allow_logbook_susulan) {
            return redirect()->route('dashboard-mahasiswa-logbook')->with('error', 'Akses Ditolak. Fitur pengisian Logbook Terlewat sedang ditutup. Hubungi Dosen Pembimbing untuk meminta izin buka akses.');
        }

        // Ambil riwayat logbook yang bertipe susulan
        $logbooks = Logbook::where('user_id', $user->id)
            ->where('is_susulan', true)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        // Ambil CPMK berdasarkan prodi mahasiswa
        $mahasiswaProdiId = $user->mahasiswaProfile?->prodi_id;

        if ($mahasiswaProdiId) {
            $daftarCpmk = Cpmk::where('prodi_id', $mahasiswaProdiId)
                ->get()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        } else {
            $daftarCpmk = Cpmk::all()
                ->map(fn($item) => "{$item->kode_cpmk} - {$item->deskripsi_cpmk}")
                ->toArray();
        }

        if (empty($daftarCpmk)) {
            $daftarCpmk = [
                'CPMK-01 - Mampu menerapkan analisis kriteria proyek industri',
                'CPMK-02 - Mampu mempraktikkan etika profesi & keselamatan kerja K3',
                'CPMK-03 - Mampu mengimplementasikan teknologi rekayasa terapan',
                'CPMK-04 - Mampu menyusun laporan kerja dan manajemen tim'
            ];
        }

        return view('dashboard.mahasiswa.logbook-susulan', compact('pendaftaran', 'logbooks', 'daftarCpmk'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$pendaftaran || !$pendaftaran->allow_logbook_susulan) {
            return redirect()->back()->with('error', 'Akses ditutup oleh Dosen Pembimbing.');
        }

        $request->validate([
            'tanggal'          => 'required|date|before_or_equal:today',
            'uraian_kegiatan'  => 'required|string|min:10',
            'mata_kuliah'      => 'nullable|array',
            'foto_dokumentasi' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('logbook_dokumentasi', 'public');
        }

        // 1. Buat record absensi otomatis untuk tanggal lampau jika belum ada
        Absensi::firstOrCreate(
            ['user_id' => $user->id, 'tanggal' => $request->tanggal],
            [
                'pendaftaran_id'    => $pendaftaran->id,
                'tipe_kehadiran'    => 'hadir',
                'waktu_masuk'       => '08:00:00',
                'waktu_pulang'      => '17:00:00',
                'status_verifikasi' => 'approved',
                'jam_diperoleh'     => 0,
            ]
        );

        // 2. Simpan entri logbook dengan flag is_susulan = true
        $logbook = new Logbook();
        $logbook->user_id          = $user->id;
        $logbook->pendaftaran_id   = $pendaftaran->id;
        $logbook->tanggal          = $request->tanggal;
        $logbook->uraian_kegiatan  = $request->uraian_kegiatan;
        $logbook->foto_dokumentasi = $fotoPath;
        $logbook->mata_kuliah      = $request->mata_kuliah ?? [];
        $logbook->status_asistensi = 'pending';
        $logbook->is_susulan       = true;
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook terlewat untuk tanggal ' . date('d M Y', strtotime($request->tanggal)) . ' berhasil dikirim ke antrean verifikasi SPV.');
    }
}