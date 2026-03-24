<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Jabatan;

class PenempatanKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'jabatan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'gaji_pokok',
        'status_kepegawaian',
        'nomor_kontrak',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status' => 'boolean',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}
