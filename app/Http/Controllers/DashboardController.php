<?php

namespace App\Http\Controllers;

use App\Models\KunjunganKlien;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today(config('app.timezone'))->toDateString();
        $visits = KunjunganKlien::query()
            ->whereDate('tanggal_kunjungan', $today)
            ->with('pasien')
            ->orderBy('jam_kunjungan')
            ->orderBy('id')
            ->limit(10)
            ->get();

        return view('dashboard', [
            'totalHariIni' => KunjunganKlien::query()->whereDate('tanggal_kunjungan', $today)->count(),
            'kunjunganHariIni' => $visits,
            'tanggalHariIni' => Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y'),
        ]);
    }
}
