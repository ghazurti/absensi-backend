<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\ShiftController;
use Illuminate\Support\Facades\Route;

// Auth (public)
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Shift (pegawai bisa CRUD shift sendiri)
    Route::apiResource('shifts', ShiftController::class);

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index']);
    Route::post('/absensi/check-in', [AbsensiController::class, 'checkIn']);
    Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut']);
    Route::get('/absensi/rekap', [AbsensiController::class, 'rekap']);

    // Izin
    Route::apiResource('izin', IzinController::class)->except(['update', 'edit', 'create']);

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::apiResource('pegawai', PegawaiController::class);
    });
});
