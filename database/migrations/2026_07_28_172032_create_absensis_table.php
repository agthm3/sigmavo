<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->nullOnDelete();
            $table->date('tanggal');
            
            // Absen Masuk
            $table->time('waktu_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('latitude_masuk')->nullable();
            $table->string('longitude_masuk')->nullable();

            // Absen Pulang
            $table->time('waktu_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->string('latitude_pulang')->nullable();
            $table->string('longitude_pulang')->nullable();

            // Pengajuan Izin / Sakit
            $table->enum('tipe_kehadiran', ['hadir', 'izin', 'sakit'])->default('hadir');
            $table->text('alasan_izin')->nullable();
            $table->string('surat_izin')->nullable();

            // Verifikasi & Pemotongan Jam
            $table->integer('jam_diperoleh')->default(0); // Misal 8 jam jika lengkap
            $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};