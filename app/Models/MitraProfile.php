<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraProfile extends Model
{
    protected $fillable = ['user_id', 'nama_perusahaan', 'jabatan', 'no_hp'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}