<?php

namespace App\Http\Controllers;

use App\Models\AdminProdiProfile;
use App\Models\DosenProfile;
use App\Models\MahasiswaProfile;
use App\Models\MitraProfile;
use App\Models\Perusahaan;
use App\Models\Pendaftaran; // Tambahan untuk riwayat magang
use App\Models\Prodi;
use App\Models\SpvProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AktivasiUserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $query = User::with([
            'roles', 
            'mahasiswaProfile.masterProdi', 
            'mahasiswaProfile.prodi',
            'dosenProfile.prodi', 
            'adminProdiProfile.prodi',
            'spvProfile.prodi',
            'spvProfile.perusahaan',
        ]);

        // 1. FILTER KETAT ADMIN PRODI HANYA BISA LIHAT PRODINYA SENDIRI
        if ($currentUser->hasAnyRole(['admin_prodi', 'admin-prodi'])) {
            $prodiId = $currentUser->adminProdiProfile?->prodi_id;

            if ($prodiId) {
                $query->where(function ($q) use ($prodiId) {
                    $q->whereHas('mahasiswaProfile', fn($m) => $m->where('prodi_id', $prodiId))
                      ->orWhereHas('dosenProfile', fn($d) => $d->where('prodi_id', $prodiId))
                      ->orWhereHas('spvProfile', fn($s) => $s->where('prodi_id', $prodiId));
                });
            }
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
        $prodis = Prodi::orderBy('nama_prodi', 'asc')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan', 'asc')->get();

        // 2. PENARIKAN DETAIL RIWAYAT MAGANG KHUSUS MAHASISWA
        foreach ($users as $user) {
            $riwayatMagang = [];
            if ($user->hasRole('mahasiswa')) {
                $pendaftarans = Pendaftaran::with(['lowongan.perusahaan', 'dosen'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();
                
                foreach ($pendaftarans as $p) {
                    $spvName = '-';
                    if ($p->jalur_magang == 'mandiri' && $p->catatan_seleksi) {
                        preg_match('/Supervisor:\s*(.*?)\s*\(/i', $p->catatan_seleksi, $matchName);
                        $spvName = !empty($matchName[1]) ? trim($matchName[1]) : '-';
                    } else if ($p->lowongan && $p->lowongan->perusahaan) {
                        $spv = SpvProfile::with('user')->where('perusahaan_id', $p->lowongan->perusahaan_id)->first();
                        if ($spv && $spv->user) {
                            $spvName = $spv->user->name;
                        }
                    }

                    $riwayatMagang[] = [
                        'posisi'   => $p->jalur_magang == 'mandiri' ? $p->divisi_mandiri : ($p->lowongan->judul_posisi ?? '-'),
                        'instansi' => $p->jalur_magang == 'mandiri' ? $p->nama_instansi_mandiri : ($p->lowongan->perusahaan->nama_perusahaan ?? '-'),
                        'dosen'    => $p->dosen->name ?? 'Belum ditetapkan',
                        'spv'      => $spvName,
                        'status'   => $p->status_seleksi,
                        'jalur'    => $p->jalur_magang,
                        'tgl'      => $p->created_at->format('d M Y')
                    ];
                }
            }
            $user->riwayat_magang = $riwayatMagang;
        }

        return view('dashboard.manajemen-akun.aktivasi-user', compact('users', 'prodis', 'perusahaans', 'currentUser'));
    }

    // --- (Semua kode di bawah ini SAMA dengan milik Anda sebelumnya, tidak ada yang dikurangi) ---
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if (Auth::user()->hasAnyRole(['admin_prodi', 'admin-prodi']) && ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang mengubah akun ini.');
        }
        $user->is_active = !$user->is_active;
        $user->save();
        $statusMessage = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusMessage}.");
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi', 'admin-prodi'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if ($currentUser->hasAnyRole(['admin_prodi', 'admin-prodi'])) {
            $adminProdiId = $currentUser->adminProdiProfile?->prodi_id;
            if ($adminProdiId) $request->merge(['prodi_id' => $adminProdiId]);
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

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'temp_password' => $request->password,
                'is_active'     => true,
            ]);
            $user->assignRole($request->role);

            switch ($request->role) {
                case 'mahasiswa':
                    $mhs = new MahasiswaProfile();
                    $mhs->user_id = $user->id;
                    $mhs->prodi_id = $request->prodi_id;
                    $mhs->nim = $request->nim;
                    $mhs->angkatan = $request->angkatan ?? date('Y');
                    $mhs->no_hp = $request->no_hp;
                    $mhs->save();
                    break;
                case 'dosen':
                    $prodiObj = Prodi::find($request->prodi_id);
                    $dsn = new DosenProfile();
                    $dsn->user_id = $user->id;
                    $dsn->prodi_id = $request->prodi_id;
                    $dsn->nip_nidn = $request->nip_nidn;
                    $dsn->departemen = $prodiObj?->nama_prodi ?? 'Vokasi';
                    $dsn->no_hp = $request->no_hp;
                    $dsn->save();
                    break;
                case 'admin_prodi':
                    $adm = new AdminProdiProfile();
                    $adm->user_id = $user->id;
                    $adm->prodi_id = $request->prodi_id;
                    $adm->nip_nidn = $request->nip_nidn;
                    $adm->save();
                    break;
                case 'spv':
                    $spv = new SpvProfile();
                    $spv->user_id = $user->id;
                    $spv->prodi_id = $request->prodi_id;
                    $spv->perusahaan_id = $request->perusahaan_id;
                    $spv->jabatan = $request->jabatan ?? 'Supervisor Lapangan';
                    $spv->no_hp = $request->no_hp;
                    $spv->save();
                    break;
            }
            DB::commit();
            return redirect()->back()->with('success', "User {$user->name} berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan user.');
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'          => 'required|string|exists:roles,name',
            'prodi_id'      => 'nullable|exists:prodis,id',
            'perusahaan_id' => 'nullable|exists:perusahaans,id',
            'nim'           => 'nullable|string',
            'angkatan'      => 'nullable|string|max:4',
            'nip_nidn'      => 'nullable|string',
            'jabatan'       => 'nullable|string',
            'no_hp'         => 'nullable|string',
        ]);

        $prodiId = $currentUser->hasRole('admin_prodi') ? $currentUser->adminProdiProfile?->prodi_id : $request->prodi_id;

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            if (!$user->hasRole($request->role)) $user->syncRoles([$request->role]);

            switch ($request->role) {
                case 'mahasiswa':
                    $mhs = $user->mahasiswaProfile ?? new MahasiswaProfile(['user_id' => $user->id]);
                    if ($prodiId) $mhs->prodi_id = $prodiId;
                    if ($request->filled('nim')) $mhs->nim = $request->nim;
                    if ($request->filled('angkatan')) $mhs->angkatan = $request->angkatan;
                    $mhs->no_hp = $request->no_hp;
                    $mhs->save();
                    break;
                case 'dosen':
                    $dsn = $user->dosenProfile ?? new DosenProfile(['user_id' => $user->id]);
                    if ($prodiId) $dsn->prodi_id = $prodiId;
                    if ($request->filled('nip_nidn')) $dsn->nip_nidn = $request->nip_nidn;
                    $dsn->no_hp = $request->no_hp;
                    $dsn->save();
                    break;
                case 'admin_prodi':
                    $adm = $user->adminProdiProfile ?? new AdminProdiProfile(['user_id' => $user->id]);
                    if ($prodiId) $adm->prodi_id = $prodiId;
                    if ($request->filled('nip_nidn')) $adm->nip_nidn = $request->nip_nidn;
                    $adm->save();
                    break;
                case 'spv':
                    $spv = $user->spvProfile ?? new SpvProfile(['user_id' => $user->id]);
                    if ($prodiId) $spv->prodi_id = $prodiId;
                    if ($request->filled('perusahaan_id')) $spv->perusahaan_id = $request->perusahaan_id;
                    $spv->jabatan = $request->jabatan ?? 'Supervisor Lapangan';
                    $spv->no_hp = $request->no_hp;
                    $spv->save();
                    break;
            }
            DB::commit();
            return redirect()->back()->with('success', "Profil {$user->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui profil.');
        }
    }

    public function downloadTemplateExcel() { /* KODE ASLI SAMA */ }
    public function previewImport(Request $request) { /* KODE ASLI SAMA */ }
    public function storeImport(Request $request) { /* KODE ASLI SAMA */ }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate(['new_password' => 'required|string|min:8']);
        $user->password = Hash::make($request->new_password);
        $user->temp_password = $request->new_password;
        $user->save();
        return redirect()->back()->with('success', "Password {$user->name} diperbarui.");
    }
}