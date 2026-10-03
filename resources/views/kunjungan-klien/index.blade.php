@extends('layouts.app')

@section('title', 'Kunjungan Klien')
@section('eyebrow', 'DATA KUNJUNGAN')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">CATATAN JADWAL KLIEN</span>
            <h1>Kunjungan Klien</h1>
            <p class="muted">Cari, lihat, dan kelola jadwal kunjungan klien.</p>
        </div>
        <a class="button button-primary" href="{{ route('kunjungan.create') }}"><span aria-hidden="true">＋</span> Tambah
            Kunjungan</a>
    </div>

    <section class="panel">
        <form class="filters" method="GET" action="{{ route('kunjungan.index') }}">
            <div class="search-field"><label class="sr-only" for="q">Cari nama pasien atau No. RM</label><span
                    aria-hidden="true">⌕</span><input id="q" type="search" name="q"
                    value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama pasien atau No. RM"></div>
            <div class="date-filter"><label for="tanggal_mulai">Dari</label><input id="tanggal_mulai" type="date"
                    name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}"></div>
            <div class="date-filter"><label for="tanggal_selesai">Sampai</label><input id="tanggal_selesai" type="date"
                    name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}"></div>
            <button class="button button-secondary" type="submit">Cari</button>
            @if (count(array_filter($filters)))
                <a class="reset-link" href="{{ route('kunjungan.index') }}">Reset</a>
            @endif
        </form>
        <div class="table-meta"><strong>{{ $kunjungan->total() }} jadwal</strong><span>Diurutkan dari tanggal terbaru</span>
        </div>
        @if ($kunjungan->isEmpty())
            <div class="empty-state"><span class="empty-icon" aria-hidden="true">⌕</span><strong>Tidak ada jadwal
                    ditemukan</strong>
                <p>Coba ubah kata pencarian atau rentang tanggal.</p>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kunjungan as $item)
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
                                <td class="row-actions">
                                    <details class="action-menu">
                                        <summary class="icon-button action-trigger" title="Buka menu aksi"
                                            aria-label="Aksi untuk {{ $item->pasien->nama_lengkap }}"><img
                                                src="{{ asset('icons/menu.png') }}" alt="" width="20"
                                                height="20"></summary>
                                        <div class="action-menu-items"><a
                                                href="{{ route('pasien.show', ['pasien' => $item->pasien_id, 'kunjungan' => $item->id]) }}"
                                                title="Periksa Pasien">Periksa</a><a
                                                href="{{ route('kunjungan.edit', $item) }}"
                                                title="Ubah jadwal kunjungan">Edit Jadwal</a>
                                            @if ($item->status === 'Berlangsung')
                                                <form method="POST" action="{{ route('kunjungan.complete', $item) }}"
                                                    onsubmit="return confirm('Tandai sesi terapi {{ $item->pasien->nama_lengkap }} selesai? Catatan terapi dan CPPT akan dikunci.');">
                                                    @csrf @method('PATCH')<button type="submit"
                                                        title="Simpan status selesai dan kunci catatan">Selesaikan
                                                        Terapi</button></form>
                                                @endif @if (auth()->user()->role === 'admin')
                                                    <button class="danger-text" type="button" data-delete-trigger
                                                        data-action="{{ route('kunjungan.destroy', $item) }}"
                                                        data-name="{{ $item->pasien->nama_lengkap }}"
                                                        title="Hapus jadwal kunjungan">Hapus Kunjungan</button>
                                                @endif
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($kunjungan->hasPages())
                <div class="pagination-wrap custom-pagination">
                    <span>Halaman {{ $kunjungan->currentPage() }} dari {{ $kunjungan->lastPage() }}</span>
                    <div>
                        @if ($kunjungan->onFirstPage())
                        <span class="page-disabled">← Sebelumnya</span>@else<a
                                href="{{ $kunjungan->previousPageUrl() }}">← Sebelumnya</a>
                        @endif
                        @if ($kunjungan->hasMorePages())
                        <a href="{{ $kunjungan->nextPageUrl() }}">Berikutnya →</a>@else<span
                                class="page-disabled">Berikutnya →</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </section>
@endsection
