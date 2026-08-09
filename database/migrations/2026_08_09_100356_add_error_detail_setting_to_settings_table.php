<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        // Masukkan default setting show_error_detail = 'true'
        Setting::setKey('show_error_detail', 'true');
    }

    public function down(): void
    {
        //
    }
};