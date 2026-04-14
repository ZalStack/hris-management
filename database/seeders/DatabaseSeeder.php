<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        Karyawan::create([
            'nip' => 'ADMIN001',
            'email' => 'admin@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Administrator HRIS',
            'role' => 'admin',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        // Create HR User
        Karyawan::create([
            'nip' => 'HR001',
            'email' => 'hr@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Human Resources Manager',
            'role' => 'hr',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        // Create Sample Employee
        Karyawan::create([
            'nip' => 'EMP001',
            'email' => 'employee@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'John Doe',
            'role' => 'karyawan',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_pernikahan' => 'Belum Menikah',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'pendidikan_terakhir' => 'S1',
            'universitas' => 'Universitas Indonesia',
            'jurusan' => 'Teknik Informatika',
            'tahun_lulus' => 2015,
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh No. 123, Jakarta',
        ]);

        // Create additional sample employees
        Karyawan::create([
            'nip' => 'EMP002',
            'email' => 'jane@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Jane Smith',
            'role' => 'karyawan',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
            'jenis_kelamin' => 'Perempuan',
            'agama' => 'Kristen',
            'status_pernikahan' => 'Menikah',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1988-05-15',
            'pendidikan_terakhir' => 'S2',
            'universitas' => 'Universitas Padjadjaran',
            'jurusan' => 'Manajemen',
            'tahun_lulus' => 2012,
            'nomor_telepon' => '081234567891',
            'alamat' => 'Jl. Contoh No. 456, Bandung',
        ]);
    }
}