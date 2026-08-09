<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MahasiswaProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'prodi_id', 
        'nim', 
        'angkatan', 
        'no_hp', 
        'alamat'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI UTAMA KE MODEL PRODI
     */
    public function masterProdi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    /**
     * ALIAS RELASI (Agar tidak error saat sistem memanggil ->prodi)
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}