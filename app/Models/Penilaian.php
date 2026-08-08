<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model {
    protected $fillable = ['pendaftaran_id', 'penilai_id', 'tipe_penilai', 'nilai_akhir'];

    public function details() {
        return $this->hasMany(PenilaianDetail::class, 'penilaian_id');
    }
    public function penilai() {
        return $this->belongsTo(User::class, 'penilai_id');
    }
}