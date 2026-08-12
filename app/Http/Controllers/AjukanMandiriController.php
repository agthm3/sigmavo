<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Perusahaan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AjukanMandiriController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek pendaftaran aktif
        $pendaftaranAktif = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status_seleksi', ['menunggu', 'wawancara', 'diterima'])
            ->first();

        // Cek batas maksimal pengajuan dari setting global (default 3)
        $maxPengajuan = (int) Setting::getByKey('max_pengajuan', 3);
        $totalPengajuan = Pendaftaran::where('user_id', $user->id)->count();

        return view('dashboard.ajukan-mandiri.index', compact('pendaftaranAktif', 'maxPengajuan', 'totalPengajuan', 'user'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            // 1. Proteksi Batas Maksimal Pengajuan
            $maxPengajuan = (int) Setting::getByKey('max_pengajuan', 3);
            $totalPengajuan = Pendaftaran::where('user_id', $user->id)->count();

            if ($totalPengajuan >= $maxPengajuan) {
                return response()->json([
                    'message' => "Anda telah mencapai batas maksimal pengajuan magang ({$maxPengajuan} kali)."
                ], 422);
            }

            // 2. Proteksi Pengajuan Aktif yang Sedang Diproses
            $pendaftaranAktif = Pendaftaran::where('user_id', $user->id)
                ->whereIn('status_seleksi', ['menunggu', 'wawancara', 'diterima'])
                ->first();

            if ($pendaftaranAktif) {
                return response()->json([
                    'message' => 'Anda masih memiliki pengajuan magang yang aktif/sedang diproses.'
                ], 422);
            }

            // 3. Validasi Input Form
            $request->validate([
                'nama_instansi'        => 'required|string|max:255',
                'sektor_industri'      => 'required|string|max:255',
                'website_instansi'     => 'nullable|url|max:255',
                'alamat_instansi'      => 'required|string',
                'posisi'               => 'required|string|max:255',
                'tanggal_mulai'        => 'required|date',
                'tanggal_selesai'      => 'required|date|after:tanggal_mulai',
                'jobdesc'              => 'required|string',
                'nama_supervisor'      => 'required|string|max:255',
                'jabatan_supervisor'   => 'required|string|max:255',
                'email_supervisor'     => 'required|email|max:255',
                'no_hp_supervisor'     => 'required|string|max:50',
                'surat_balasan'        => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            ]);

            // 4. Simpan File Surat Balasan
            $suratPath = null;
            if ($request->hasFile('surat_balasan')) {
                $suratPath = $request->file('surat_balasan')->store('surat_balasan', 'public');
            }

            // 5. Simpan / Dapatkan Data Perusahaan Mitra
            $perusahaanData = [
                'sektor_industri' => $request->sektor_industri,
                'website'         => $request->website_instansi,
                'alamat'          => $request->alamat_instansi,
            ];

            if (Schema::hasColumn('perusahaans', 'email_hrd')) {
                $perusahaanData['email_hrd'] = $request->email_supervisor;
            }
            if (Schema::hasColumn('perusahaans', 'email')) {
                $perusahaanData['email'] = $request->email_supervisor;
            }
            if (Schema::hasColumn('perusahaans', 'telepon')) {
                $perusahaanData['telepon'] = $request->no_hp_supervisor;
            } elseif (Schema::hasColumn('perusahaans', 'no_hp')) {
                $perusahaanData['no_hp'] = $request->no_hp_supervisor;
            }

            Perusahaan::firstOrCreate(
                ['nama_perusahaan' => $request->nama_instansi],
                $perusahaanData
            );

            // 6. Simpan Pendaftaran Mandiri ke Tabel pendaftarans (Sesuai DDL MySQL)
            Pendaftaran::create([
                'user_id'               => $user->id,
                'lowongan_id'           => null, // Nullable di DB
                'jalur_magang'          => 'mandiri', // ENUM: reguler, mandiri
                'nama_instansi_mandiri' => $request->nama_instansi,
                'divisi_mandiri'        => $request->posisi,
                'status_seleksi'        => 'menunggu', // ENUM: menunggu, diterima, ditolak, wawancara
                'tgl_mulai_magang'      => $request->tanggal_mulai,
                'tgl_selesai_magang'    => $request->tanggal_selesai,
                'status_surat'          => 'menunggu',
                'surat_balasan'         => $suratPath,
                'catatan_seleksi'       => "Detail Jobdesc: " . $request->jobdesc . " | Supervisor: " . $request->nama_supervisor . " (" . $request->jabatan_supervisor . " - " . $request->no_hp_supervisor . " / " . $request->email_supervisor . ")",
            ]);

            session()->flash('success', 'Pengajuan magang mandiri berhasil dikirim! Silakan pantau status verifikasi.');

            return response()->json([
                'status'       => 'success',
                'message'      => 'Pengajuan magang mandiri berhasil dikirim.',
                'redirect_url' => route('dashboard-mahasiswa-status-pengajuan')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->first()[0] ?? 'Data form tidak valid.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}