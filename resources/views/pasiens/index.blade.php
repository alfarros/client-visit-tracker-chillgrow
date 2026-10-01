@extends('layouts.app')

@section('title', 'Data Pasien')
@section('eyebrow', 'BUKU INDUK PASIEN')

@section('content')
    <div class="page-heading">
        <div><span class="eyebrow">MASTER DATA</span><h1>Data Pasien</h1><p class="muted">Cari pasien dan buka catatan pemeriksaannya.</p></div>
        <a class="button button-primary" href="{{ route('pasien.create') }}"><span aria-hidden="true">＋</span> Tambah Pasien</a>
    </div>
    <section class="panel">
        <form class="filters" method="GET" action="{{ route('pasien.index') }}">
            <div class="search-field"><label class="sr-only" for="q">Cari nama pasien atau No. RM</label><span aria-hidden="true">⌕</span><input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama pasien atau No. RM"></div>
            <button class="button button-secondary" type="submit">Cari</button>
            @if (!empty($filters['q']))<a class="reset-link" href="{{ route('pasien.index') }}">Reset</a>@endif
        </form>
        <div class="table-meta"><strong>{{ $pasiens->total() }} pasien</strong><span>Urut berdasarkan nama</span></div>
        @if ($pasiens->isEmpty())
            <div class="empty-state"><span class="empty-icon" aria-hidden="true">♙</span><strong>Belum ada data pasien</strong><p>Tambahkan pasien untuk mulai membuat jadwal kunjungan.</p><a class="button button-secondary" href="{{ route('pasien.create') }}">Tambah pasien</a></div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Nomor</th><th>Nama Pasien</th><th>No. Rekam Medis</th><th>Tanggal Lahir</th><th>Aksi</th></tr></thead><tbody>
                @foreach ($pasiens as $pasien)
                    <tr><td class="cell-id">{{ $pasiens->firstItem() + $loop->index }}</td><td class="cell-name"><a href="{{ route('pasien.show', $pasien) }}">{{ $pasien->nama_lengkap }}</a></td><td class="cell-mono">{{ $pasien->no_rekam_medis }}</td><td>{{ $pasien->tanggal_lahir->format('d M Y') }}</td><td class="row-actions"><a class="icon-button" href="{{ route('pasien.show', $pasien) }}">Periksa Pasien</a><a class="icon-button" href="{{ route('pasien.edit', $pasien) }}">Edit</a>@if (auth()->user()->role === 'admin')<button class="icon-button danger-text" type="button" data-delete-trigger data-action="{{ route('pasien.destroy', $pasien) }}" data-name="{{ $pasien->nama_lengkap }}">Hapus</button>@endif</td></tr>
                @endforeach
            </tbody></table></div>
            @if ($pasiens->hasPages())<div class="pagination-wrap custom-pagination"><span>Halaman {{ $pasiens->currentPage() }} dari {{ $pasiens->lastPage() }}</span><div>@if ($pasiens->onFirstPage())<span class="page-disabled">← Sebelumnya</span>@else<a href="{{ $pasiens->previousPageUrl() }}">← Sebelumnya</a>@endif @if ($pasiens->hasMorePages())<a href="{{ $pasiens->nextPageUrl() }}">Berikutnya →</a>@else<span class="page-disabled">Berikutnya →</span>@endif</div></div>@endif
        @endif
    </section>
@endsection
