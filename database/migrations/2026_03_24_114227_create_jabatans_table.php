<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jabatan', 20)->unique();
            $table->string('nama_jabatan', 100);
            $table->foreignId('departemen_id')->constrained('departemens')->onDelete('cascade');
            $table->text('deskripsi')->nullable();
            $table->decimal('gaji_minimal', 15, 2)->nullable();
            $table->decimal('gaji_maksimal', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};