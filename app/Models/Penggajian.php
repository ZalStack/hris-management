<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajians';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'bulan',
        'tahun',
        'gaji_pokok',
        'total_tunjangan',
        'uang_lembur',
        'bonus',
        'total_potongan',
        'jumlah_pajak',
        'potongan_bpjs',
        'gaji_bersih',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'nama_bank',
        'nomor_rekening',
        'status',
        'catatan',
        'dibuat_oleh',
        'detail_gaji',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'uang_lembur' => 'decimal:2',
        'bonus' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'jumlah_pajak' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];

    // Status options
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    // Method status badges
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">Draft</span>',
            self::STATUS_PENDING => '<span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">Pending Approval</span>',
            self::STATUS_APPROVED => '<span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs">Approved</span>',
            self::STATUS_PAID => '<span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Paid</span>',
            self::STATUS_CANCELLED => '<span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Cancelled</span>',
            default => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getBulanTextAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(Karyawan::class, 'dibuat_oleh', 'nama_lengkap');
    }

    // Calculate pajak (tax) based on salary
    public static function hitungPajak($gajiKotor)
    {
        // Tax calculation: 5% for salary > 5 million, 15% for > 50 million, etc.
        if ($gajiKotor <= 5000000) {
            return 0;
        } elseif ($gajiKotor <= 50000000) {
            return $gajiKotor * 0.05;
        } elseif ($gajiKotor <= 250000000) {
            return $gajiKotor * 0.15;
        } elseif ($gajiKotor <= 500000000) {
            return $gajiKotor * 0.25;
        } else {
            return $gajiKotor * 0.30;
        }
    }

    // Calculate BPJS (health insurance)
    public static function hitungBPJS($gajiPokok)
    {
        // BPJS: 1% from employee, 4% from company (total 5% of salary)
        return $gajiPokok * 0.01;
    }
}