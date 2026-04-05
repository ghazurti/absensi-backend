<?php

use App\Http\Controllers\Web\AbsensiController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\IzinController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\SkorController;
use App\Http\Controllers\Web\PegawaiController;
use App\Http\Controllers\Web\ShiftController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard atau login
Route::get('/', fn() => redirect()->route('dashboard'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/check-in', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');

    // Shift
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
    Route::delete('/shift/{shift}', [ShiftController::class, 'destroy'])->name('shift.destroy');

    // Izin
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::delete('/izin/{izin}', [IzinController::class, 'destroy'])->name('izin.destroy');

    // Admin only
    Route::middleware('admin')->group(function () {
        // Kelola Pegawai
        Route::resource('/pegawai', PegawaiController::class);

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

        // Skor Kehadiran
        Route::get('/laporan/skor', [SkorController::class, 'index'])->name('skor.index');
        Route::get('/laporan/skor/cetak', [SkorController::class, 'cetak'])->name('skor.cetak');
    });
});
