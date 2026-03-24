<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jabatan;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_departemen',
        'nama_departemen',
        'deskripsi',
        'kepala_departemen_id',
    ];

    public function kepalaDepartemen()
    {
        return $this->belongsTo(Karyawan::class, 'kepala_departemen_id');
    }

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class);
    }
}