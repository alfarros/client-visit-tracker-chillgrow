<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jalankan backup database dan file setiap hari jam 00:00
Schedule::command('backup:run --only-db')->dailyAt('00:00');

// Jalankan pembersihan backup lama setiap hari jam 01:00
Schedule::command('backup:clean --only-db')->dailyAt('01:00');

Artisan::command('about:tracker', function () {
    $this->info('Client Visit Tracker — MVP');
})->purpose('Tampilkan informasi singkat aplikasi');

Schedule::command('kunjungan:mulai-otomatis')
    ->everyMinute()
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
