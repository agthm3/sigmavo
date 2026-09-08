<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // 1. Bersihkan prodi_id orphan jika masih ada
        DB::table('lowongans')
            ->whereNotNull('prodi_id')
            ->whereNotIn('prodi_id', DB::table('prodis')->pluck('id'))
            ->update(['prodi_id' => null]);

        // 2. Tambahkan kolom HANYA JIKA belum ada di tabel
        if (!Schema::hasColumn('lowongans', 'spv_id')) {
            Schema::table('lowongans', function (Blueprint $table) {
                $table->unsignedBigInteger('spv_id')->nullable()->after('perusahaan_id');
                $table->foreign('spv_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Pindahkan data SPV lama ke lowongan yang spv_id-nya masih kosong
        $lowongans = DB::table('lowongans')->whereNull('spv_id')->get();
        foreach ($lowongans as $lowongan) {
            $spvProfile = DB::table('spv_profiles')
                ->where('perusahaan_id', $lowongan->perusahaan_id)
                ->first();

            if ($spvProfile) {
                DB::table('lowongans')
                    ->where('id', $lowongan->id)
                    ->update(['spv_id' => $spvProfile->user_id]);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasColumn('lowongans', 'spv_id')) {
            Schema::table('lowongans', function (Blueprint $table) {
                // Drop foreign key dulu jika ada
                try {
                    $table->dropForeign(['spv_id']);
                } catch (\Exception $e) {
                    // Abaikan jika nama foreign key berbeda
                }
                $table->dropColumn('spv_id');
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};