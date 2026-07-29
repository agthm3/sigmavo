<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembekalans', function (Blueprint $table) {
            // Nullable: Jika null berarti berlaku untuk Semua Prodi (Fakultas), jika terisi berarti spesifik Prodi
            $table->foreignId('prodi_id')->nullable()->after('id')->constrained('prodis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembekalans', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }
};