@extends('layouts.app')

@section('title', 'Dashboard')
@section('eyebrow', 'RINGKASAN HARIAN')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">{{ $tanggalHariIni }}</span>
            <h1>Jadwal Hari Ini</h1>
            <p class="muted">Ringkasan kunjungan klien yang terjadwal hari ini.</p>
        </div>
        <a class="button button-primary" href="{{ route('kunjungan.create') }}"><span aria-hidden="true">＋</span> Tambah
            Kunjungan</a>
    </div>

    <section class="metric-card" aria-label="Total pasien hari ini">
        <div class="metric-icon" aria-hidden="true">♧</div>
        <div><span class="metric-label">TOTAL PASIEN HARI INI</span><strong
                class="metric-value">{{ $totalHariIni }}</strong><span class="metric-note">jadwal kunjungan</span></div>
        <div class="metric-date">{{ now(config('app.timezone'))->format('d.m.Y') }}</div>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <h2>Daftar Kunjungan</h2>
                <p class="muted">Jadwal yang berlangsung hari ini</p>
            </div><a class="text-link"
                href="{{ route('kunjungan.index', ['tanggal_mulai' => now(config('app.timezone'))->toDateString(), 'tanggal_selesai' => now(config('app.timezone'))->toDateString()]) }}">Lihat
                semua <span aria-hidden="true">→</span></a>
        </div>
        @if ($kunjunganHariIni->isEmpty())
            <div class="empty-state"><span class="empty-icon" aria-hidden="true">☼</span><strong>Belum ada jadwal hari
                    ini</strong>
                <p>Jadwal kunjungan yang ditambahkan akan muncul di sini.</p><a class="button button-secondary"
                    href="{{ route('kunjungan.create') }}">Tambah jadwal pertama</a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Pasien</th>
                            <th>No. Rekam Medis</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Cara Bayar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kunjunganHariIni as $item)
                            <tr>
                                <td class="cell-name">{{ $item->pasien->nama_lengkap }}</td>
                                <td class="cell-mono">{{ $item->pasien->no_rekam_medis }}</td>
                                <td>{{ $item->tanggal_kunjungan->format('d M Y') }}</td>
                                <td>{{ $item->jam_kunjungan ? substr((string) $item->jam_kunjungan, 0, 5) : '—' }}</td>
                                <td><span
                                        class="badge badge-{{ strtolower($item->cara_bayar) }}">{{ $item->cara_bayar }}</span>
                                </td>
                                <td><span
                                        class="badge badge-status-{{ str($item->status)->lower() }}">{{ $item->status_label }}</span>
                                </td>
                                <td class="cell-action"><a class="icon-button"
                                        href="{{ route('pasien.show', ['pasien' => $item->pasien_id, 'kunjungan' => $item->id]) }}">Periksa Pasien</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
