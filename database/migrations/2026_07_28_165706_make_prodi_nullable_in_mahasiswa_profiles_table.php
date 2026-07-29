<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table) {
            // Mengubah kolom prodi menjadi nullable agar tidak error saat insert prodi_id
            $table->string('prodi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table) {
            $table->string('prodi')->nullable(false)->change();
        });
    }
};