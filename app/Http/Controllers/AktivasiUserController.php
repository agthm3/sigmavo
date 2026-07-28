<?php

namespace App\Http\Controllers;

use App\Models\AdminProdiProfile;
use App\Models\DosenProfile;
use App\Models\MahasiswaProfile;
use App\Models\MitraProfile;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AktivasiUserController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Query Utama User
        $query = User::with([
            'roles', 
            'mahasiswaProfile.prodi', 
            'dosenProfile.prodi', 
            'adminProdiProfile.prodi',
            'mitraProfile'
        ]);

        // ==========================================
        // LOGIC FILTERING PRODI (Dua Tingkat Akses)
        // ==========================================
        if ($currentUser->hasRole('admin_prodi')) {
            // Jika yang login adalah Admin Prodi -> BATASI HANYA PRODI DIA SENDEDIRI
            $prodiId = $currentUser->prodi_id;

            $query->where(function ($q) use ($prodiId) {
                // Filter Mahasiswa di prodi tersebut
                $q->whereHas('mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId))
                  // Atau Dosen di prodi tersebut
                  ->orWhereHas('dosenProfile', fn($d) => $d->where('prodi_id', $prodiId));
            });
        } elseif ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
            // Jika yang login adalah Superadmin/Admin Fakultas & Memilih Filter Prodi tertentu
            $prodiId = $request->prodi_id;

            $query->where(function ($q) use ($prodiId) {
                $q->whereHas('mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId))
                  ->orWhereHas('dosenProfile', fn($d) => $d->where('prodi_id', $prodiId))
                  ->orWhereHas('adminProdiProfile', fn($a) => $a->where('prodi_id', $prodiId));
            });
        }

        // Filter Berdasarkan Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        // Filter Berdasarkan Role
        if ($request->filled('role') && $request->role !== 'semua') {
            $role = $request->role;
            $query->whereHas('roles', fn($r) => $r->where('name', $role));
        }

        // Pencarian Nama/Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $prodis = Prodi::all();

        return view('dashboard.manajemen-akun.aktivasi-user', compact('users', 'prodis', 'currentUser'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Safety Check: Admin prodi tidak boleh nonaktifkan sesama admin/superadmin
        if (Auth::user()->hasRole('admin_prodi') && ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang mengubah akun ini.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusMessage = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusMessage}.");
    }
    public function store(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Safety Check: Hanya Admin, Superadmin, atau Admin Prodi yang berhak
        if (!$currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menambah user.');
        }

        // 1. Validasi Input Dasar
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
            'role'      => 'required|string|exists:roles,name',
            'prodi_id'  => 'nullable|exists:prodis,id',
            // Validasi Kondisional Spesifik
            'nim'       => 'nullable|required_if:role,mahasiswa|string|unique:mahasiswa_profiles,nim',
            'angkatan'  => 'nullable|required_if:role,mahasiswa|string|max:4',
            'nip_nidn'  => 'nullable|required_if:role,dosen,admin_prodi|string',
            'perusahaan' => 'nullable|required_if:role,mitra|string',
        ]);

        // Jika yang login Admin Prodi, kunci prodi_id sesuai prodi tempat dia bertugas
        $prodiId = $currentUser->hasRole('admin_prodi') 
            ? $currentUser->prodi_id 
            : $request->prodi_id;

        // 2. Buat User Utama
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true, // User bentukan admin langsung aktif
        ]);

        // 3. Assign Role Spatie
        $user->assignRole($request->role);

        // 4. Buat Profil Spesifik Sesuai Role
        switch ($request->role) {
            case 'mahasiswa':
                MahasiswaProfile::create([
                    'user_id'  => $user->id,
                    'prodi_id' => $prodiId,
                    'nim'      => $request->nim,
                    'angkatan' => $request->angkatan ?? date('Y'),
                    'no_hp'    => $request->no_hp,
                ]);
                break;

            case 'dosen':
                DosenProfile::create([
                    'user_id'    => $user->id,
                    'prodi_id'   => $prodiId,
                    'nip_nidn'   => $request->nip_nidn,
                    'departemen' => $request->departemen ?? 'Vokasi',
                    'no_hp'      => $request->no_hp,
                ]);
                break;

            case 'admin_prodi':
                AdminProdiProfile::create([
                    'user_id'  => $user->id,
                    'prodi_id' => $prodiId,
                    'nip_nidn' => $request->nip_nidn,
                ]);
                break;

            case 'mitra':
                MitraProfile::create([
                    'user_id'         => $user->id,
                    'nama_perusahaan' => $request->perusahaan,
                    'jabatan'         => $request->jabatan ?? 'HR / Representative',
                    'no_hp'           => $request->no_hp,
                ]);
                break;
        }

        return redirect()->back()->with('success', "User baru ({$user->name}) berhasil ditambahkan dan langsung aktif.");
    }
}