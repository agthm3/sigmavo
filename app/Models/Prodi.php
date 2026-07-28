<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $fillable = ['kode_prodi', 'nama_prodi', 'jenjang'];

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(MahasiswaProfile::class);
    }

    public function dosens(): HasMany
    {
        return $this->hasMany(DosenProfile::class);
    }
}