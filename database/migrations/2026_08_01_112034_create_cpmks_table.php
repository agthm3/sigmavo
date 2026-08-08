<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpmks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodis')->cascadeOnDelete();
            $table->string('kode_cpmk')->nullable(); // Contoh: CPMK-01, CPMK-02
            $table->text('deskripsi_cpmk'); // Keterangan CPMK
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpmks');
    }
};