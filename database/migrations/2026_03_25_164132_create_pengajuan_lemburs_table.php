<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_lemburs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nama_karyawan', 255);
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_jam', 5, 2);
            $table->text('keterangan');
            $table->string('status', 20)->default('pending');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->string('nama_disetujui', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->string('lampiran', 255)->nullable();
            $table->decimal('tarif_lembur_per_jam', 15, 2)->nullable();
            $table->decimal('total_lembur', 15, 2)->nullable();
            $table->timestamps();
            
            $table->index('karyawan_id');
            $table->index('status');
            $table->index('tanggal_lembur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_lemburs');
    }
};