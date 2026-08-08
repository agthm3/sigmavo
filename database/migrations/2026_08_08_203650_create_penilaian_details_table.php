<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penilaian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaians')->cascadeOnDelete();
            $table->foreignId('rubrik_id')->constrained('rubrik_penilaians')->cascadeOnDelete();
            $table->decimal('nilai_mentah', 5, 2)->default(0); // Nilai 0-100 yang diinput
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('penilaian_details');
    }
};