<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->nullOnDelete();
            $table->string('judul_laporan');
            $table->string('file_laporan');
            $table->enum('status_verifikasi', ['pending', 'approved', 'revisi'])->default('pending');
            $table->text('catatan')->nullable();
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('waktu_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_akhirs');
    }
};