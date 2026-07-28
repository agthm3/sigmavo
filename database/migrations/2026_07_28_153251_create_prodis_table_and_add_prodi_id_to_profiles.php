<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Program Studi
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prodi')->unique();
            $table->string('nama_prodi');
            $table->string('jenjang')->default('D4'); // D3 / D4
            $table->timestamps();
        });

        // 2. Tambahkan prodi_id ke profil Mahasiswa
        Schema::table('mahasiswa_profiles', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('user_id')->constrained('prodis')->nullOnDelete();
        });

        // 3. Tambahkan prodi_id ke profil Dosen
        Schema::table('dosen_profiles', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('user_id')->constrained('prodis')->nullOnDelete();
        });

        // 4. Tabel Profil khusus Admin Prodi
        Schema::create('admin_prodi_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->cascadeOnDelete();
            $table->string('nip_nidn')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_prodi_profiles');

        Schema::table('dosen_profiles', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });

        Schema::table('mahasiswa_profiles', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });

        Schema::dropIfExists('prodis');
    }
};