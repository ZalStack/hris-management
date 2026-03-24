<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('email', 100)->unique();
            $table->string('kata_sandi');
            $table->string('nama_lengkap', 100);
            $table->string('role', 20)->default('karyawan');
            $table->string('foto_profil', 255)->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->string('nik', 16)->unique()->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('status_pernikahan', 20)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->string('universitas', 100)->nullable();
            $table->string('jurusan', 100)->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->string('nama_kontak_darurat', 100)->nullable();
            $table->string('telepon_kontak_darurat', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};