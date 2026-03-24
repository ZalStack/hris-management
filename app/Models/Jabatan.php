<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Departemen;
use App\Models\PenempatanKaryawan;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'departemen_id',
        'deskripsi',
        'gaji_minimal',
        'gaji_maksimal',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function penempatan()
    {
        return $this->hasMany(PenempatanKaryawan::class);
    }
}