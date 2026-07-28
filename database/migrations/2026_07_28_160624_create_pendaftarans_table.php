<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Mahasiswa pelamar
            $table->foreignId('lowongan_id')->constrained('lowongans')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('users')->nullOnDelete(); // Dosen Pendamping Lapangan
            $table->enum('status_seleksi', ['menunggu', 'diterima', 'ditolak', 'wawancara'])->default('menunggu');
            $table->string('file_cv')->nullable();
            $table->string('file_transkrip')->nullable();
            $table->text('catatan_seleksi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};