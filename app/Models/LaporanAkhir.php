<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanAkhir extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pendaftaran_id',
        'judul_laporan',
        'file_laporan',
        'status_verifikasi',
        'catatan',
        'verifikator_id',
        'waktu_verifikasi',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}