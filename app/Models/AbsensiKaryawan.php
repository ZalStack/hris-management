<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'absensi_karyawans';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lokasi_masuk',
        'lokasi_pulang',
        'total_jam_kerja',
        'status_kehadiran',
        'keterangan',
        'is_change_day',
        'change_day_tanggal_awal',
        'change_day_tanggal_akhir',
        'change_day_jam_mulai',
        'change_day_jam_selesai',
        'change_day_alasan',
        'change_day_status',
        'change_day_catatan_admin',
        'change_day_disetujui_pada',
        'change_day_disetujui_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_jam_kerja' => 'decimal:2',
        'is_change_day' => 'boolean',
        'change_day_tanggal_awal' => 'date',
        'change_day_tanggal_akhir' => 'date',
        'change_day_disetujui_pada' => 'datetime',
    ];

    // Status constants
    const CHANGE_DAY_PENDING = 'pending';
    const CHANGE_DAY_APPROVED = 'approved';
    const CHANGE_DAY_REJECTED = 'rejected';

    const STATUS_HADIR = 'hadir';
    const STATUS_IZIN = 'izin';
    const STATUS_SAKIT = 'sakit';
    const STATUS_ALPHA = 'alpha';
    const STATUS_PENDING = 'pending';

    public function getChangeDayStatusBadgeAttribute()
    {
        return match($this->change_day_status) {
            self::CHANGE_DAY_PENDING => '<span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">Pending</span>',
            self::CHANGE_DAY_APPROVED => '<span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Disetujui</span>',
            self::CHANGE_DAY_REJECTED => '<span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Ditolak</span>',
            default => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">' . ucfirst($this->change_day_status) . '</span>',
        };
    }

    public function getStatusKehadiranBadgeAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_HADIR => 'green',
            self::STATUS_IZIN => 'blue',
            self::STATUS_SAKIT => 'purple',
            self::STATUS_ALPHA => 'red',
        ];
        $color = $colors[$this->status_kehadiran] ?? 'gray';
        $text = strtoupper($this->status_kehadiran);
        
        return "<span class='bg-{$color}-200 text-{$color}-800 py-1 px-3 rounded-full text-xs'>{$text}</span>";
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(Karyawan::class, 'change_day_disetujui_oleh');
    }

    public function hitungTotalJamChangeDay()
    {
        if (!$this->is_change_day || !$this->change_day_jam_mulai || !$this->change_day_jam_selesai) {
            return 0;
        }

        try {
            $tanggalMulai = $this->change_day_tanggal_awal->format('Y-m-d');
            $jamMulai = Carbon::parse($tanggalMulai . ' ' . $this->change_day_jam_mulai);
            $jamSelesai = Carbon::parse($tanggalMulai . ' ' . $this->change_day_jam_selesai);

            if ($jamSelesai < $jamMulai) {
                $jamSelesai->addDay();
            }

            $selisihMenit = $jamMulai->diffInMinutes($jamSelesai);
            return round($selisihMenit / 60, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
}