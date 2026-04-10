<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nama_karyawan', 255);
            $table->string('departemen', 100);
            $table->string('position', 100);
            $table->string('email', 100);
            $table->string('phone', 20)->nullable();
            $table->date('join_date');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('attendance_rate')->default(0); // 0-100
            $table->integer('quality')->default(0); // 0-100
            $table->integer('productivity')->default(0); // 0-100
            $table->integer('teamwork')->default(0); // 0-100
            $table->integer('discipline')->default(0); // 0-100
            $table->integer('kpi_score')->default(0); // 0-100
            $table->integer('performance_score')->default(0); // Total dari semua komponen
            $table->text('catatan')->nullable();
            $table->string('quarter', 10); 
            $table->timestamps();
            
            $table->index('karyawan_id');
            $table->index(['bulan', 'tahun']);
            $table->index('quarter');
            $table->unique(['karyawan_id', 'bulan', 'tahun'], 'unique_performa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performas');
    }
};