<?php

namespace App\Http\Controllers;

use App\Models\AdminProdiProfile;
use App\Models\DosenProfile;
use App\Models\MahasiswaProfile;
use App\Models\MitraProfile;
use App\Models\Perusahaan;
use App\Models\Prodi;
use App\Models\SpvProfile;
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
            'spvProfile.prodi',
            'spvProfile.perusahaan',
            'mitraProfile'
        ]);

        if ($currentUser->hasRole('admin_prodi')) {
            $prodiId = $currentUser->prodi_id;

            $query->where(function ($q) use ($prodiId) {
                $q->whereHas('mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId))
                  ->orWhereHas('dosenProfile', fn($d) => $d->where('prodi_id', $prodiId))
                  ->orWhereHas('spvProfile', fn($s) => $s->where('prodi_id', $prodiId));
            });
        } elseif ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
            $prodiId = $request->prodi_id;

            $query->where(function ($q) use ($prodiId) {
                $q->whereHas('mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId))
                  ->orWhereHas('dosenProfile', fn($d) => $d->where('prodi_id', $prodiId))
                  ->orWhereHas('adminProdiProfile', fn($a) => $a->where('prodi_id', $prodiId))
                  ->orWhereHas('spvProfile', fn($s) => $s->where('prodi_id', $prodiId));
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('role') && $request->role !== 'semua') {
            $role = $request->role;
            $query->whereHas('roles', fn($r) => $r->where('name', $role));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $prodis = Prodi::all();
        $perusahaans = Perusahaan::all();

        return view('dashboard.manajemen-akun.aktivasi-user', compact('users', 'prodis', 'perusahaans', 'currentUser'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
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

        if (!$currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menambah user.');
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'role'          => 'required|string|exists:roles,name',
            'prodi_id'      => 'nullable|required_if:role,mahasiswa,dosen,admin_prodi,spv|exists:prodis,id',
            'perusahaan_id' => 'nullable|required_if:role,spv|exists:perusahaans,id',
            'nim'           => 'nullable|required_if:role,mahasiswa|string|unique:mahasiswa_profiles,nim',
            'angkatan'      => 'nullable|required_if:role,mahasiswa|string|max:4',
            'nip_nidn'      => 'nullable|required_if:role,dosen,admin_prodi|string',
            'jabatan'       => 'nullable|string',
            'no_hp'         => 'nullable|string',
        ]);

        $prodiId = $currentUser->hasRole('admin_prodi') 
            ? $currentUser->prodi_id 
            : $request->prodi_id;

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

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
                    'departemen' => 'Vokasi',
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

            case 'spv':
                SpvProfile::create([
                    'user_id'       => $user->id,
                    'prodi_id'      => $prodiId,
                    'perusahaan_id' => $request->perusahaan_id,
                    'jabatan'       => $request->jabatan ?? 'Supervisor Lapangan',
                    'no_hp'         => $request->no_hp,
                ]);
                break;
        }

        return redirect()->back()->with('success', "User baru ({$user->name}) berkategori " . strtoupper($request->role) . " berhasil ditambahkan.");
    }
}