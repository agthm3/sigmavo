<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PenilaianDetail extends Model {
    protected $fillable = ['penilaian_id', 'rubrik_id', 'nilai_mentah'];
    
    public function rubrik() {
        return $this->belongsTo(RubrikPenilaian::class, 'rubrik_id');
    }
}