<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            if (!Schema::hasColumn('logbooks', 'status_spv')) {
                $table->enum('status_spv', ['pending', 'approved', 'revisi'])->default('pending')->after('status_asistensi');
            }
            if (!Schema::hasColumn('logbooks', 'status_dosen')) {
                $table->enum('status_dosen', ['pending', 'approved', 'revisi'])->default('pending')->after('status_spv');
            }
            if (!Schema::hasColumn('logbooks', 'catatan_spv')) {
                $table->text('catatan_spv')->nullable()->after('catatan_dosen');
            }
        });

        // Sinkronisasi data logbook lama agar tidak kembali jadi pending
        if (Schema::hasColumn('logbooks', 'status_spv')) {
            DB::statement("UPDATE logbooks SET status_spv = 'approved' WHERE status_asistensi IN ('approved_spv', 'approved')");
        }
        if (Schema::hasColumn('logbooks', 'status_dosen')) {
            DB::statement("UPDATE logbooks SET status_dosen = 'approved' WHERE status_asistensi = 'approved'");
        }
    }

    public function down()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('logbooks', 'status_spv')) $dropCols[] = 'status_spv';
            if (Schema::hasColumn('logbooks', 'status_dosen')) $dropCols[] = 'status_dosen';
            
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};