<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pendaftaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil pendaftaran magang aktif yang sudah DITERIMA
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        // 2. Jika mahasiswa belum diterima magang, set isLocked = true
        $isLocked = !$pendaftaran;

        // 3. Ambil parameter target jam dari Setting Global (Default: 900 Jam)
        $targetJam = (int) Setting::getByKey('min_jam_magang', 900);

        // 4. Hitung total jam yang telah diapprove dosen
        $jamTercapai = Absensi::where('user_id', $user->id)
            ->where('status_verifikasi', 'approved')
            ->sum('jam_diperoleh');

        $sisaJam = max(0, $targetJam - $jamTercapai);
        $persentase = $targetJam > 0 ? min(100, round(($jamTercapai / $targetJam) * 100, 1)) : 0;

        // 5. Data absensi hari ini
        $today = now()->toDateString();
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // 6. Riwayat Absensi
        $riwayats = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.absensi.index', compact(
            'pendaftaran',
            'isLocked',
            'targetJam',
            'jamTercapai',
            'sisaJam',
            'persentase',
            'absensiHariIni',
            'riwayats',
            'user'
        ));
    }

    /**
     * Proses Absen Masuk & Pulang (Base64 Camera WebRTC + GPS)
     */
    public function storeAbsensi(Request $request)
    {
        $user = Auth::user();

        // Proteksi Backend: Cek Pendaftaran Aktif
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima pada program magang aktif.');
        }

        $today = now()->toDateString();

        $request->validate([
            'tipe'      => 'required|in:masuk,pulang',
            'image'     => 'required|string', // String DataURL Base64
            'latitude'  => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        // Decode Base64 Image
        $imageParts = explode(";base64,", $request->image);
        $imageBuffer = base64_decode($imageParts[1] ?? '');
        $fileName = 'absensi/' . $user->id . '_' . time() . '_' . $request->tipe . '.jpg';
        Storage::disk('public')->put($fileName, $imageBuffer);

        $absensi = Absensi::firstOrNew([
            'user_id' => $user->id,
            'tanggal' => $today,
        ]);

        $absensi->pendaftaran_id = $pendaftaran->id;

        if ($request->tipe === 'masuk') {
            $absensi->waktu_masuk     = now()->toTimeString();
            $absensi->foto_masuk      = $fileName;
            $absensi->latitude_masuk  = $request->latitude;
            $absensi->longitude_masuk = $request->longitude;
        } else {
            $absensi->waktu_pulang     = now()->toTimeString();
            $absensi->foto_pulang      = $fileName;
            $absensi->latitude_pulang  = $request->latitude;
            $absensi->longitude_pulang = $request->longitude;

            // Jika masuk & pulang sudah ada, set alokasi jam standar (8 jam)
            if ($absensi->waktu_masuk) {
                $absensi->jam_diperoleh = 8;
            }
        }

        $absensi->save();

        return redirect()->back()->with('success', 'Absensi ' . ucfirst($request->tipe) . ' berhasil dicatat.');
    }

    /**
     * Pengajuan Izin / Sakit
     */
    public function storeIzin(Request $request)
    {
        $user = Auth::user();

        // Proteksi Backend: Cek Pendaftaran Aktif
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima pada program magang aktif.');
        }

        $today = now()->toDateString();

        $request->validate([
            'tipe_kehadiran' => 'required|in:izin,sakit',
            'alasan_izin'    => 'required|string',
            'surat_izin'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_izin')) {
            $suratPath = $request->file('surat_izin')->store('surat_izin', 'public');
        }

        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            [
                'pendaftaran_id'    => $pendaftaran->id,
                'tipe_kehadiran'    => $request->tipe_kehadiran,
                'alasan_izin'       => $request->alasan_izin,
                'surat_izin'        => $suratPath,
                'status_verifikasi' => 'pending',
                'jam_diperoleh'     => 0,
            ]
        );

        return redirect()->back()->with('success', 'Pengajuan ' . $request->tipe_kehadiran . ' berhasil dikirim.');
    }
}