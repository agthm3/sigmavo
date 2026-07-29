<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class DashboardMahasiswaAkunController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user()->load(['mahasiswaProfile.prodi']);

        // Cek status magang aktif mahasiswa
        $activeMagang = Pendaftaran::where('user_id', $user->id)
            ->where('status_seleksi', 'diterima')
            ->latest()
            ->first();

        return view('dashboard.akun.index', compact('user', 'activeMagang'));
    }

    /**
     * Perbarui informasi kontak (Email, WA, Alamat, Foto Profile)
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_wa'          => 'nullable|string|max:20',
            'alamat_domisili'=> 'nullable|string|max:500',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // 1. Update data user utama
        $user->email = $request->email;

        // Jika upload foto profil baru
        if ($request->hasFile('foto')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('foto')->store('avatars', 'public');
        }

        $user->save();

        // 2. Update data profil mahasiswa
        if ($user->mahasiswaProfile) {
            $user->mahasiswaProfile->update([
                'no_wa'           => $request->no_wa,
                'alamat_domisili' => $request->alamat_domisili,
            ]);
        }

        return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Perbarui Password Akun
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password akun Anda berhasil diperbarui.');
    }
}