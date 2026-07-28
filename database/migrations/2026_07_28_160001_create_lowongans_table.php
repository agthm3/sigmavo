<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->cascadeOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete(); // null = Semua Prodi
            $table->string('judul_posisi');
            $table->enum('mode_kerja', ['WFO', 'Hybrid', 'WFH'])->default('WFO');
            $table->integer('kuota')->default(1);
            $table->integer('kuota_terisi')->default(0);
            $table->date('batas_pendaftaran');
            $table->string('durasi')->default('6 Bulan');
            $table->text('deskripsi');
            $table->enum('status', ['published', 'draft', 'closed'])->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};