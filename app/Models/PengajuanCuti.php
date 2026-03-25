<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cutis';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'alasan',
        'status',
        'tanggal_disetujui',
        'lampiran',
        'catatan',
        'tahun_cuti',
        'kuota_total',
        'kuota_terpakai',
        'sisa_kuota',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Calculate total days
    public static function hitungTotalHari($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        return $startDate->diffInDays($endDate) + 1;
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

    // Get jenis cuti label
    public function getJenisCutiLabelAttribute()
    {
        $jenis = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'melahirkan' => 'Cuti Melahirkan',
            'penting' => 'Cuti Kepentingan',
            'ibadah' => 'Cuti Ibadah',
            'lainnya' => 'Cuti Lainnya',
        ];
        return $jenis[$this->jenis_cuti] ?? ucfirst($this->jenis_cuti);
    }
}