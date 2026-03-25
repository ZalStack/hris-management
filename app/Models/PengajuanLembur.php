<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengajuanLembur extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_lemburs';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'keterangan',
        'status',
        'tanggal_disetujui',
        'disetujui_oleh',
        'nama_disetujui',
        'catatan',
        'lampiran',
        'tarif_lembur_per_jam',
        'total_lembur',
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'tanggal_disetujui' => 'datetime',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'total_jam' => 'decimal:2',
        'total_lembur' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(Karyawan::class, 'disetujui_oleh');
    }

    // Calculate total hours
    public static function hitungTotalJam($start, $end)
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        $diffInMinutes = $startTime->diffInMinutes($endTime);
        return round($diffInMinutes / 60, 2);
    }

    // Get status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'disetujui' => 'green',
            'ditolak' => 'red',
            default => 'gray',
        };
    }

    // Get status text
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}