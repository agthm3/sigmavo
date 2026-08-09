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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// Library Excel PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AktivasiUserController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // PENTING: Gunakan masterProdi untuk mahasiswa agar tidak bentrok
        $query = User::with([
            'roles', 
            'mahasiswaProfile.masterProdi', 
            'dosenProfile.prodi', 
            'adminProdiProfile.prodi',
            'spvProfile.prodi',
            'spvProfile.perusahaan',
            'mitraProfile'
        ]);

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
        $prodis = Prodi::all();
        $perusahaans = Perusahaan::all();

        return view('dashboard.manajemen-akun.aktivasi-user', compact('users', 'prodis', 'perusahaans', 'currentUser'));
    }

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
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi', 'admin-prodi'])) {
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

        $prodiId = $currentUser->hasAnyRole(['admin_prodi', 'admin-prodi']) 
            ? $currentUser->adminProdiProfile?->prodi_id 
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
                // Gunakan native object
                $mhs = new MahasiswaProfile();
                $mhs->user_id = $user->id;
                $mhs->prodi_id = $prodiId;
                $mhs->nim = $request->nim;
                $mhs->angkatan = $request->angkatan ?? date('Y');
                $mhs->no_hp = $request->no_hp;
                $mhs->save();
                break;

            case 'dosen':
                $prodiObj = Prodi::find($prodiId);
                $dsn = new DosenProfile();
                $dsn->user_id = $user->id;
                $dsn->prodi_id = $prodiId;
                $dsn->nip_nidn = $request->nip_nidn;
                $dsn->departemen = $prodiObj?->nama_prodi ?? 'Vokasi';
                $dsn->no_hp = $request->no_hp;
                $dsn->save();
                break;

            case 'admin_prodi':
            case 'admin-prodi':
                $adm = new AdminProdiProfile();
                $adm->user_id = $user->id;
                $adm->prodi_id = $prodiId;
                $adm->nip_nidn = $request->nip_nidn;
                $adm->save();
                break;

            case 'spv':
                $spv = new SpvProfile();
                $spv->user_id = $user->id;
                $spv->prodi_id = $prodiId;
                $spv->perusahaan_id = $request->perusahaan_id;
                $spv->jabatan = $request->jabatan ?? 'Supervisor Lapangan';
                $spv->no_hp = $request->no_hp;
                $spv->save();
                break;
        }

        return redirect()->back()->with('success', "User baru ({$user->name}) berkategori " . strtoupper($request->role) . " berhasil ditambahkan.");
    }

    /**
     * 1. DOWNLOAD TEMPLATE EXCEL
     */
    public function downloadTemplateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');

        $headers = ['Nama Lengkap', 'Email', 'Password', 'Role', 'NIM atau NIP', 'Angkatan'];
        $columnLetter = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . '1', $header);
            $sheet->getStyle($columnLetter . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $columnLetter++;
        }

        $sheet->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', 'budi@vokasi.unhas.ac.id');
        $sheet->setCellValue('C2', 'vokasi123');
        $sheet->setCellValue('D2', 'mahasiswa');
        $sheet->setCellValueExplicit('E2', 'H071231012', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F2', '2023', DataType::TYPE_STRING);

        $sheet->setCellValue('A3', 'Dr. Andi Dosen');
        $sheet->setCellValue('B3', 'andi@vokasi.unhas.ac.id');
        $sheet->setCellValue('C3', 'vokasi123');
        $sheet->setCellValue('D3', 'dosen');
        $sheet->setCellValueExplicit('E3', '198501012010121001', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F3', '', DataType::TYPE_STRING);

        $fileName = 'Template_Import_User_SIGMAVO.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * 2. PREVIEW EXCEL UNTUK DIPILIHKAN PRODI
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file_excel.mimes' => 'Format file harus Excel (.xlsx atau .xls).',
            'file_excel.max'   => 'Ukuran file maksimal 5MB.',
        ]);

        $path = $request->file('file_excel')->getRealPath();
        
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        array_shift($rows); // Hapus Header

        $parsedData = [];
        foreach ($rows as $row) {
            $nama  = trim($row[0] ?? '');
            $email = trim($row[1] ?? '');

            if (!empty($nama) && !empty($email)) {
                $parsedData[] = [
                    'nama'     => $nama,
                    'email'    => $email,
                    'password' => trim($row[2] ?? '') ?: 'vokasi123',
                    'role'     => strtolower(trim($row[3] ?? 'mahasiswa')),
                    'nim_nip'  => trim($row[4] ?? ''),
                    'angkatan' => trim($row[5] ?? date('Y')),
                ];
            }
        }

        if (empty($parsedData)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan di dalam file Excel.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        // Ambil daftar Master Program Studi untuk dipilih di halaman preview
        $prodis = Prodi::orderBy('nama_prodi', 'asc')->get(); 

        return view('dashboard.manajemen-akun.preview-import', compact('parsedData', 'currentUser', 'prodis'));
    }

    /**
     * 3. PENYIMPANAN FINAL MASS IMPORT (PRODI PASTI LENGKET)
     */
    public function storeImport(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 1. Ambil prodi_id dari request atau profil admin prodi
        $prodiId = null;

        if ($currentUser->hasAnyRole(['admin_prodi', 'admin-prodi'])) {
            $prodiId = $currentUser->adminProdiProfile?->prodi_id ?? $currentUser->prodi_id;
            // Fallback hidden input jika profil kosong
            if (!$prodiId && $request->has('prodi_id')) {
                $prodiId = $request->input('prodi_id');
            }
        } else {
            $request->validate([
                'prodi_id' => 'required|exists:prodis,id',
            ], [
                'prodi_id.required' => 'Silakan pilih Program Studi tujuan terlebih dahulu.',
                'prodi_id.exists'   => 'Program Studi yang dipilih tidak valid di database.',
            ]);
            $prodiId = $request->input('prodi_id');
        }

        $prodiId = (int) $prodiId; // Cast to integer

        if (!$prodiId || $prodiId === 0) {
            return redirect()->route('dashboard-manajemen-aktivasi-user')
                ->with('error', 'Gagal import: Program Studi tujuan tidak ditemukan atau belum dipilih.');
        }

        $prodiObj = Prodi::find($prodiId);
        $namaProdi = $prodiObj ? $prodiObj->nama_prodi : 'Vokasi';

        $usersData = json_decode($request->users_data, true) ?? [];
        if (empty($usersData)) {
            return redirect()->route('dashboard-manajemen-aktivasi-user')
                ->with('error', 'Gagal import: Data pengguna di Excel kosong.');
        }

        $berhasil = 0;
        $gagal = 0;

        DB::beginTransaction();
        try {
            foreach ($usersData as $u) {
                $email = strtolower(trim($u['email'] ?? ''));

                if (empty($email) || User::where('email', $email)->exists()) {
                    $gagal++;
                    continue; 
                }

                // 2. Buat Akun User Utama
                $user = new User();
                $user->name      = trim($u['nama']);
                $user->email     = $email;
                $user->password  = Hash::make(trim($u['password'] ?? 'vokasi123'));
                $user->is_active = true;
                $user->save();

                // 3. Normalisasi & Assign Role
                $rawRole = strtolower(trim($u['role'] ?? 'mahasiswa'));
                if (in_array($rawRole, ['mhs', 'mahasiswa'])) {
                    $roleName = 'mahasiswa';
                } elseif (in_array($rawRole, ['dosen', 'dospen', 'dosen_pembimbing'])) {
                    $roleName = 'dosen';
                } elseif (in_array($rawRole, ['admin_prodi', 'admin-prodi', 'admin prodi'])) {
                    $roleName = 'admin_prodi';
                } elseif (in_array($rawRole, ['spv', 'supervisor'])) {
                    $roleName = 'spv';
                } else {
                    $roleName = 'mahasiswa';
                }
                
                $user->assignRole($roleName);

                // 4. SIMPAN PROFILE SECARA NATIVE AGAR MYSQL MEMAKSA FILL PRODI_ID (Tanpa $fillable restrictions)
                if ($roleName === 'mahasiswa') {
                    $mhs = new MahasiswaProfile();
                    $mhs->user_id  = $user->id;
                    $mhs->prodi_id = $prodiId;
                    $mhs->nim      = trim($u['nim_nip'] ?? '');
                    $mhs->angkatan = trim($u['angkatan'] ?? '') ?: date('Y');
                    $mhs->save();
                } elseif ($roleName === 'dosen') {
                    $dsn = new DosenProfile();
                    $dsn->user_id    = $user->id;
                    $dsn->prodi_id   = $prodiId;
                    $dsn->nip_nidn   = trim($u['nim_nip'] ?? '');
                    $dsn->departemen = $namaProdi;
                    $dsn->save();
                } elseif ($roleName === 'admin_prodi') {
                    $adm = new AdminProdiProfile();
                    $adm->user_id  = $user->id;
                    $adm->prodi_id = $prodiId;
                    $adm->nip_nidn = trim($u['nim_nip'] ?? '');
                    $adm->save();
                }
                
                $berhasil++;
            }

            DB::commit();
            return redirect()->route('dashboard-manajemen-aktivasi-user')
                ->with('success', "$berhasil User berhasil ditambahkan ke Program Studi {$namaProdi}!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat Mass Import: ' . $e->getMessage());
            return redirect()->route('dashboard-manajemen-aktivasi-user')
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage());
        }
    }
}