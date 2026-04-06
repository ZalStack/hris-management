<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PenggajianApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/penggajian', [PenggajianApiController::class, 'index']);
Route::get('/penggajian/{id}', [PenggajianApiController::class, 'show']);
Route::post('/penggajian', [PenggajianApiController::class, 'store']);
Route::put('/penggajian/{id}', [PenggajianApiController::class, 'update']);
Route::patch('/penggajian/{id}', [PenggajianApiController::class, 'update']);
Route::delete('/penggajian/{id}', [PenggajianApiController::class, 'destroy']);
Route::patch('/penggajian/{id}/status', [PenggajianApiController::class, 'updateStatus']);
Route::get('/penggajian/karyawan/{karyawan_id}', [PenggajianApiController::class, 'getByKaryawan']);
Route::get('/penggajian/report/summary', [PenggajianApiController::class, 'getSummary']);