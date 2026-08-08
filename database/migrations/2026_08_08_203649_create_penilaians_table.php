<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipe_penilai', ['spv', 'dosen']);
            $table->decimal('nilai_akhir', 5, 2)->default(0); // Nilai akumulasi (0-100) dari rubrik
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('penilaians');
    }
};