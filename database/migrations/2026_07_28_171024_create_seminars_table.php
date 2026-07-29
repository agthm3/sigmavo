<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seminars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->nullOnDelete();
            $table->string('file_ppt')->nullable(); // File presentasi pptx/pdf
            $table->dateTime('waktu_seminar')->nullable();
            $table->string('lokasi_ruangan')->nullable();
            
            // Penguji
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penguji_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penguji_2_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Status & Nilai
            $table->enum('status_seminar', ['belum_daftar', 'mengajukan', 'dijadwalkan', 'selesai', 'ditolak'])->default('belum_daftar');
            $table->decimal('nilai_pembimbing', 5, 2)->nullable();
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seminars');
    }
};