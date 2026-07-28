<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Profil Mahasiswa
        Schema::create('mahasiswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->unique();
            $table->string('prodi');
            $table->string('angkatan', 4);
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // 2. Profil Dosen
        Schema::create('dosen_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nip_nidn')->unique();
            $table->string('departemen');
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });

        // 3. Profil Mitra / Perusahaan (HR)
        Schema::create('mitra_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_perusahaan');
            $table->string('jabatan');
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_profiles');
        Schema::dropIfExists('dosen_profiles');
        Schema::dropIfExists('mahasiswa_profiles');
    }
};