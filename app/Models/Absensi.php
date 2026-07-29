<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pendaftaran_id',
        'tanggal',
        'waktu_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'waktu_pulang',
        'foto_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'tipe_kehadiran',
        'alasan_izin',
        'surat_izin',
        'jam_diperoleh',
        'status_verifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}