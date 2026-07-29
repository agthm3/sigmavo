<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembekalanPresensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembekalan_id',
        'user_id',
        'is_hadir',
        'waktu_presensi',
    ];

    public function pembekalan(): BelongsTo
    {
        return $this->belongsTo(Pembekalan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}