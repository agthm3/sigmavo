<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\SpvProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SeleksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['user.mahasiswaProfile.prodi', 'lowongan.perusahaan', 'dosen']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($mp) => $mp->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan', fn($l) => $l->where('judul_posisi', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('lowongan_id') && $request->lowongan_id !== 'semua') {
            $query->where('lowongan_id', $request->lowongan_id);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_seleksi', $request->status);
        }

        $pendaftarans = $query->latest()->paginate(10)->withQueryString();

        $lowongans = Lowongan::with('perusahaan')->get();
        $dosens = User::role('dosen')->get();

        $totalPelamar = Pendaftaran::count();
        $totalMenunggu = Pendaftaran::where('status_seleksi', 'menunggu')->count();
        $totalDiterima = Pendaftaran::where('status_seleksi', 'diterima')->count();
        $totalDitolak = Pendaftaran::where('status_seleksi', 'ditolak')->count();

        return view('dashboard.seleksi.index', compact(
            'pendaftarans', 'lowongans', 'dosens', 'totalPelamar', 'totalMenunggu', 'totalDiterima', 'totalDitolak'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::with(['lowongan.perusahaan', 'user'])->findOrFail($id);

        $request->validate([
            'status_seleksi'  => 'required|in:menunggu,diterima,ditolak,wawancara',
            'dosen_id'        => 'nullable|exists:users,id',
            'catatan_seleksi' => 'nullable|string',
        ]);

        $statusLama = $pendaftaran->status_seleksi;
        
        $pendaftaran->update([
            'status_seleksi'  => $request->status_seleksi,
            'dosen_id'        => $request->dosen_id,
            'catatan_seleksi' => $request->catatan_seleksi,
        ]);

        // TRIGGER AUTO-CREATE SPV (Hanya saat status mandiri berubah jadi diterima)
        if ($request->status_seleksi === 'diterima' && $statusLama !== 'diterima') {
            $pendaftaran->lowongan?->increment('kuota_terisi');

            if ($pendaftaran->jalur_magang === 'mandiri') {
                $catatan = $pendaftaran->catatan_seleksi ?? '';
                
                // Regex lebih tangguh (mengabaikan spasi berlebih)
                preg_match('/Supervisor:\s*(.*?)\s*\(/i', $catatan, $matchName);
                preg_match('/-\s*(.*?)\s*\//', $catatan, $matchPhone);
                preg_match('/\/\s*(.*?)\)/', $catatan, $matchEmail);

                $spvName  = !empty($matchName[1]) ? trim($matchName[1]) : ('Supervisor ' . $pendaftaran->nama_instansi_mandiri);
                $spvPhone = !empty($matchPhone[1]) ? trim($matchPhone[1]) : '-';
                $spvEmail = !empty($matchEmail[1]) ? trim($matchEmail[1]) : null;

                // Fallback jika dari catatan tidak ada, ambil dari master perusahaan
                if (!$spvEmail && $pendaftaran->lowongan?->perusahaan) {
                    $spvEmail = $pendaftaran->lowongan->perusahaan->email_hrd;
                }

                // Jika Email Ditemukan, Eksekusi Auto-Create
                if ($spvEmail) {
                    $existingUser = User::where('email', $spvEmail)->first();

                    if ($existingUser) {
                        if (!$existingUser->hasRole('spv')) {
                            $existingUser->assignRole('spv');
                        }
                        SpvProfile::firstOrCreate(
                            ['user_id' => $existingUser->id],
                            ['perusahaan_id' => $pendaftaran->lowongan->perusahaan_id, 'no_hp' => $spvPhone]
                        );

                        return redirect()->route('dashboard-seleksi-berhasil-mandiri')->with('spvData', [
                            'status'    => 'linked',
                            'name'      => $existingUser->name,
                            'email'     => $existingUser->email,
                            'mahasiswa' => $pendaftaran->user->name,
                            'instansi'  => $pendaftaran->lowongan->perusahaan->nama_perusahaan,
                        ]);
                    } else {
                        $passwordRandom = 'Spv-' . rand(1000, 9999);
                        $newUser = User::create([
                            'name'     => $spvName,
                            'email'    => $spvEmail,
                            'password' => Hash::make($passwordRandom),
                        ]);
                        $newUser->assignRole('spv');

                        SpvProfile::create([
                            'user_id'       => $newUser->id,
                            'perusahaan_id' => $pendaftaran->lowongan->perusahaan_id,
                            'no_hp'         => $spvPhone,
                        ]);

                        return redirect()->route('dashboard-seleksi-berhasil-mandiri')->with('spvData', [
                            'status'    => 'created',
                            'name'      => $newUser->name,
                            'email'     => $newUser->email,
                            'password'  => $passwordRandom,
                            'mahasiswa' => $pendaftaran->user->name,
                            'instansi'  => $pendaftaran->lowongan->perusahaan->nama_perusahaan,
                        ]);
                    }
                } else {
                    // JIKA EMAIL GAGAL DITEMUKAN SAMA SEKALI
                    return redirect()->back()->with('success', 'Keputusan berhasil disimpan. NAMUN, Akun SPV gagal dibuat karena email supervisor tidak ditemukan di catatan maupun master instansi.');
                }
            }
        }

        return redirect()->back()->with('success', 'Keputusan seleksi berhasil disimpan.');
    }

    public function successMandiri()
    {
        if (!session('spvData')) {
            return redirect()->route('dashboard-daftar-lowongan-seleksi');
        }
        return view('dashboard.seleksi.success-mandiri');
    }
}