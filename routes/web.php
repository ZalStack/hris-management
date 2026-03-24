<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PlacementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Admin/HR Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Employee Management
    Route::get('/karyawan', [AdminDashboardController::class, 'karyawan'])->name('karyawan');
    Route::post('/karyawan', [AdminDashboardController::class, 'storeKaryawan'])->name('karyawan.store');
    Route::get('/karyawan/{id}/edit', [AdminDashboardController::class, 'editKaryawan'])->name('karyawan.edit');
    Route::put('/karyawan/{id}', [AdminDashboardController::class, 'updateKaryawan'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [AdminDashboardController::class, 'destroyKaryawan'])->name('karyawan.destroy');
    Route::get('/karyawan/{id}/password', [AdminDashboardController::class, 'showKaryawanPassword'])->name('karyawan.password');
    
    // Department Management
    Route::resource('departemen', DepartmentController::class);
    
    // Position Management
    Route::resource('jabatan', PositionController::class);
    Route::get('/jabatan-by-departemen/{departemen_id}', [PositionController::class, 'getByDepartemen'])->name('jabatan.by-departemen');
    
    // Employee Placement Management
    Route::resource('penempatan', PlacementController::class);
    Route::get('/penempatan-karyawan', [PlacementController::class, 'index'])->name('penempatan.index');
    Route::get('/penempatan/karyawan/{id}', [PlacementController::class, 'getPlacementByKaryawan'])->name('penempatan.by-karyawan');
});

// Employee Routes
Route::middleware(['auth', 'karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';