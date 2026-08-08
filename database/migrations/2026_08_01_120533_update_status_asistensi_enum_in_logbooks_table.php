<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah tipe enum status_asistensi pada MySQL
        DB::statement("ALTER TABLE logbooks MODIFY COLUMN status_asistensi ENUM('pending', 'pending_spv', 'approved_spv', 'approved', 'revisi') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE logbooks MODIFY COLUMN status_asistensi ENUM('pending', 'approved', 'revisi') NOT NULL DEFAULT 'pending'");
    }
};