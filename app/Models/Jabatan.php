<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'gaji_minimal' => 'decimal:2',
        'gaji_maksimal' => 'decimal:2',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}