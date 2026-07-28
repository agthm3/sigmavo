<?php

namespace Database\Seeders;

use App\Models\AdminProdiProfile;
use App\Models\DosenProfile;
use App\Models\MahasiswaProfile;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $adminProdiRole = Role::firstOrCreate(['name' => 'admin_prodi']);
        $dosenRole      = Role::firstOrCreate(['name' => 'dosen']);
        $mahasiswaRole  = Role::firstOrCreate(['name' => 'mahasiswa']);
        $mitraRole      = Role::firstOrCreate(['name' => 'mitra']);

        // 2. Buat Program Studi (Prodi) Vokasi UNHAS
        $prodiTRK = Prodi::create([
            'kode_prodi' => 'TRK',
            'nama_prodi' => 'D4 Teknologi Rekayasa Komputer',
            'jenjang'    => 'D4',
        ]);

        $prodiBiolaut = Prodi::create([
            'kode_prodi' => 'BDL',
            'nama_prodi' => 'D4 Budidaya Laut & Perikanan',
            'jenjang'    => 'D4',
        ]);

        // ==========================================
        // 3. AKUN SUPERADMIN (AKSES SEMUA PRODI)
        // ==========================================
        $superadmin = User::create([
            'name'      => 'Superadmin Vokasi UNHAS',
            'email'     => 'admin@unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $superadmin->assignRole($adminRole);

        // ==========================================
        // 4. ADMIN PRODI TRK
        // ==========================================
        $adminTRK = User::create([
            'name'      => 'Admin Prodi TRK',
            'email'     => 'admin.trk@unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $adminTRK->assignRole($adminProdiRole);
        AdminProdiProfile::create([
            'user_id'  => $adminTRK->id,
            'prodi_id' => $prodiTRK->id,
            'nip_nidn' => '199001012020011001',
        ]);

        // ==========================================
        // 5. ADMIN PRODI BUDIDAYA LAUT
        // ==========================================
        $adminBDL = User::create([
            'name'      => 'Admin Prodi Budidaya Laut',
            'email'     => 'admin.bdl@unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $adminBDL->assignRole($adminProdiRole);
        AdminProdiProfile::create([
            'user_id'  => $adminBDL->id,
            'prodi_id' => $prodiBiolaut->id,
            'nip_nidn' => '199002022020021002',
        ]);

        // ==========================================
        // 6. DOSEN & MAHASISWA PRODI TRK
        // ==========================================
        $dosenTRK = User::create([
            'name'      => 'Dr. Eng. Dosen TRK, S.T., M.T.',
            'email'     => 'dosen.trk@unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $dosenTRK->assignRole($dosenRole);
        DosenProfile::create([
            'user_id'    => $dosenTRK->id,
            'prodi_id'   => $prodiTRK->id,
            'nip_nidn'   => '198501012010121001',
            'departemen' => 'Teknologi Informasi',
            'no_hp'      => '081234567890',
        ]);

        $mhsTRK = User::create([
            'name'      => 'Fadehl Thristansyah (TRK)',
            'email'     => 'fadehl@student.unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $mhsTRK->assignRole($mahasiswaRole);
        MahasiswaProfile::create([
            'user_id'  => $mhsTRK->id,
            'prodi_id' => $prodiTRK->id,
            'nim'      => 'H071211001',
            'prodi'    => 'D4 Teknologi Rekayasa Komputer',
            'angkatan' => '2023',
            'no_hp'    => '089876543210',
            'alamat'   => 'Makassar',
        ]);

        // ==========================================
        // 7. DOSEN & MAHASISWA PRODI BUDIDAYA LAUT
        // ==========================================
        $dosenBDL = User::create([
            'name'      => 'Prof. Dr. Dosen Perikanan, M.Si.',
            'email'     => 'dosen.bdl@unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => false, // Set Pending
        ]);
        $dosenBDL->assignRole($dosenRole);
        DosenProfile::create([
            'user_id'    => $dosenBDL->id,
            'prodi_id'   => $prodiBiolaut->id,
            'nip_nidn'   => '197505052005011003',
            'departemen' => 'Kelautan',
            'no_hp'      => '081112223334',
        ]);

        $mhsBDL = User::create([
            'name'      => 'Andi Mahasiswa Budidaya Laut',
            'email'     => 'andi@student.unhas.ac.id',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);
        $mhsBDL->assignRole($mahasiswaRole);
        MahasiswaProfile::create([
            'user_id'  => $mhsBDL->id,
            'prodi_id' => $prodiBiolaut->id,
            'nim'      => 'H071211002',
            'prodi'    => 'D4 Budidaya Laut & Perikanan',
            'angkatan' => '2023',
            'no_hp'    => '085211223344',
            'alamat'   => 'Gowa',
        ]);
    }
}