<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'karyawans';
    
    protected $fillable = [
        'nip',
        'email',
        'kata_sandi',
        'nama_lengkap',
        'role',
        'foto_profil',
        'nomor_telepon',
        'alamat',
        'tanggal_bergabung',
        'status',
        'nik',
        'npwp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan',
        'pendidikan_terakhir',
        'universitas',
        'jurusan',
        'tahun_lulus',
        'nama_kontak_darurat',
        'telepon_kontak_darurat',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_lahir' => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function departemenDikepalai()
    {
        return $this->hasMany(Departemen::class, 'kepala_departemen_id');
    }

    public function penempatan()
    {
        return $this->hasMany(PenempatanKaryawan::class);
    }

    public function jabatanSaatIni()
    {
        return $this->hasOne(PenempatanKaryawan::class)
            ->where('status', true)
            ->with('jabatan');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHR()
    {
        return $this->role === 'hr';
    }
}