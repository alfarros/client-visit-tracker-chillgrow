@extends('layouts.app')

@section('title', 'Periksa Pasien')
@section('eyebrow', 'PEMERIKSAAN PASIEN')

@section('content')
    @php($readOnly = $kunjunganAktif?->status === 'Selesai')
    <div class="page-heading">
        <div><a class="back-link" href="{{ route('pasien.index') }}">← Kembali ke pasien</a><h1>Periksa Pasien</h1><p class="muted">Lembar terapi, CPPT, dan riwayat kunjungan.</p></div>
        <div class="heading-actions">
            <a class="button button-secondary" href="{{ route('pasien.edit', $pasien) }}">Edit Pasien</a>
            <a class="button button-primary" href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}">＋ Jadwalkan Kunjungan</a>
            @if ($kunjunganAktif && $kunjunganAktif->status === 'Antre')
                <form method="POST" action="{{ route('kunjungan.complete', $kunjunganAktif) }}" onsubmit="return confirm('Tandai pemeriksaan ini selesai? Catatan terapi dan CPPT akan dikunci.');">
                    @csrf @method('PATCH')
                    <button class="button button-success" type="submit">✓ Selesaikan Pemeriksaan</button>
                </form>
            @endif
        </div>
    </div>

    <section class="profile-card panel">
        <div class="profile-heading"><span class="profile-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($pasien->nama_lengkap, 0, 1)) }}</span><div><span class="eyebrow">PROFIL PASIEN</span><h2>{{ $pasien->nama_lengkap }}</h2><span class="cell-mono">No. RM {{ $pasien->no_rekam_medis }}</span></div></div>
        <div class="profile-details"><div><span class="detail-label">Tanggal Lahir</span><strong>{{ $pasien->tanggal_lahir->locale('id')->translatedFormat('d F Y') }}</strong></div><div><span class="detail-label">Usia</span><strong>{{ $pasien->tanggal_lahir->age }} tahun</strong></div><div class="diagnosis-detail"><span class="detail-label">Diagnosa Awal</span><strong>{{ $pasien->diagnosa_awal ?: 'Belum dicatat' }}</strong></div></div>
    </section>

    @if ($kunjunganAktif)
        <section class="panel examination-panel">
            <div class="examination-context"><div><span class="eyebrow">KUNJUNGAN AKTIF</span><strong>{{ $kunjunganAktif->tanggal_kunjungan->locale('id')->translatedFormat('d F Y') }}</strong></div><span class="badge badge-status-{{ str($kunjunganAktif->status)->lower() }}">{{ $kunjunganAktif->status }}</span></div>
            @if ($readOnly)<div class="readonly-banner">Pemeriksaan selesai. Lembar Program Terapi dan CPPT hanya dapat dilihat.</div>@endif
            <div class="exam-tabs" role="tablist" aria-label="Catatan pemeriksaan">
                <button class="exam-tab is-active" id="tab-program-terapi" type="button" role="tab" aria-selected="true" aria-controls="panel-program-terapi" data-tab-target="panel-program-terapi">Lembar Program Terapi</button>
                <button class="exam-tab" id="tab-cppt" type="button" role="tab" aria-selected="false" aria-controls="panel-cppt" data-tab-target="panel-cppt">CPPT</button>
            </div>
            <div class="exam-tab-panel" id="panel-program-terapi" role="tabpanel" aria-labelledby="tab-program-terapi">
                <form method="POST" action="{{ route('kunjungan.program.update', $kunjunganAktif) }}" class="form-stack">
                    @csrf @method('PUT')
                    <div class="field"><label for="long_term_goals">Tujuan Jangka Panjang</label><textarea id="long_term_goals" name="long_term_goals" rows="3" @disabled($readOnly)>{{ old('long_term_goals', $programTerapis?->long_term_goals) }}</textarea>@error('long_term_goals')<p class="field-error">{{ $message }}</p>@enderror</div>
                    <div class="field"><label for="short_term_goals">Tujuan Jangka Pendek</label><textarea id="short_term_goals" name="short_term_goals" rows="3" @disabled($readOnly)>{{ old('short_term_goals', $programTerapis?->short_term_goals) }}</textarea>@error('short_term_goals')<p class="field-error">{{ $message }}</p>@enderror</div>
                    <div class="field"><label for="aktivitas_hari_ini">Aktivitas Hari Ini</label><textarea id="aktivitas_hari_ini" name="aktivitas_hari_ini" rows="4" @disabled($readOnly)>{{ old('aktivitas_hari_ini', $programTerapis?->aktivitas_hari_ini) }}</textarea>@error('aktivitas_hari_ini')<p class="field-error">{{ $message }}</p>@enderror</div>
                    <div class="field"><label for="respon_anak">Respon Anak</label><textarea id="respon_anak" name="respon_anak" rows="4" @disabled($readOnly)>{{ old('respon_anak', $programTerapis?->respon_anak) }}</textarea>@error('respon_anak')<p class="field-error">{{ $message }}</p>@enderror</div>
                    @unless($readOnly)<div class="form-actions"><button class="button button-primary" type="submit">Simpan Program Terapi</button></div>@endunless
                </form>
            </div>
            <div class="exam-tab-panel" id="panel-cppt" role="tabpanel" aria-labelledby="tab-cppt" hidden>
                <form method="POST" action="{{ route('kunjungan.cppt.update', $kunjunganAktif) }}" class="form-stack">
                    @csrf @method('PUT')
                    <div class="field-grid"><div class="field"><label for="tanggal">Tanggal</label><input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $cppt?->tanggal?->format('Y-m-d') ?? $kunjunganAktif->tanggal_kunjungan->format('Y-m-d')) }}" @disabled($readOnly)>@error('tanggal')<p class="field-error">{{ $message }}</p>@enderror</div><div class="field"><label for="penanggung_jawab">Penanggung Jawab</label><input id="penanggung_jawab" name="penanggung_jawab" type="text" maxlength="150" value="{{ old('penanggung_jawab', $cppt?->penanggung_jawab) }}" @disabled($readOnly)>@error('penanggung_jawab')<p class="field-error">{{ $message }}</p>@enderror</div></div>
                    @foreach (['subjective' => 'Subjective', 'objective' => 'Objective', 'assessment' => 'Assessment', 'planning' => 'Planning'] as $key => $label)<div class="field"><label for="{{ $key }}">{{ $label }}</label><textarea id="{{ $key }}" name="{{ $key }}" rows="4" @disabled($readOnly)>{{ old($key, $cppt?->{$key}) }}</textarea>@error($key)<p class="field-error">{{ $message }}</p>@enderror</div>@endforeach
                    @unless($readOnly)<div class="form-actions"><button class="button button-primary" type="submit">Simpan CPPT</button></div>@endunless
                </form>
            </div>
        </section>
    @else
        <section class="panel empty-examination"><strong>Belum ada kunjungan untuk diperiksa</strong><p class="muted">Buat jadwal kunjungan terlebih dahulu untuk mengisi Program Terapi dan CPPT.</p><a class="button button-primary" href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}">＋ Jadwalkan Kunjungan</a></section>
    @endif

    <section class="panel history-panel">
        <div class="panel-heading"><div><h2>Riwayat Kunjungan</h2><p class="muted">{{ $pasien->kunjungan->count() }} kunjungan tercatat</p></div><a class="text-link" href="{{ route('kunjungan.index', ['q' => $pasien->no_rekam_medis]) }}">Semua kunjungan →</a></div>
        @if ($pasien->kunjungan->isEmpty())<div class="empty-state"><span class="empty-icon" aria-hidden="true">▤</span><strong>Belum ada riwayat kunjungan</strong><p>Jadwal kunjungan pasien akan tampil di sini.</p></div>@else
            <div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Status</th><th>Cara Bayar</th><th>Aksi</th></tr></thead><tbody>@foreach ($pasien->kunjungan as $kunjungan)<tr><td>{{ $kunjungan->tanggal_kunjungan->locale('id')->translatedFormat('d F Y') }}</td><td><span class="badge badge-status-{{ str($kunjungan->status)->lower() }}">{{ $kunjungan->status }}</span></td><td><span class="badge badge-{{ strtolower($kunjungan->cara_bayar) }}">{{ $kunjungan->cara_bayar }}</span></td><td><a class="icon-button" href="{{ route('pasien.show', ['pasien' => $pasien->id, 'kunjungan' => $kunjungan->id]) }}">Periksa Pasien →</a></td></tr>@endforeach</tbody></table></div>
        @endif
    </section>
@endsection
