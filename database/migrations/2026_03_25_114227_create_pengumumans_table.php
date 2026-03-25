<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('konten');
            $table->string('kategori', 50)->nullable();
            $table->string('target_role', 20)->nullable();
            $table->string('target_departemen', 255)->nullable();
            $table->boolean('status')->default(false);
            $table->string('lampiran', 255)->nullable();
            $table->timestamp('tanggal_terbit')->nullable();
            $table->date('tanggal_berlaku_hingga')->nullable();
            $table->unsignedBigInteger('dibuat_oleh');
            $table->timestamps();
            
            $table->index('status');
            $table->index('tanggal_terbit');
            $table->index('target_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};