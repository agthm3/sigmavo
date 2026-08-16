<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM status_seleksi agar memuat nilai 'selesai' atau ubah menjadi VARCHAR
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status_seleksi VARCHAR(50) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status_seleksi ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending'");
    }
};