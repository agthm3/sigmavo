<?php

namespace App\Http\Controllers;

use App\Models\PembekalanPresensi;
use App\Models\Pendaftaran;
use App\Models\Seminar;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SeminarController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil daftar dosen untuk dropdown modal admin (mencegah undefined variable)
        $dosens = User::role(['dosen', 'admin_prodi', 'admin'])->get();

        // JIKA ADMIN / SUPERADMIN / ADMIN PRODI
        if ($user->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            $queryMhs = User::role('mahasiswa')->with([
                'mahasiswaProfile.prodi',
                'seminars.pembimbing',
                'seminars.penguji1',
                'seminars.penguji2'
            ]);

            // Filter scoping prodi jika admin prodi
            if ($user->hasRole('admin_prodi') && $user->adminProdiProfile?->prodi_id) {
                $prodiId = $user->adminProdiProfile->prodi_id;
                $queryMhs->whereHas('mahasiswaProfile', fn($q) => $q->where('prodi_id', $prodiId));
            }

            // Filter Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $queryMhs->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('mahasiswaProfile', fn($p) => $p->where('nim', 'like', "%{$search}%"));
                });
            }

            $mahasiswas = $queryMhs->paginate(10)->withQueryString();

            return view('dashboard.seminar.index', compact('user', 'mahasiswas', 'dosens'));
        }

        // JIKA MAHASISWA
        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        $targetJam = (int) Setting::getByKey('min_jam_magang', 900);
        $jamTercapai = $pendaftaran ? 900 : 0; 

        $sudahPembekalan = PembekalanPresensi::where('user_id', $user->id)
            ->where('is_hadir', true)
            ->exists();

        $jamMemenuhi = $jamTercapai >= $targetJam;
        $layakSeminar = $sudahPembekalan && $jamMemenuhi;

        $seminar = Seminar::with(['pembimbing', 'penguji1', 'penguji2'])
            ->where('user_id', $user->id)
            ->first();

        return view('dashboard.seminar.index', compact(
            'pendaftaran',
            'targetJam',
            'jamTercapai',
            'sudahPembekalan',
            'jamMemenuhi',
            'layakSeminar',
            'seminar',
            'dosens',
            'user'
        ));
    }

    /**
     * Upload File Presentasi / Daftar Seminar (Mahasiswa)
     */
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        $request->validate([
            'file_ppt' => 'required|file|mimes:ppt,pptx,pdf|max:10240',
        ]);

        $seminar = Seminar::firstOrNew(['user_id' => $user->id]);

        if ($request->hasFile('file_ppt')) {
            if ($seminar->file_ppt && Storage::disk('public')->exists($seminar->file_ppt)) {
                Storage::disk('public')->delete($seminar->file_ppt);
            }
            $seminar->file_ppt = $request->file('file_ppt')->store('seminar_ppt', 'public');
        }

        $seminar->pendaftaran_id = $pendaftaran?->id;
        $seminar->pembimbing_id  = $pendaftaran?->dosen_id;
        if ($seminar->status_seminar === 'belum_daftar') {
            $seminar->status_seminar = 'mengajukan';
        }
        $seminar->save();

        return redirect()->back()->with('success', 'Bahan presentasi berhasil diunggah.');
    }

    /**
     * Set Jadwal, Dewan Penguji & Nilai oleh Admin / Admin Prodi
     */
    public function setJadwalAdmin(Request $request, $userId)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses.');
        }

        $request->validate([
            'waktu_seminar'  => 'required|date',
            'lokasi_ruangan' => 'required|string|max:255',
            'pembimbing_id'  => 'nullable|exists:users,id',
            'penguji_1_id'   => 'nullable|exists:users,id',
            'penguji_2_id'   => 'nullable|exists:users,id',
            'status_seminar' => 'required|in:mengajukan,dijadwalkan,selesai,ditolak',
            'nilai_akhir'    => 'nullable|numeric|min:0|max:100',
        ]);

        $seminar = Seminar::firstOrNew(['user_id' => $userId]);

        $seminar->waktu_seminar  = $request->waktu_seminar;
        $seminar->lokasi_ruangan = $request->lokasi_ruangan;
        $seminar->pembimbing_id  = $request->pembimbing_id;
        $seminar->penguji_1_id   = $request->penguji_1_id;
        $seminar->penguji_2_id   = $request->penguji_2_id;
        $seminar->status_seminar = $request->status_seminar;
        $seminar->nilai_akhir    = $request->nilai_akhir;
        $seminar->save();

        return redirect()->back()->with('success', 'Jadwal seminar dan dewan penguji berhasil diperbarui.');
    }

    /**
     * Hapus berkas PPT
     */
    public function destroyPpt()
    {
        $seminar = Seminar::where('user_id', Auth::id())->firstOrFail();

        if ($seminar->file_ppt && Storage::disk('public')->exists($seminar->file_ppt)) {
            Storage::disk('public')->delete($seminar->file_ppt);
        }

        $seminar->file_ppt = null;
        $seminar->save();

        return redirect()->back()->with('success', 'File presentasi berhasil dihapus.');
    }
}