<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProdiProfile extends Model
{
    protected $fillable = ['user_id', 'prodi_id', 'nip_nidn'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}