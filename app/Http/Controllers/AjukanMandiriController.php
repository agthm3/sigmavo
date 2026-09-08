<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Perusahaan;
use App\Models\Lowongan;
use App\Models\Setting;
use App\Models\User;
use App\Models\SpvProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        // Cek batas maksimal pengajuan
        $maxPengajuan = (int) Setting::getByKey('max_pengajuan', 3);
        $totalPengajuan = Pendaftaran::where('user_id', $user->id)->count();

        // Ambil data perusahaan untuk Dropdown Search Mahasiswa
        $perusahaans = Perusahaan::select('id', 'nama_perusahaan', 'sektor_industri', 'alamat', 'website')
            ->orderBy('nama_perusahaan', 'asc')
            ->get();

        return view('dashboard.ajukan-mandiri.index', compact('pendaftaranAktif', 'maxPengajuan', 'totalPengajuan', 'user', 'perusahaans'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // 1. Proteksi Batas Maksimal
            $maxPengajuan = (int) Setting::getByKey('max_pengajuan', 3);
            $totalPengajuan = Pendaftaran::where('user_id', $user->id)->count();

            if ($totalPengajuan >= $maxPengajuan) {
                return response()->json(['message' => "Anda telah mencapai batas maksimal pengajuan magang ({$maxPengajuan} kali)."], 422);
            }

            // 2. Proteksi Pengajuan Aktif
            $pendaftaranAktif = Pendaftaran::where('user_id', $user->id)
                ->whereIn('status_seleksi', ['menunggu', 'wawancara', 'diterima'])
                ->first();

            if ($pendaftaranAktif) {
                return response()->json(['message' => 'Anda masih memiliki pengajuan magang yang aktif/sedang diproses.'], 422);
            }

            // 3. Validasi Form
            $request->validate([
                'perusahaan_id'        => 'nullable|string',
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

            $suratPath = null;
            if ($request->hasFile('surat_balasan')) {
                $suratPath = $request->file('surat_balasan')->store('surat_balasan', 'public');
            }

            // 4. PENENTUAN PERUSAHAAN
            $cleanedNama = trim($request->nama_instansi);
            $perusahaan = null;

            if ($request->filled('perusahaan_id') && is_numeric($request->perusahaan_id)) {
                $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
            } else {
                $perusahaan = Perusahaan::whereRaw('LOWER(TRIM(nama_perusahaan)) = ?', [strtolower($cleanedNama)])->first();

                if (!$perusahaan) {
                    $perusahaanData = [
                        'nama_perusahaan'  => $cleanedNama,
                        'sektor_industri'  => $request->sektor_industri,
                        'website'          => $request->website_instansi,
                        'alamat'           => $request->alamat_instansi,
                        'status_kerjasama' => 'Mandiri Partner',
                    ];

                    if (Schema::hasColumn('perusahaans', 'email_hrd')) $perusahaanData['email_hrd'] = $request->email_supervisor;
                    if (Schema::hasColumn('perusahaans', 'email')) $perusahaanData['email'] = $request->email_supervisor;
                    if (Schema::hasColumn('perusahaans', 'telepon')) $perusahaanData['telepon'] = $request->no_hp_supervisor;
                    elseif (Schema::hasColumn('perusahaans', 'no_hp')) $perusahaanData['no_hp'] = $request->no_hp_supervisor;

                    $perusahaan = Perusahaan::create($perusahaanData);
                }
            }

            // 5. AUTO-CREATE / AMBIL AKUN SPV
            $spvUser = User::where('email', $request->email_supervisor)->first();

            if (!$spvUser) {
                $rawPassword = 'Spv-' . rand(1000, 9999);
                $spvUser = User::create([
                    'name'          => trim($request->nama_supervisor),
                    'email'         => trim($request->email_supervisor),
                    'password'      => Hash::make($rawPassword),
                    'temp_password' => $rawPassword,
                    'is_active'     => true,
                ]);

                if (method_exists($spvUser, 'assignRole')) {
                    $spvUser->assignRole('spv');
                }

                SpvProfile::create([
                    'user_id'       => $spvUser->id,
                    'perusahaan_id' => $perusahaan->id,
                    'jabatan'       => trim($request->jabatan_supervisor),
                    'no_hp'         => trim($request->no_hp_supervisor),
                ]);
            } else {
                if (method_exists($spvUser, 'hasRole') && !$spvUser->hasRole('spv')) {
                    $spvUser->assignRole('spv');
                }

                SpvProfile::firstOrCreate(
                    ['user_id' => $spvUser->id],
                    [
                        'perusahaan_id' => $perusahaan->id,
                        'jabatan'       => trim($request->jabatan_supervisor),
                        'no_hp'         => trim($request->no_hp_supervisor),
                    ]
                );
            }

            // 6. AUTO-CREATE LOWONGAN MANDIRI (DITAUTKAN KE spv_id)
            $sampleLowongan = Lowongan::latest()->first();
            $validStatus = $sampleLowongan ? $sampleLowongan->status : 'published';

            $lowonganMandiri = Lowongan::firstOrCreate(
                [
                    'perusahaan_id' => $perusahaan->id,
                    'judul_posisi'  => trim($request->posisi),
                ],
                [
                    'spv_id'             => $spvUser->id, // KUNCI KE AKUN SPV
                    'deskripsi'          => 'Lowongan Otomatis - Jalur Magang Mandiri',
                    'kualifikasi'        => 'Khusus Pengajuan Mandiri',
                    'tipe_magang'        => 'mandiri',
                    'kuota'              => 1,
                    'status'             => $validStatus,
                    'batas_pendaftaran'  => $request->tanggal_selesai ?? now()->addMonths(6)->toDateString(),
                ]
            );

            // Jika lowongan sudah ada sebelumnya tapi spv_id masih null, perbarui sekarang
            if (empty($lowonganMandiri->spv_id)) {
                $lowonganMandiri->update(['spv_id' => $spvUser->id]);
            }

            // 7. SIMPAN PENDAFTARAN
            Pendaftaran::create([
                'user_id'               => $user->id,
                'lowongan_id'           => $lowonganMandiri->id,
                'jalur_magang'          => 'mandiri',
                'nama_instansi_mandiri' => $perusahaan->nama_perusahaan,
                'divisi_mandiri'        => trim($request->posisi),
                'status_seleksi'        => 'menunggu',
                'tgl_mulai_magang'      => $request->tanggal_mulai,
                'tgl_selesai_magang'    => $request->tanggal_selesai,
                'status_surat'          => 'menunggu',
                'surat_balasan'         => $suratPath,
                'catatan_seleksi'       => "Detail Jobdesc: " . $request->jobdesc . " | Supervisor: " . $request->nama_supervisor . " (" . $request->jabatan_supervisor . " - " . $request->no_hp_supervisor . " / " . $request->email_supervisor . ")",
            ]);

            DB::commit();

            session()->flash('success', 'Pengajuan magang mandiri berhasil dikirim! Silakan pantau status verifikasi.');

            return response()->json([
                'status'       => 'success',
                'message'      => 'Pengajuan magang mandiri berhasil dikirim.',
                'redirect_url' => route('dashboard-mahasiswa-status-pengajuan')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->first()[0] ?? 'Data form tidak valid.'
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}