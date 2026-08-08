<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrik_penilaians', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut')->default(0);
            $table->string('komponen');
            $table->text('indikator');
            $table->decimal('bobot', 5, 2); // Contoh: 15.50 (%)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubrik_penilaians');
    }
};