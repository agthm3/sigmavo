<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\Prodi;
use App\Models\User;
use App\Models\SpvProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ListingProgramController extends Controller
{
    public function index(Request $request)
    {
        // Tambahkan 'spv' ke dalam eager loading
        $query = Lowongan::with(['perusahaan', 'prodi', 'spv'])
            ->withCount([
                'pendaftarans as total_pelamar',
                'pendaftarans as total_diterima' => function ($q) {
                    $q->where('status_seleksi', 'diterima');
                }
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_posisi', 'like', "%{$search}%")
                  ->orWhereHas('perusahaan', fn($p) => $p->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhereHas('prodi', fn($pr) => $pr->where('nama_prodi', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('mode_kerja') && $request->mode_kerja !== 'semua') {
            $query->where('mode_kerja', $request->mode_kerja);
        }

        $lowongans = $query->latest()->paginate(10)->withQueryString();
        $perusahaans = Perusahaan::all();
        $prodis = Prodi::all();

        $totalLowongan = Lowongan::count();
        $totalPublished = Lowongan::where('status', 'published')->count();
        $totalKuota = Lowongan::sum('kuota');
        $totalDraftClosed = Lowongan::whereIn('status', ['draft', 'closed'])->count();

        return view('dashboard.listing-program.index', compact(
            'lowongans', 'perusahaans', 'prodis', 
            'totalLowongan', 'totalPublished', 'totalKuota', 'totalDraftClosed'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perusahaan_id'     => 'required|exists:perusahaans,id',
            'judul_posisi'      => 'required|string|max:255',
            'prodi_id'          => 'nullable',
            'mode_kerja'        => 'required|in:WFO,Hybrid,WFH',
            'kuota'             => 'required|integer|min:1',
            'batas_pendaftaran' => 'required|date',
            'durasi'            => 'nullable|string',
            'deskripsi'         => 'required|string',
            'status'            => 'required|in:published,draft,closed',
            
            // Inputan Baru untuk SPV
            'spv_name'          => 'required|string|max:255',
            'spv_email'         => 'required|email',
            'spv_no_hp'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. AUTO-CREATE / AMBIL AKUN SPV
            $spvId = $this->handleSpvAccount($request->spv_name, $request->spv_email, $request->spv_no_hp, $request->perusahaan_id, $passwordBaru);

            // 2. BUAT LOWONGAN
            Lowongan::create([
                'perusahaan_id'     => $request->perusahaan_id,
                'spv_id'            => $spvId, // Tautkan SPV ke Lowongan
                'prodi_id'          => $request->prodi_id === 'all' ? null : $request->prodi_id,
                'judul_posisi'      => $request->judul_posisi,
                'mode_kerja'        => $request->mode_kerja,
                'kuota'             => $request->kuota,
                'batas_pendaftaran' => $request->batas_pendaftaran,
                'durasi'            => $request->durasi ?? '6 Bulan',
                'deskripsi'         => $request->deskripsi,
                'status'            => $request->status,
            ]);

            DB::commit();

            if ($passwordBaru) {
                return redirect()->back()->with('success_spv', [
                    'message' => 'Lowongan magang baru berhasil dibuat beserta Akun SPV-nya.',
                    'email' => $request->spv_email,
                    'password' => $passwordBaru
                ]);
            }

            return redirect()->back()->with('success', 'Lowongan magang berhasil dibuat. Akun SPV sudah ditautkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat lowongan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $lowongan = Lowongan::findOrFail($id);

        $request->validate([
            'perusahaan_id'     => 'required|exists:perusahaans,id',
            'judul_posisi'      => 'required|string|max:255',
            'prodi_id'          => 'nullable',
            'mode_kerja'        => 'required|in:WFO,Hybrid,WFH',
            'kuota'             => 'required|integer|min:1',
            'batas_pendaftaran' => 'required|date',
            'durasi'            => 'nullable|string',
            'deskripsi'         => 'required|string',
            'status'            => 'required|in:published,draft,closed',
            
            'spv_name'          => 'required|string|max:255',
            'spv_email'         => 'required|email',
            'spv_no_hp'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update / Cek SPV
            $spvId = $this->handleSpvAccount($request->spv_name, $request->spv_email, $request->spv_no_hp, $request->perusahaan_id, $passwordBaru);

            $lowongan->update([
                'perusahaan_id'     => $request->perusahaan_id,
                'spv_id'            => $spvId,
                'prodi_id'          => $request->prodi_id === 'all' ? null : $request->prodi_id,
                'judul_posisi'      => $request->judul_posisi,
                'mode_kerja'        => $request->mode_kerja,
                'kuota'             => $request->kuota,
                'batas_pendaftaran' => $request->batas_pendaftaran,
                'durasi'            => $request->durasi ?? '6 Bulan',
                'deskripsi'         => $request->deskripsi,
                'status'            => $request->status,
            ]);

            DB::commit();

            if ($passwordBaru) {
                return redirect()->back()->with('success_spv', [
                    'message' => "Lowongan '{$lowongan->judul_posisi}' berhasil diperbarui beserta pembuatan Akun SPV baru.",
                    'email' => $request->spv_email,
                    'password' => $passwordBaru
                ]);
            }

            return redirect()->back()->with('success', "Lowongan '{$lowongan->judul_posisi}' berhasil diperbarui.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui lowongan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->delete();
        return redirect()->back()->with('success', 'Lowongan magang berhasil dihapus.');
    }

    public function togglePublish($id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->status = $lowongan->status === 'published' ? 'draft' : 'published';
        $lowongan->save();
        $msg = $lowongan->status === 'published' ? 'dipublikasikan' : 'dijadikan draft';
        return redirect()->back()->with('success', "Status lowongan berhasil {$msg}.");
    }

    /**
     * Helper Function: Membuat atau Mencari Akun SPV
     */
    private function handleSpvAccount($name, $email, $phone, $perusahaanId, &$passwordBaru)
    {
        $existingUser = User::where('email', $email)->first();
        $passwordBaru = null;

        if ($existingUser) {
            // Jika akun sudah ada, pastikan punya role SPV & lengkapi profile
            if (!$existingUser->hasRole('spv')) {
                $existingUser->assignRole('spv');
            }
            SpvProfile::firstOrCreate(
                ['user_id' => $existingUser->id],
                ['perusahaan_id' => $perusahaanId, 'no_hp' => $phone, 'jabatan' => 'Supervisor Lapangan']
            );
            // Update nama jika diperlukan (opsional)
            $existingUser->update(['name' => $name]);
            return $existingUser->id;
        } else {
            // Buat akun baru jika belum ada
            $passwordRaw = 'Spv-' . rand(1000, 9999);
            $passwordBaru = $passwordRaw; // Untuk dilempar ke session alert

            $newUser = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($passwordRaw),
                'temp_password' => $passwordRaw,
                'is_active' => true,
            ]);
            $newUser->assignRole('spv');

            SpvProfile::create([
                'user_id'       => $newUser->id,
                'perusahaan_id' => $perusahaanId,
                'no_hp'         => $phone,
                'jabatan'       => 'Supervisor Lapangan'
            ]);

            return $newUser->id;
        }
    }
}