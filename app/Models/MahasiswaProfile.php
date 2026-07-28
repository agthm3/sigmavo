<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MahasiswaProfile extends Model
{
    protected $fillable = ['user_id', 'prodi_id', 'nim', 'prodi', 'angkatan', 'no_hp', 'alamat'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // RELASI KE PRODI
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}