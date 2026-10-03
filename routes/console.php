<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('about:tracker', function () {
    $this->info('Client Visit Tracker — MVP');
})->purpose('Tampilkan informasi singkat aplikasi');

Schedule::command('kunjungan:mulai-otomatis')
    ->everyMinute()
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
