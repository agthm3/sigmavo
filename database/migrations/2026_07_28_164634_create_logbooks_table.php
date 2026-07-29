<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Mahasiswa pembuat logbook
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->nullOnDelete(); // Pendaftaran/program magang terkait
            $table->date('tanggal');
            $table->text('uraian_kegiatan');
            $table->string('foto_dokumentasi')->nullable(); // Path gambar di storage
            $table->enum('status_asistensi', ['pending', 'approved', 'revisi'])->default('pending');
            $table->text('catatan_dosen')->nullable(); // Masukan/revisi dari dosen pendamping
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete(); // Dosen yang memverifikasi
            $table->timestamp('waktu_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};