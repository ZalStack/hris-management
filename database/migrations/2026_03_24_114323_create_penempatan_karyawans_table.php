<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penempatan_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('jabatans')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('status')->default(true);
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->string('status_kepegawaian', 20)->nullable();
            $table->string('nomor_kontrak', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_karyawans');
    }
};