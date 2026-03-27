<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nama_karyawan', 255);
            $table->integer('bulan');
            $table->integer('tahun');
            $table->string('gaji_pokok', 50)->default('0');
            $table->string('total_tunjangan', 50)->default('0');
            $table->string('uang_lembur', 50)->default('0');
            $table->string('bonus', 50)->default('0');
            $table->string('total_potongan', 50)->default('0');
            $table->string('jumlah_pajak', 50)->default('0');
            $table->string('potongan_bpjs', 50)->default('0');
            $table->string('gaji_bersih', 50)->default('0');
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('metode_pembayaran', 30)->nullable();
            $table->string('nama_bank', 50)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('catatan')->nullable();
            $table->string('dibuat_oleh', 255);
            $table->text('detail_gaji')->nullable();
            $table->timestamps();
            
            $table->index('karyawan_id');
            $table->index(['bulan', 'tahun']);
            $table->index('status');
            $table->unique(['karyawan_id', 'bulan', 'tahun'], 'unique_penggajian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajians');
    }
};