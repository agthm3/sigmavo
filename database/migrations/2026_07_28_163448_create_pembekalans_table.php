<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Jadwal & Informasi Pembekalan
        Schema::create('pembekalans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('lokasi');
            $table->string('link_zoom')->nullable();
            $table->string('pemateri');
            $table->text('topik_utama')->nullable(); // JSON / Text terpisah koma
            $table->enum('status', ['mendatang', 'berlangsung', 'selesai'])->default('selesai');
            $table->timestamps();
        });

        // 2. Tabel Presensi Pembekalan Mahasiswa
        Schema::create('pembekalan_presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembekalan_id')->constrained('pembekalans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_hadir')->default(true);
            $table->timestamp('waktu_presensi')->useCurrent();
            $table->timestamps();
        });

        // 3. Tabel Materi Pembekalan
        Schema::create('pembekalan_materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembekalan_id')->constrained('pembekalans')->cascadeOnDelete();
            $table->string('judul_materi');
            $table->string('tipe_file')->default('PDF'); // PDF, DOCX, PPTX
            $table->string('ukuran_file')->default('1.5 MB');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembekalan_materis');
        Schema::dropIfExists('pembekalan_presensis');
        Schema::dropIfExists('pembekalans');
    }
};