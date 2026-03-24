<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\PenempatanKaryawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = Karyawan::create([
            'nip' => 'ADMIN001',
            'email' => 'admin@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Administrator HRIS',
            'role' => 'admin',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        // Create HR User
        $hr = Karyawan::create([
            'nip' => 'HR001',
            'email' => 'hr@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Human Resources Manager',
            'role' => 'hr',
            'status' => 'aktif',
            'tanggal_bergabung' => now(),
        ]);

        // Create Sample Employee
        $employee = Karyawan::create([
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

        // Create Departments
        $itDept = Departemen::create([
            'kode_departemen' => 'IT',
            'nama_departemen' => 'Information Technology',
            'deskripsi' => 'Menangani infrastruktur IT dan pengembangan sistem',
            'kepala_departemen_id' => $employee->id,
        ]);

        $hrDept = Departemen::create([
            'kode_departemen' => 'HR',
            'nama_departemen' => 'Human Resources',
            'deskripsi' => 'Mengelola sumber daya manusia',
            'kepala_departemen_id' => $hr->id,
        ]);

        $financeDept = Departemen::create([
            'kode_departemen' => 'FIN',
            'nama_departemen' => 'Finance',
            'deskripsi' => 'Mengelola keuangan perusahaan',
        ]);

        // Create Positions
        $itManager = Jabatan::create([
            'kode_jabatan' => 'IT-MGR',
            'nama_jabatan' => 'IT Manager',
            'departemen_id' => $itDept->id,
            'deskripsi' => 'Mengelola tim IT',
            'gaji_minimal' => 10000000,
            'gaji_maksimal' => 20000000,
        ]);

        $developer = Jabatan::create([
            'kode_jabatan' => 'DEV',
            'nama_jabatan' => 'Developer',
            'departemen_id' => $itDept->id,
            'deskripsi' => 'Mengembangkan aplikasi',
            'gaji_minimal' => 5000000,
            'gaji_maksimal' => 15000000,
        ]);

        $hrManager = Jabatan::create([
            'kode_jabatan' => 'HR-MGR',
            'nama_jabatan' => 'HR Manager',
            'departemen_id' => $hrDept->id,
            'deskripsi' => 'Mengelola tim HR',
            'gaji_minimal' => 8000000,
            'gaji_maksimal' => 18000000,
        ]);

        $financeStaff = Jabatan::create([
            'kode_jabatan' => 'FIN-STF',
            'nama_jabatan' => 'Finance Staff',
            'departemen_id' => $financeDept->id,
            'deskripsi' => 'Mengelola transaksi keuangan',
            'gaji_minimal' => 4000000,
            'gaji_maksimal' => 10000000,
        ]);

        // Create Employee Placements
        PenempatanKaryawan::create([
            'karyawan_id' => $employee->id,
            'jabatan_id' => $developer->id,
            'tanggal_mulai' => now(),
            'status' => true,
            'gaji_pokok' => 7000000,
            'status_kepegawaian' => 'Tetap',
        ]);

        PenempatanKaryawan::create([
            'karyawan_id' => $hr->id,
            'jabatan_id' => $hrManager->id,
            'tanggal_mulai' => now(),
            'status' => true,
            'gaji_pokok' => 12000000,
            'status_kepegawaian' => 'Tetap',
        ]);

        PenempatanKaryawan::create([
            'karyawan_id' => $admin->id,
            'jabatan_id' => $itManager->id,
            'tanggal_mulai' => now(),
            'status' => true,
            'gaji_pokok' => 15000000,
            'status_kepegawaian' => 'Kontrak',
            'nomor_kontrak' => 'CONT-001',
        ]);
    }
}