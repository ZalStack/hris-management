<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_karyawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nama_karyawan', 255);
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();  
            $table->time('jam_pulang')->nullable(); 
            $table->string('lokasi_masuk', 255)->nullable();
            $table->string('lokasi_pulang', 255)->nullable();
            $table->decimal('total_jam_kerja', 5, 2)->nullable();
            $table->string('status_kehadiran', 20)->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('karyawan_id');
            $table->index('tanggal');
            $table->index('status_kehadiran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_karyawans');
    }
};