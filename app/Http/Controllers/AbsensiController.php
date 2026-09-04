<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pendaftaran;
use App\Models\Setting;
use App\Models\Pembekalan;
use App\Models\PembekalanPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

        // 2. Cek apakah ada Agenda Pembekalan & apakah mahasiswa sudah Presensi Hadir
        $latestPembekalan = Pembekalan::latest()->first();
        $sudahPembekalan = true; // Default true jika admin belum pernah membuat agenda sama sekali

        if ($latestPembekalan) {
            $cekPresensi = PembekalanPresensi::where('pembekalan_id', $latestPembekalan->id)
                ->where('user_id', $user->id)
                ->where('is_hadir', true)
                ->exists();
            
            if (!$cekPresensi) {
                $sudahPembekalan = false;
            }
        }

        // 3. Kunci Absensi jika belum diterima magang ATAU belum ikut pembekalan
        $isLocked = !$pendaftaran || !$sudahPembekalan;

        // 4. Ambil parameter target jam dari Setting Global (Default: 900 Jam)
        $targetJam = (int) Setting::getByKey('min_jam_magang', 900);

        // 5. Hitung total jam yang telah diapprove dosen
        $jamTercapai = Absensi::where('user_id', $user->id)
            ->where('status_verifikasi', 'approved')
            ->sum('jam_diperoleh');

        $sisaJam = max(0, $targetJam - $jamTercapai);
        $persentase = $targetJam > 0 ? min(100, round(($jamTercapai / $targetJam) * 100, 1)) : 0;

        // 6. Data absensi hari ini
        $today = now()->toDateString();
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // 7. Riwayat Absensi
        $riwayats = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.absensi.index', compact(
            'pendaftaran',
            'isLocked',
            'sudahPembekalan', // Variabel baru untuk blade
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

        // Proteksi Backend 1: Cek Pendaftaran Aktif
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima pada program magang aktif.');
        }

        // Proteksi Backend 2: Cek Kehadiran Pembekalan
        $latestPembekalan = Pembekalan::latest()->first();
        if ($latestPembekalan) {
            $cekPresensi = PembekalanPresensi::where('pembekalan_id', $latestPembekalan->id)
                ->where('user_id', $user->id)
                ->where('is_hadir', true)
                ->exists();
                
            if (!$cekPresensi) {
                return redirect()->back()->with('error', 'Akses Ditolak. Anda wajib melakukan konfirmasi kehadiran pada menu Pembekalan Magang terlebih dahulu.');
            }
        }

        $today = now()->toDateString();

        $request->validate([
            'tipe'         => 'required|in:masuk,pulang',
            'image'        => 'required|string', 
            'latitude'     => 'nullable|string',
            'longitude'    => 'nullable|string',
            'waktu_lokal'  => 'required|string', // <-- Menangkap waktu lokal dari frontend
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

        // Ambil status Mode Testing dari Setting Global (Default: 'true')
        $isTestingMode = Setting::getByKey('mode_testing', 'true') === 'true';

        // Gunakan Waktu Lokal yang dikirim perangkat mahasiswa
        $waktuAbsen = $request->waktu_lokal; 

        if ($request->tipe === 'masuk') {
            $absensi->waktu_masuk     = $waktuAbsen; // <-- Pakai waktu lokal
            $absensi->foto_masuk      = $fileName;
            $absensi->latitude_masuk  = $request->latitude;
            $absensi->longitude_masuk = $request->longitude;
        } else {
            // PROSES ABSEN PULANG
            if (!$absensi->waktu_masuk) {
                return redirect()->back()->with('error', 'Gagal Absen Pulang: Anda belum melakukan Absen Masuk hari ini.');
            }

            // KONTROL VALIDASI MODE FINAL VS TESTING
            if (!$isTestingMode) {
                // Konversi string waktu lokal menjadi objek Carbon agar bisa dihitung
                $waktuMasukObj = Carbon::createFromFormat('H:i:s', $absensi->waktu_masuk);
                $waktuPulangObj = Carbon::createFromFormat('H:i:s', $waktuAbsen);
                
                $selisihMenit = $waktuPulangObj->diffInMinutes($waktuMasukObj);
                $selisihJam = round($selisihMenit / 60, 1);

                // Di Mode Final/Produksi: Harus minimal 8 jam (480 menit) setelah absen masuk
                if ($selisihMenit < 480) {
                    $sisaMenit = 480 - $selisihMenit;
                    $sisaJam = floor($sisaMenit / 60);
                    $sisaMenitSisa = $sisaMenit % 60;

                    $pesanSisa = $sisaJam > 0 
                        ? "{$sisaJam} jam {$sisaMenitSisa} menit" 
                        : "{$sisaMenitSisa} menit";

                    return redirect()->back()->with('error', "Absen pulang belum dapat dilakukan. Durasi kerja Anda baru {$selisihJam} jam. Minimal durasi kerja adalah 8 jam (Harus menunggu {$pesanSisa} lagi).");
                }
            }

            // Jika lulus validasi (atau sedang di Mode Testing)
            $absensi->waktu_pulang     = $waktuAbsen; // <-- Pakai waktu lokal
            $absensi->foto_pulang      = $fileName;
            $absensi->latitude_pulang  = $request->latitude;
            $absensi->longitude_pulang = $request->longitude;
            $absensi->jam_diperoleh    = 8; // Mengalokasikan 8 jam
        }

        $absensi->save();

        $pesanMode = $isTestingMode ? ' [MODE TESTING]' : '';
        return redirect()->back()->with('success', 'Absensi ' . ucfirst($request->tipe) . ' berhasil dicatat' . $pesanMode . '.');
    }

    /**
     * Pengajuan Izin / Sakit
     */
    public function storeIzin(Request $request)
    {
        $user = Auth::user();

        // Proteksi Backend 1: Cek Pendaftaran Aktif
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda belum diterima pada program magang aktif.');
        }

        // Proteksi Backend 2: Cek Kehadiran Pembekalan
        $latestPembekalan = Pembekalan::latest()->first();
        if ($latestPembekalan) {
            $cekPresensi = PembekalanPresensi::where('pembekalan_id', $latestPembekalan->id)
                ->where('user_id', $user->id)
                ->where('is_hadir', true)
                ->exists();
                
            if (!$cekPresensi) {
                return redirect()->back()->with('error', 'Akses Ditolak. Anda wajib melakukan konfirmasi kehadiran pada menu Pembekalan Magang terlebih dahulu.');
            }
        }

        $today = now()->toDateString();

        $request->validate([
            'tipe_kehadiran' => 'required|in:izin,sakit',
            'alasan_izin'    => 'required|string',
            'surat_izin'     => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // Maksimal 10MB
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