# CONTEXT.md - Panduan Lengkap & Arsitektur SIGMAVO

Dokumen ini berisi pemahaman mendalam, arsitektur, relasi data, aturan bisnis, dan panduan operasional proyek **SIGMAVO** (Sistem Informasi Manajemen Magang Vokasi Universitas Hasanuddin) agar setiap AI Agent atau pengembang berikutnya memahami konteks proyek secara komprehensif tanpa ambigu.

---

## 1. Ikhtisar & Tujuan Sistem

**SIGMAVO** adalah platform terintegrasi berbasis web untuk tata kelola seluruh siklus magang/PKL mahasiswa Pendidikan Vokasi Universitas Hasanuddin (UNHAS).

Sistem menangani siklus hidup magang secara menyeluruh:
1. Pendaftaran magang (Lowongan Mitra Kampus & Jalur Mandiri).
2. Verifikasi dan seleksi berkas, penerbitan surat pengantar & upload surat balasan instansi.
3. Pembekalan magang dan presensi sebelum terjun ke industri.
4. Pelaksanaan harian: Presensi masuk/pulang, pengisian Logbook harian & pengajuan izin/sakit.
5. Verifikasi asistensi paralel (Dual Approval: SPV Mitra Lapangan & Dosen Pembimbing Kampus).
6. Ujian/Seminar hasil magang, pengumpulan laporan akhir, rubrik penilaian, dan rekapitulasi nilai konversi.

---

## 2. Tech Stack & Library Kunci

- **Backend Framework**: [Laravel 12 / 13](https://laravel.com) (PHP 8.3+)
- **Autentikasi & Otorisasi**: Laravel Breeze & [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission) (Role & Permission)
- **Frontend & Styling**: Blade Templates, [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database**: SQLite (lokal dev) / MySQL (staging & production)
- **Ekspor Dokumen**:
  - Word: `phpoffice/phpword` (Ekspor rekap logbook ke dokumen .docx)
  - PDF: `barryvdh/laravel-dompdf` (Ekspor lembar penilaian & surat)
  - Excel: `phpoffice/phpspreadsheet` (Import massal data user / mahasiswa / dosen)
- **Kompresi File Client-Side**: `sigmavo-compressor.js` (Canvas-based image compression sebelum submit untuk meringankan server)

---

## 3. Aktor & Role (Spatie Permission)

Sistem mengimplementasikan multi-role yang sangat ketat:

| Role | Deskripsi & Cakupan Akses |
|---|---|
| `superadmin` / `admin` | Akses penuh: manajemen prodi, manajemen role & permissions, master akun, pengaturan global, error toggling, monitoring, merge perusahaan duplicate. |
| `admin_prodi` | Tata kelola program magang tingkat prodi, manajemen lowongan, seleksi & penerbitan surat, master CPMK & Mata Kuliah prodi, verifikasi pendaftaran mandiri. |
| `dosen` (Dosen Pembimbing) | Melihat daftar mahasiswa bimbingan, memverifikasi absensi izin/sakit, memverifikasi logbook asistensi mahasiswa bimbingan, memberikan izin logbook susulan, input nilai rubrik seminar/laporan. |
| `spv` (Pembimbing Lapangan Mitra) | Melakukan verifikasi logbook harian mahasiswa di perusahaannya, memverifikasi presensi harian, memberikan catatan evaluasi lapangan. |
| `mahasiswa` | Melamar lowongan / mengajukan mandiri, upload surat balasan, presensi pembekalan, absensi harian (masuk/pulang/izin), submit logbook harian, logbook susulan (jika diaktifkan dosen), submit seminar & laporan akhir. |

---

## 4. Alur & Siklus Bisnis Utama (Core Workflows)

### 4.1. Alur Pendaftaran Magang
1. **Pilihan Jalur**:
   - **Lowongan Mitra Kampus**: Dibuat oleh Admin/Admin Prodi via `ListingProgramController`. Kuota berkurang saat mahasiswa melamar.
   - **Jalur Mandiri**: Mahasiswa mengisi nama instansi, posisi/divisi, upload proposal/surat mandiri via `AjukanMandiriController`.
2. **Penerbitan Surat Pengantar**: Admin menerbitkan surat pengantar magang berformat PDF.
3. **Surat Balasan**: Mahasiswa mengunggah surat balasan/penerimaan dari perusahaan. Admin memverifikasi (`status_seleksi` -> `diterima`).

### 4.2. Gerbang Pembekalan Magang (Prasyarat Wajib)
- Sebelum mahasiswa dapat mengisi absensi dan logbook harian, mahasiswa **wajib** tercatat hadir pada kegiatan Pembekalan (`PembekalanPresensi` dengan `is_hadir = true`).
- Jika belum pembekalan, antarmuka logbook terkunci (`isLocked = true`).

### 4.3. Presensi & Jam Kerja Harian
- Mahasiswa melakukan **Absen Masuk** dan **Absen Pulang**.
- Jam kerja normal default: 8 jam per hari kehadiran.
- Ada saklar mode testing di pengaturan global (`mode_testing`) untuk bypass validasi jam ketat saat pengujian.

### 4.4. Logbook & Dual Approval (Paralel)
- Mahasiswa hanya bisa mengisi logbook hari ini **setelah melakukan Absen Pulang**.
- **Mekanisme Dual Approval**:
  - Kolom di DB: `status_spv` (`pending`, `approved`, `revisi`), `status_dosen` (`pending`, `approved`, `revisi`), dan `status_asistensi`.
  - Dosen dan SPV Mitra memverifikasi secara **paralel independen** (tidak perlu antre menunggu satu sama lain).
  - Ketika **keduanya** telah menyetujui (`status_spv == 'approved' && status_dosen == 'approved'`), master status `status_asistensi` otomatis menjadi `approved`.
- **Logbook Susulan**:
  - Ditujukan bagi mahasiswa yang terlewat mengisi logbook di hari kerja sebelumnya.
  - Mahasiswa hanya bisa input logbook susulan jika Dosen Pembimbing atau Admin Prodi telah mengaktifkan saklar susulan (`is_susulan_enabled` pada tabel `pendaftarans`).

### 4.5. Keterkaitan Mata Kuliah & CPMK pada Logbook
- Setiap entri logbook dapat menautkan capaian pembelajaran / mata kuliah (`mata_kuliah` kolom JSON/array di tabel `logbooks`).
- Proyek memiliki Master Model `Cpmk` dan `MataKuliah` yang terikat per `prodi_id`.

---

## 5. Struktur Direktori Utama

```
sigmavo/
├── app/
│   ├── Http/
│   │   └── Controllers/        # Semua controller domain (Logbook, Absensi, Seleksi, dll.)
│   └── Models/                 # 24+ Eloquent Models relasional
├── database/
│   ├── migrations/             # Migrasi skema database (termasuk dual approval, susulan)
│   └── seeders/                # UserSeeder, MataKuliahSeeder, DatabaseSeeder
├── resources/
│   ├── views/
│   │   ├── dashboard/          # Blade views untuk tiap modul fitur
│   │   ├── layouts/            # Layout utama app & guest
│   │   └── components/         # Reusable Blade UI components
│   ├── css/ & js/              # Tailwind assets & Vite build
├── routes/
│   ├── web.php                 # Rute utama aplikasi dengan filter middleware Spatie
│   └── auth.php                # Rute autentikasi Breeze
├── public/
│   ├── dummy/                  # File HTML prototype/mockup UI acuan
│   └── js/sigmavo-compressor.js# Script kompresi gambar client-side
└── NOTE-PROGRESS               # Catatan task/revisi aktif pengembang
```

---

## 6. Tabel & Model Database Inti

1. **`users`**: Data user utama, berelasi polimorfik ke Spatie roles & profile masing-masing (`MahasiswaProfile`, `DosenProfile`, `AdminProdiProfile`, `SpvProfile`, `MitraProfile`).
2. **`prodis`**: Master program studi di Vokasi UNHAS (misal: TRK, BDL, AGR, TPT).
3. **`perusahaans` & `lowongans`**: Master mitra instansi dan lowongan pekerjaan magang.
4. **`pendaftarans`**: Pivot utama antara mahasiswa, lowongan, dosen pembimbing, status seleksi, dan izin logbook susulan.
5. **`logbooks`**: Catatan harian aktivitas magang, relasi ke pendaftaran, catatan dosen/SPV, status dual-approval, tag CPMK/mata kuliah (JSON).
6. **`absensis`**: Catatan waktu masuk, waktu pulang, status verifikasi izin/sakit, dan akumulasi jam kerja (`jam_diperoleh`).
7. **`pembekalans` & `pembekalan_presensis`**: Jadwal pembekalan dan rekaman kehadiran mahasiswa.
8. **`seminars` & `laporan_akhirs`**: Berkas presentasi, penjadwalan seminar, dan naskah laporan akhir magang.
9. **`rubrik_penilaians`, `penilaians`, `penilaian_details`**: Sistem penilaian terstruktur berbasis indikator/kriteria.
10. **`mata_kuliahs` & `cpmks`**: Master kurikulum dan capaian pembelajaran per program studi.
11. **`settings`**: Key-value table untuk konfigurasi global (`tahun_akademik`, `min_jam_magang`, `mode_testing`, dll.).

---

## 7. Catatan Pengembangan & Aturan untuk Agent

1. **Integritas Dual Approval**: Saat memodifikasi logika approval logbook, pastikan kedua status (`status_dosen` dan `status_spv`) dicek sebelum memvalidasi master `status_asistensi = 'approved'`.
2. **Multi-Prodi Isolation**: Pastikan query data untuk `admin_prodi` selalu difilter berdasarkan `prodi_id` milik admin prodi tersebut.
3. **Mode Testing Parameter**: Sistem memiliki opsi `mode_testing` di tabel `settings`. Perhatikan apakah fitur absensi/logbook sedang memperhitungkan saklar ini sebelum memberlakukan validasi waktu kaku.
4. **Status Seleksi Enum**: Status pendaftaran meliputi `pending`, `administrasi`, `wawancara`, `diterima`, `ditolak`, `selesai`.
5. **NOTE-PROGRESS**: Selalu periksa file `NOTE-PROGRESS` di root project untuk mengetahui task atau perbaikan terbaru yang diminta oleh pengguna.
