<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = Prodi::all();

        $sampleMatkul = [
            'TRK' => [
                ['kode' => 'TRK101', 'nama' => 'Proyek Industri Rekayasa Komputer', 'sks' => 4],
                ['kode' => 'TRK102', 'nama' => 'Pemrograman Sistem Tertanam', 'sks' => 3],
                ['kode' => 'TRK103', 'nama' => 'Jaringan Komputer & Keamanan Cyber', 'sks' => 3],
                ['kode' => 'TRK104', 'nama' => 'Etika Profesi & K3 Industri', 'sks' => 2],
            ],
            'BDL' => [
                ['kode' => 'BDL201', 'nama' => 'Teknologi Budidaya Perairan Terapan', 'sks' => 4],
                ['kode' => 'BDL202', 'nama' => 'Manajemen Kualitas Air Industri', 'sks' => 3],
                ['kode' => 'BDL203', 'nama' => 'Praktikum Lapangan Perikanan', 'sks' => 3],
            ],
            'AGR' => [
                ['kode' => 'AGR301', 'nama' => 'Agribisnis & Rantai Pasok Modern', 'sks' => 4],
                ['kode' => 'AGR302', 'nama' => 'Manajemen Keuangan Usaha Tani', 'sks' => 3],
                ['kode' => 'AGR303', 'nama' => 'Pemasaran Digital Produk Pertanian', 'sks' => 3],
            ],
            'TPT' => [
                ['kode' => 'TPT401', 'nama' => 'Teknologi Produksi Tanaman Industri', 'sks' => 4],
                ['kode' => 'TPT402', 'nama' => 'Kultur Jaringan & Bioteknologi', 'sks' => 3],
                ['kode' => 'TPT403', 'nama' => 'Manajemen Hama & Penyakit Tanaman', 'sks' => 3],
            ],
        ];

        foreach ($prodis as $prodi) {
            $kode = strtoupper($prodi->kode_prodi);
            $matkuls = $sampleMatkul[$kode] ?? [
                ['kode' => $kode.'01', 'nama' => 'Praktikum Lapangan / Proyek Magang', 'sks' => 4],
                ['kode' => $kode.'02', 'nama' => 'Etika Profesi & Tata Kelola Kerja', 'sks' => 2],
                ['kode' => $kode.'03', 'nama' => 'Manajemen Proyek Terapan', 'sks' => 3],
            ];

            foreach ($matkuls as $mk) {
                MataKuliah::firstOrCreate([
                    'prodi_id' => $prodi->id,
                    'nama_mk'  => $mk['nama'],
                ], [
                    'kode_mk'  => $mk['kode'],
                    'sks'      => $mk['sks'],
                ]);
            }
        }
    }
}