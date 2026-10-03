<?php

namespace App\Console\Commands;

use App\Models\KunjunganKlien;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class StartScheduledVisits extends Command
{
    protected $signature = 'kunjungan:mulai-otomatis';

    protected $description = 'Ubah kunjungan menjadi berlangsung saat jam jadwalnya tiba';

    public function handle(): int
    {
        $now = Carbon::now(config('app.timezone'));

        $updated = KunjunganKlien::query()
            ->whereDate('tanggal_kunjungan', $now->toDateString())
            ->whereNotNull('jam_kunjungan')
            ->whereTime('jam_kunjungan', '<=', $now->format('H:i:s'))
            ->where('status', 'Antre')
            ->update(['status' => 'Berlangsung']);

        $this->info("{$updated} kunjungan diubah menjadi Terapi Berlangsung.");

        return self::SUCCESS;
    }
}
