<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Performa extends Model
{
    use HasFactory;

    protected $table = 'performas';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'departemen',
        'position',
        'email',
        'phone',
        'join_date',
        'bulan',
        'tahun',
        'attendance_rate',
        'quality',
        'productivity',
        'teamwork',
        'discipline',
        'kpi_score',
        'performance_score',
        'catatan',
        'quarter',
    ];

    protected $casts = [
        'join_date' => 'date',
        'attendance_rate' => 'integer',
        'quality' => 'integer',
        'productivity' => 'integer',
        'teamwork' => 'integer',
        'discipline' => 'integer',
        'kpi_score' => 'integer',
        'performance_score' => 'integer',
    ];

    // Calculate performance score
    public static function calculatePerformanceScore($attendance_rate, $quality, $productivity, $teamwork, $discipline, $kpi_score)
    {
        // Weight distribution
        $weights = [
            'attendance_rate' => 0.15, // 15%
            'quality' => 0.20,         // 20%
            'productivity' => 0.20,    // 20%
            'teamwork' => 0.15,        // 15%
            'discipline' => 0.15,      // 15%
            'kpi_score' => 0.15,       // 15%
        ];
        
        $score = ($attendance_rate * $weights['attendance_rate']) +
                 ($quality * $weights['quality']) +
                 ($productivity * $weights['productivity']) +
                 ($teamwork * $weights['teamwork']) +
                 ($discipline * $weights['discipline']) +
                 ($kpi_score * $weights['kpi_score']);
        
        return round($score);
    }

    // Get rating based on performance score
    public function getRatingAttribute()
    {
        if ($this->performance_score >= 90) {
            return ['label' => 'Sangat Baik (A)', 'color' => 'green'];
        } elseif ($this->performance_score >= 75) {
            return ['label' => 'Baik (B)', 'color' => 'blue'];
        } elseif ($this->performance_score >= 60) {
            return ['label' => 'Cukup (C)', 'color' => 'yellow'];
        } elseif ($this->performance_score >= 50) {
            return ['label' => 'Kurang (D)', 'color' => 'orange'];
        } else {
            return ['label' => 'Sangat Kurang (E)', 'color' => 'red'];
        }
    }

    // Get bulan text
    public function getBulanTextAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    // Get quarter text
    public function getQuarterTextAttribute()
    {
        return match($this->quarter) {
            'Q1' => 'Januari - Maret',
            'Q2' => 'April - Juni',
            'Q3' => 'Juli - September',
            'Q4' => 'Oktober - Desember',
            default => '-',
        };
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}