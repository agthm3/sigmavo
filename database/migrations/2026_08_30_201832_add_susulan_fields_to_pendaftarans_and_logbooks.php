<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->boolean('allow_logbook_susulan')->default(false)->after('status_seleksi');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->boolean('is_susulan')->default(false)->after('status_asistensi');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn('allow_logbook_susulan');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn('is_susulan');
        });
    }
};