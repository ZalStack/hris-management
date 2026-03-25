<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe_notifikasi',
        'status',
        'tanggal_dibaca',
    ];

    protected $casts = [
        'status' => 'boolean',
        'tanggal_dibaca' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Karyawan::class, 'user_id');
    }
}