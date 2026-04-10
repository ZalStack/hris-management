<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PerformaController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Default dashboard route for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin() || auth()->user()->isHR()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('karyawan.dashboard');
    })->name('dashboard');
});

// Profile Routes (accessible by all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Performa Routes (Employee - View only)
Route::middleware('auth')
    ->prefix('performa')
    ->name('performa.')
    ->group(function () {
        Route::get('/', [PerformaController::class, 'index'])->name('index');
        Route::get('/{id}', [PerformaController::class, 'show'])->name('show');
    });

// Absensi Routes (for employees)
Route::middleware('auth')->group(function () {
    Route::prefix('absensi')
        ->name('absensi.')
        ->group(function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::get('/create', [AbsensiController::class, 'create'])->name('create');
            Route::post('/', [AbsensiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AbsensiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AbsensiController::class, 'update'])->name('update');
            Route::post('/{id}/pulang', [AbsensiController::class, 'absensiPulang'])->name('pulang');
        });
});

// Cuti Routes (Employee)
Route::middleware('auth')
    ->prefix('cuti')
    ->name('cuti.')
    ->group(function () {
        Route::get('/', [CutiController::class, 'index'])->name('index');
        Route::get('/create', [CutiController::class, 'create'])->name('create');
        Route::post('/', [CutiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CutiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CutiController::class, 'update'])->name('update');
        Route::delete('/{id}', [CutiController::class, 'destroy'])->name('destroy');
    });

// Pengumuman Routes (for employees to view)
Route::middleware('auth')->group(function () {
    Route::prefix('pengumuman')
        ->name('pengumuman.')
        ->group(function () {
            Route::get('/', [PengumumanController::class, 'employeeIndex'])->name('index');
            Route::get('/{id}', [PengumumanController::class, 'employeeShow'])->name('show');
        });
});

// Notifikasi Routes (for all authenticated users)
Route::middleware('auth')->group(function () {
    Route::prefix('notifikasi')
        ->name('notifikasi.')
        ->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('unread-count');
        });
});

// Penggajian Routes (Employee)
Route::middleware('auth')
    ->prefix('penggajian')
    ->name('penggajian.')
    ->group(function () {
        Route::get('/', [PenggajianController::class, 'index'])->name('index');
        Route::get('/{id}', [PenggajianController::class, 'show'])->name('show');
    });

// Admin/HR Routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Employee Management
        Route::get('/karyawan', [AdminDashboardController::class, 'karyawan'])->name('karyawan');
        Route::post('/karyawan', [AdminDashboardController::class, 'storeKaryawan'])->name('karyawan.store');
        Route::get('/karyawan/{id}/edit', [AdminDashboardController::class, 'editKaryawan'])->name('karyawan.edit');
        Route::put('/karyawan/{id}', [AdminDashboardController::class, 'updateKaryawan'])->name('karyawan.update');
        Route::delete('/karyawan/{id}', [AdminDashboardController::class, 'destroyKaryawan'])->name('karyawan.destroy');
        Route::get('/karyawan/{id}/show-password', [AdminDashboardController::class, 'showPassword'])->name('karyawan.show-password');

        // Department Management
        Route::resource('departemen', DepartmentController::class);

        // Position Management
        Route::resource('jabatan', PositionController::class);
        Route::get('/jabatan-by-departemen/{departemen_id}', [PositionController::class, 'getByDepartemen'])->name('jabatan.by-departemen');

        // Absensi Management for Admin/HR
        Route::prefix('absensi')
            ->name('absensi.')
            ->group(function () {
                Route::get('/', [AbsensiController::class, 'adminIndex'])->name('index');
                Route::put('/{id}/status', [AbsensiController::class, 'adminUpdateStatus'])->name('update-status');
                Route::get('/{id}', [AbsensiController::class, 'adminShow'])->name('show');
            });

        // Cuti Management for Admin/HR
        Route::prefix('cuti')
            ->name('cuti.')
            ->group(function () {
                Route::get('/', [CutiController::class, 'adminIndex'])->name('index');
                Route::put('/{id}/status', [CutiController::class, 'adminUpdateStatus'])->name('update-status');
                Route::get('/{id}', [CutiController::class, 'adminShow'])->name('show');
            });

        // Penggajian Management
        Route::prefix('penggajian')
            ->name('penggajian.')
            ->group(function () {
                Route::get('/', [PenggajianController::class, 'adminIndex'])->name('index');
                Route::get('/create', [PenggajianController::class, 'adminCreate'])->name('create');
                Route::post('/calculate', [PenggajianController::class, 'calculateSalary'])->name('calculate');
                Route::post('/', [PenggajianController::class, 'adminStore'])->name('store');
                Route::get('/{id}/edit', [PenggajianController::class, 'adminEdit'])->name('edit');
                Route::put('/{id}', [PenggajianController::class, 'adminUpdate'])->name('update');
                Route::delete('/{id}', [PenggajianController::class, 'adminDestroy'])->name('destroy');
                Route::put('/{id}/status', [PenggajianController::class, 'adminUpdateStatus'])->name('update-status');
                Route::get('/{id}', [PenggajianController::class, 'adminShow'])->name('show');
                Route::get('/export/report', [PenggajianController::class, 'exportReport'])->name('export');
            });

        // Pengumuman Management for Admin/HR
        Route::resource('pengumuman', PengumumanController::class);

        // Performa Management
        Route::prefix('performa')
            ->name('performa.')
            ->group(function () {
                Route::get('/', [PerformaController::class, 'adminIndex'])->name('index');
                Route::get('/create', [PerformaController::class, 'adminCreate'])->name('create');
                Route::get('/bulk', [PerformaController::class, 'adminBulkCreate'])->name('bulk');
                Route::post('/bulk', [PerformaController::class, 'adminBulkStore'])->name('bulk.store');
                Route::get('/karyawan/{id}', [PerformaController::class, 'getKaryawanData'])->name('get-karyawan');
                Route::post('/', [PerformaController::class, 'adminStore'])->name('store');
                Route::get('/{id}/edit', [PerformaController::class, 'adminEdit'])->name('edit');
                Route::put('/{id}', [PerformaController::class, 'adminUpdate'])->name('update');
                Route::delete('/{id}', [PerformaController::class, 'adminDestroy'])->name('destroy');
                Route::get('/{id}', [PerformaController::class, 'adminShow'])->name('show');
                Route::post('/check-reset', [PerformaController::class, 'checkAndResetKPI'])->name('check-reset');
            });
    });

// Employee Routes
Route::middleware(['auth', 'karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
    });

require __DIR__ . '/auth.php';
