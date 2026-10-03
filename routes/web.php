<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CpptController;
use App\Http\Controllers\EvaluasiBulananController;
use App\Http\Controllers\KunjunganKlienController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProgramTerapisController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('pasien', PasienController::class)->except(['destroy']);
    Route::post('/evaluasi-bulanan', [EvaluasiBulananController::class, 'store'])->name('evaluasi-bulanan.store');
    Route::put('/evaluasi-bulanan/{evaluasiBulanan}', [EvaluasiBulananController::class, 'update'])->name('evaluasi-bulanan.update');
    Route::delete('/evaluasi-bulanan/{evaluasiBulanan}', [EvaluasiBulananController::class, 'destroy'])->name('evaluasi-bulanan.destroy');
    Route::delete('/pasien/{pasien}', [PasienController::class, 'destroy'])
        ->middleware('admin')
        ->name('pasien.destroy');

    Route::resource('kunjungan', KunjunganKlienController::class)
        ->parameters(['kunjungan' => 'kunjunganKlien'])
        ->except(['show', 'destroy']);
    Route::delete('/kunjungan/{kunjunganKlien}', [KunjunganKlienController::class, 'destroy'])
        ->middleware('admin')
        ->name('kunjungan.destroy');
    Route::patch('/kunjungan/{kunjunganKlien}/selesai', [KunjunganKlienController::class, 'complete'])
        ->name('kunjungan.complete');
    Route::put('/kunjungan/{kunjunganKlien}/program-terapis', [ProgramTerapisController::class, 'update'])
        ->name('kunjungan.program.update');
    Route::get('/kunjungan/{kunjunganKlien}/cppt', [CpptController::class, 'show'])->name('kunjungan.cppt.show');
    Route::put('/kunjungan/{kunjunganKlien}/cppt', [CpptController::class, 'update'])->name('kunjungan.cppt.update');
});
