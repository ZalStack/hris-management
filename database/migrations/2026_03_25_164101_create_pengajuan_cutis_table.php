<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cutis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nama_karyawan', 255);
            $table->string('jenis_cuti', 30);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('total_hari');
            $table->text('alasan');
            $table->string('status', 20)->default('pending');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->string('lampiran', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->integer('tahun_cuti');
            $table->integer('kuota_total')->default(12);
            $table->integer('kuota_terpakai')->default(0);
            $table->integer('sisa_kuota')->default(12);
            $table->timestamps();
            
            $table->index('karyawan_id');
            $table->index('status');
            $table->index('tahun_cuti');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cutis');
    }
};