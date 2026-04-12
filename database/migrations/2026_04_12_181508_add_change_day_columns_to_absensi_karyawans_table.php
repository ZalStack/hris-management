<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_karyawans', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('absensi_karyawans', 'is_change_day')) {
                $table->boolean('is_change_day')->default(false)->after('keterangan');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_tanggal_awal')) {
                $table->date('change_day_tanggal_awal')->nullable()->after('is_change_day');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_tanggal_akhir')) {
                $table->date('change_day_tanggal_akhir')->nullable()->after('change_day_tanggal_awal');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_jam_mulai')) {
                $table->time('change_day_jam_mulai')->nullable()->after('change_day_tanggal_akhir');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_jam_selesai')) {
                $table->time('change_day_jam_selesai')->nullable()->after('change_day_jam_mulai');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_alasan')) {
                $table->text('change_day_alasan')->nullable()->after('change_day_jam_selesai');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_status')) {
                $table->string('change_day_status', 20)->default('pending')->after('change_day_alasan');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_catatan_admin')) {
                $table->text('change_day_catatan_admin')->nullable()->after('change_day_status');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_disetujui_pada')) {
                $table->timestamp('change_day_disetujui_pada')->nullable()->after('change_day_catatan_admin');
            }
            if (!Schema::hasColumn('absensi_karyawans', 'change_day_disetujui_oleh')) {
                $table->unsignedBigInteger('change_day_disetujui_oleh')->nullable()->after('change_day_disetujui_pada');
            }
            
            // Index
            $table->index('is_change_day');
            $table->index('change_day_status');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_karyawans', function (Blueprint $table) {
            $table->dropColumn([
                'is_change_day',
                'change_day_tanggal_awal',
                'change_day_tanggal_akhir',
                'change_day_jam_mulai',
                'change_day_jam_selesai',
                'change_day_alasan',
                'change_day_status',
                'change_day_catatan_admin',
                'change_day_disetujui_pada',
                'change_day_disetujui_oleh',
            ]);
        });
    }
};