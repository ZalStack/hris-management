<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans'; 

    protected $fillable = [
        'judul',
        'konten',
        'kategori',
        'target_role',
        'target_departemen',
        'status',
        'lampiran',
        'tanggal_terbit',
        'tanggal_berlaku_hingga',
        'dibuat_oleh',
    ];

    protected $casts = [
        'status' => 'boolean',
        'tanggal_terbit' => 'datetime',
        'tanggal_berlaku_hingga' => 'date',
    ];

    public function pembuat()
    {
        return $this->belongsTo(Karyawan::class, 'dibuat_oleh');
    }
}