<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('zona_waktu_masuk', 10)->nullable()->after('longitude_masuk');
            $table->string('zona_waktu_pulang', 10)->nullable()->after('longitude_pulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['zona_waktu_masuk', 'zona_waktu_pulang']);
        });
    }
};
