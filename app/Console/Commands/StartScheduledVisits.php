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
        $today = $now->toDateString();
        $activeStatuses = ['Antre', 'Berlangsung', 'Menunggu Diselesaikan'];

        $pastVisits = KunjunganKlien::query()
            ->whereDate('tanggal_kunjungan', '<', $today)
            ->whereIn('status', ['Antre', 'Berlangsung'])
            ->update(['status' => 'Menunggu Diselesaikan']);

        $pastTodayVisits = KunjunganKlien::query()
            ->whereDate('tanggal_kunjungan', $today)
            ->whereNotNull('jam_selesai')
            ->whereTime('jam_selesai', '<', $now->format('H:i:s'))
            ->whereIn('status', ['Antre', 'Berlangsung'])
            ->update(['status' => 'Menunggu Diselesaikan']);

        $ongoingVisits = KunjunganKlien::query()
            ->whereDate('tanggal_kunjungan', $today)
            ->whereNotNull('jam_kunjungan')
            ->whereNotNull('jam_selesai')
            ->whereTime('jam_kunjungan', '<=', $now->format('H:i:s'))
            ->whereTime('jam_selesai', '>=', $now->format('H:i:s'))
            ->whereIn('status', $activeStatuses)
            ->update(['status' => 'Berlangsung']);

        $waiting = $pastVisits + $pastTodayVisits;
        $this->info("{$ongoingVisits} kunjungan berlangsung; {$waiting} menunggu diselesaikan.");

        return self::SUCCESS;
    }
}
