<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CpptController;
use App\Http\Controllers\DokumenPasienController;
use App\Http\Controllers\EvaluasiBulananController;
use App\Http\Controllers\KunjunganKlienController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProgramTerapisController;
use App\Http\Controllers\UserController;
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

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('super-admin')->group(function (): void {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::resource('pasien', PasienController::class)->except(['destroy']);
    Route::post('/pasien/{pasien}/dokumen', [DokumenPasienController::class, 'store'])->name('pasien.dokumen.store');
    Route::get('/pasien/{pasien}/dokumen/{dokumenPasien}/preview', [DokumenPasienController::class, 'preview'])->name('pasien.dokumen.preview');
    Route::get('/pasien/{pasien}/dokumen/{dokumenPasien}/download', [DokumenPasienController::class, 'download'])->name('pasien.dokumen.download');
    Route::delete('/pasien/{pasien}/dokumen/{dokumenPasien}', [DokumenPasienController::class, 'destroy'])->name('pasien.dokumen.destroy');
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
    Route::get('/kunjungan/{kunjunganKlien}/cppt/word', [CpptController::class, 'exportWord'])->name('kunjungan.cppt.word');
    Route::get('/kunjungan/{kunjunganKlien}/program-terapis/word', [ProgramTerapisController::class, 'exportWord'])->name('kunjungan.program.word');
});
