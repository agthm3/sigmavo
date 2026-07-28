<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class JenisRoleController extends Controller
{
    /**
     * Menampilkan daftar Role beserta statistik & Permission-nya
     */
    public function index()
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Safety Check: Hanya Superadmin/Admin Fakultas yang berhak kelola Role
        if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('superadmin')) {
            return redirect()->route('dashboard-analitik')->with('error', 'Anda tidak memiliki hak akses ke manajemen role.');
        }

        // Ambil semua role beserta jumlah user yang memilikinya
        $roles = Role::withCount('users')->get();

        // Ambil semua permission yang ada
        $permissions = Permission::all();

        return view('dashboard.manajemen-akun.jenis-role', compact('roles', 'permissions'));
    }

    /**
     * Menyimpan Role Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Buat Role Baru (Format lowercase tanpa spasi)
        $roleName = strtolower(str_replace(' ', '_', trim($request->name)));

        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        // Sync Permission jika dipilih
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', "Role '{$role->name}' berhasil ditambahkan.");
    }

    /**
     * Update Permission pada Role
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Jangan izinkan ubah role 'admin' jika tidak sengaja terganggu
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', "Permission untuk role '{$role->name}' berhasil diperbarui.");
    }

    /**
     * Menghapus Role Kustom
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Mencegah penghapusan Role Bawaan Sistem
        $defaultRoles = ['admin', 'superadmin', 'admin_prodi', 'dosen', 'mahasiswa', 'mitra'];
        if (in_array($role->name, $defaultRoles)) {
            return redirect()->back()->with('error', "Role sistem '{$role->name}' tidak boleh dihapus!");
        }

        // Cek apakah masih ada user di role ini
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', "Gagal menghapus! Masih ada {$role->users()->count()} pengguna yang terikat pada role ini.");
        }

        $role->delete();

        return redirect()->back()->with('success', "Role '{$role->name}' berhasil dihapus.");
    }
}