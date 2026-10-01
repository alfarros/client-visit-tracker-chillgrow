<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:tracker', function () {
    $this->info('Client Visit Tracker — MVP');
})->purpose('Tampilkan informasi singkat aplikasi');
