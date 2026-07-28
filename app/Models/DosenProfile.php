<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DosenProfile extends Model
{
    protected $fillable = ['user_id', 'prodi_id', 'nip_nidn', 'departemen', 'no_hp'];

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