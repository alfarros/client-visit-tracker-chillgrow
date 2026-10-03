@extends('layouts.app')

@section('title', 'Periksa Pasien')
@section('eyebrow', 'PERIKSA PASIEN')

@section('content')
    @php($readOnly = $kunjunganAktif?->status === 'Selesai')
    <div class="page-heading">
        <div><a class="back-link" href="{{ route('pasien.index') }}">← Kembali ke pasien</a>
            @if ($melihatRiwayat && $kunjunganAktif)
                <h1>Detail Kunjungan · {{ $kunjunganAktif->tanggal_kunjungan->locale('id')->translatedFormat('d F Y') }}</h1>
                <p class="muted">{{ $pasien->nama_lengkap }} · {{ $kunjunganAktif->status_label }} · Cara bayar {{ $kunjunganAktif->cara_bayar }}</p>
            @else
                <h1>Periksa Pasien</h1>
                <p class="muted">Lembar terapi, CPPT, dan riwayat kunjungan.</p>
            @endif
        </div>
        <div class="heading-actions">
            @if ($melihatRiwayat)
                <a class="button button-secondary" href="{{ route('pasien.show', $pasien) }}">← Kembali ke Profil Pasien</a>
            @else
                <a class="button button-secondary" href="{{ route('pasien.edit', $pasien) }}">Edit Pasien</a>
                <a class="button button-primary" href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}">＋ Jadwalkan
                    Kunjungan</a>
            @endif
            @if ($kunjunganAktif && $kunjunganAktif->status === 'Berlangsung')
                <form method="POST" action="{{ route('kunjungan.complete', $kunjunganAktif) }}"
                    onsubmit="return confirm('Tandai sesi terapi ini selesai? Catatan terapi dan CPPT akan dikunci.');">
                    @csrf @method('PATCH')
                    <button class="button button-success" type="submit">✓ Selesaikan Terapi</button>
                </form>
            @endif
        </div>
    </div>

    <section class="profile-card panel">
        <div class="profile-heading"><span class="profile-avatar"
                aria-hidden="true">{{ mb_strtoupper(mb_substr($pasien->nama_lengkap, 0, 1)) }}</span>
            <div><span class="eyebrow">PROFIL PASIEN</span>
                <h2>{{ $pasien->nama_lengkap }}</h2><span class="cell-mono">No. RM {{ $pasien->no_rekam_medis }}</span>
            </div>
        </div>
        <div class="profile-details">
            <div><span class="detail-label">Tanggal
                    Lahir</span><strong>{{ $pasien->tanggal_lahir->locale('id')->translatedFormat('d F Y') }}</strong></div>
            <div><span class="detail-label">Usia</span><strong>{{ $pasien->tanggal_lahir->age }} tahun</strong></div>
            <div class="diagnosis-detail"><span class="detail-label">Diagnosa
                    Awal</span><strong>{{ $pasien->diagnosa_awal ?: 'Belum dicatat' }}</strong></div>
        </div>
    </section>

    <section class="panel examination-panel">
            @if ($kunjunganAktif)
            <div class="examination-context">
                <div><span class="eyebrow">JADWAL TERAPI</span><strong>{{ $kunjunganAktif->tanggal_kunjungan->locale('id')->translatedFormat('d F Y') }} @if($kunjunganAktif->jam_kunjungan) · {{ substr((string) $kunjunganAktif->jam_kunjungan, 0, 5) }}@endif</strong>
                </div><div class="visit-context-badges"><span
                    class="badge badge-status-{{ str($kunjunganAktif->status)->lower() }}">{{ $kunjunganAktif->status_label }}</span>
                    <span class="badge badge-{{ strtolower($kunjunganAktif->cara_bayar) }}">{{ $kunjunganAktif->cara_bayar }}</span></div>
            </div>
            @if ($readOnly)
                <div class="readonly-banner">Terapi selesai. Lembar Program Terapi dan CPPT hanya dapat dilihat.</div>
            @endif
            @else
                <div class="examination-context"><div><span class="eyebrow">CATATAN PASIEN</span><strong>Belum ada jadwal terapi aktif</strong></div></div>
            @endif
            <div class="exam-tabs" role="tablist" aria-label="Catatan terapi">
                <button class="exam-tab is-active" id="tab-program-terapi" type="button" role="tab"
                    aria-selected="true" aria-controls="panel-program-terapi" data-tab-target="panel-program-terapi">Lembar
                    Program Terapi</button>
                <button class="exam-tab" id="tab-cppt" type="button" role="tab" aria-selected="false"
                    aria-controls="panel-cppt" data-tab-target="panel-cppt">CPPT</button>
                <button class="exam-tab" id="tab-progres-bulanan" type="button" role="tab" aria-selected="false"
                    aria-controls="panel-progres-bulanan" data-tab-target="panel-progres-bulanan">Progres Bulanan</button>
            </div>
            @if ($kunjunganAktif)
            <div class="exam-tab-panel" id="panel-program-terapi" role="tabpanel" aria-labelledby="tab-program-terapi">
                <form method="POST" action="{{ route('kunjungan.program.update', $kunjunganAktif) }}" class="form-stack">
                    @csrf @method('PUT')
                    <div class="field"><label for="long_term_goals">Tujuan Jangka Panjang</label>
                        <textarea id="long_term_goals" name="long_term_goals" rows="3" @disabled($readOnly)>{{ old('long_term_goals', $programTerapis?->long_term_goals) }}</textarea>
                        @error('long_term_goals')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field"><label for="short_term_goals">Tujuan Jangka Pendek</label>
                        <textarea id="short_term_goals" name="short_term_goals" rows="3" @disabled($readOnly)>{{ old('short_term_goals', $programTerapis?->short_term_goals) }}</textarea>
                        @error('short_term_goals')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field"><label for="aktivitas_hari_ini">Aktivitas Hari Ini</label>
                        <textarea id="aktivitas_hari_ini" name="aktivitas_hari_ini" rows="4" @disabled($readOnly)>{{ old('aktivitas_hari_ini', $programTerapis?->aktivitas_hari_ini) }}</textarea>
                        @error('aktivitas_hari_ini')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field"><label for="respon_anak">Respon Anak</label>
                        <textarea id="respon_anak" name="respon_anak" rows="4" @disabled($readOnly)>{{ old('respon_anak', $programTerapis?->respon_anak) }}</textarea>
                        @error('respon_anak')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    @unless ($readOnly)
                        <div class="form-actions"><button class="button button-primary" type="submit">Simpan Program
                                Terapi</button></div>
                    @endunless
                </form>
            </div>
            <div class="exam-tab-panel" id="panel-cppt" role="tabpanel" aria-labelledby="tab-cppt" hidden>
                <form method="POST" action="{{ route('kunjungan.cppt.update', $kunjunganAktif) }}" class="form-stack">
                    @csrf @method('PUT')
                    <div class="field-grid">
                        <div class="field"><label for="tanggal">Tanggal</label><input id="tanggal" name="tanggal"
                                type="date"
                                value="{{ old('tanggal', $cppt?->tanggal?->format('Y-m-d') ?? $kunjunganAktif->tanggal_kunjungan->format('Y-m-d')) }}"
                                @disabled($readOnly)>
                            @error('tanggal')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field"><label for="penanggung_jawab">Penanggung Jawab</label><input
                                id="penanggung_jawab" name="penanggung_jawab" type="text" maxlength="150"
                                value="{{ old('penanggung_jawab', $cppt?->penanggung_jawab) }}"
                                @disabled($readOnly)>
                            @error('penanggung_jawab')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @foreach (['subjective' => 'Subjective', 'objective' => 'Objective', 'assessment' => 'Assessment', 'planning' => 'Planning'] as $key => $label)
                        <div class="field"><label for="{{ $key }}">{{ $label }}</label>
                            <textarea id="{{ $key }}" name="{{ $key }}" rows="4" @disabled($readOnly)>{{ old($key, $cppt?->{$key}) }}</textarea>
                            @error($key)
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                    @unless ($readOnly)
                        <div class="form-actions"><button class="button button-primary" type="submit">Simpan CPPT</button>
                        </div>
                    @endunless
                </form>
            </div>
            @else
                <div class="exam-tab-panel" id="panel-program-terapi" role="tabpanel" aria-labelledby="tab-program-terapi">
                    <div class="empty-state"><strong>Belum ada jadwal terapi</strong><p>Jadwalkan kunjungan untuk mengisi Lembar Program Terapi dan CPPT.</p>
                        <a class="button button-primary" href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}">＋ Jadwalkan Kunjungan</a></div>
                </div>
                <div class="exam-tab-panel" id="panel-cppt" role="tabpanel" aria-labelledby="tab-cppt" hidden>
                    <div class="empty-state"><strong>Belum ada jadwal terapi</strong><p>CPPT dapat diisi setelah jadwal kunjungan dibuat.</p></div>
                </div>
            @endif

            <div class="exam-tab-panel monthly-progress-panel" id="panel-progres-bulanan" role="tabpanel" aria-labelledby="tab-progres-bulanan" hidden>
                <div class="panel-heading monthly-progress-heading">
                    <div><h2>Evaluasi Bulanan</h2><p class="muted">Catat perkembangan pasien pada lima aspek terapi.</p></div>
                    <button class="button button-primary" type="button" data-evaluation-open>＋ Tambah Evaluasi</button>
                </div>

                @if ($pasien->evaluasiBulanan->isEmpty())
                    <div class="empty-state"><span class="empty-icon" aria-hidden="true">▤</span><strong>Belum ada evaluasi bulanan</strong>
                        <p>Evaluasi yang disimpan akan muncul sebagai riwayat di sini.</p></div>
                @else
                    <div class="evaluation-accordion-list">
                        @foreach ($pasien->evaluasiBulanan as $evaluasi)
                            <details class="evaluation-accordion" @if ($loop->first) open @endif>
                                <summary><span>{{ $evaluasi->periode_tanggal->locale('id')->translatedFormat('F Y') }}</span><span class="accordion-chevron" aria-hidden="true">⌄</span></summary>
                                <div class="evaluation-actions">
                                    <button class="button button-secondary button-small" type="button" data-evaluation-edit
                                        data-id="{{ $evaluasi->id }}"
                                        data-action="{{ route('evaluasi-bulanan.update', $evaluasi) }}"
                                        data-periode-tanggal="{{ $evaluasi->periode_tanggal->format('Y-m-d') }}"
                                        data-sensori="{{ $evaluasi->sensori }}"
                                        data-motorik-kasar="{{ $evaluasi->motorik_kasar }}"
                                        data-motorik-halus="{{ $evaluasi->motorik_halus }}"
                                        data-kognitif-perseptual="{{ $evaluasi->kognitif_perseptual }}"
                                        data-kemandirian="{{ $evaluasi->kemandirian }}">Edit</button>
                                    <button class="button button-danger button-small" type="button" data-delete-trigger
                                        data-action="{{ route('evaluasi-bulanan.destroy', $evaluasi) }}"
                                        data-name="evaluasi {{ $evaluasi->periode_tanggal->locale('id')->translatedFormat('F Y') }}">Hapus</button>
                                </div>
                                <div class="evaluation-aspects">
                                    @foreach ([
                                        'sensori' => 'Sensori',
                                        'motorik_kasar' => 'Motorik Kasar',
                                        'motorik_halus' => 'Motorik Halus',
                                        'kognitif_perseptual' => 'Kognitif / Perseptual',
                                        'kemandirian' => 'Kemandirian',
                                    ] as $key => $label)
                                        <article class="evaluation-aspect"><h3>{{ $label }}</h3><p>{{ $evaluasi->{$key} ?: 'Belum ada catatan.' }}</p></article>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <div class="modal-backdrop evaluation-modal-backdrop" data-evaluation-modal @if ($errors->hasAny(['pasien_id', 'periode_tanggal', 'sensori', 'motorik_kasar', 'motorik_halus', 'kognitif_perseptual', 'kemandirian'])) data-reopen="true" @endif data-edit-id="{{ old('evaluasi_id') }}" hidden>
            <section class="evaluation-modal" role="dialog" aria-modal="true" aria-labelledby="evaluation-modal-title">
                <div class="evaluation-modal-heading"><div><span class="eyebrow">PROGRES BULANAN</span><h2 id="evaluation-modal-title" data-evaluation-modal-title>Tambah Evaluasi</h2></div>
                    <button class="modal-close" type="button" aria-label="Tutup" data-evaluation-close>×</button></div>
                <form method="POST" action="{{ route('evaluasi-bulanan.store') }}" class="form-stack evaluation-form" data-evaluation-form data-store-action="{{ route('evaluasi-bulanan.store') }}">
                    @csrf
                    <input type="hidden" name="_method" value="POST" data-evaluation-method>
                    <input type="hidden" name="evaluasi_id" value="{{ old('evaluasi_id') }}" data-evaluation-id>
                    <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                    <div class="field"><label for="periode_tanggal">Periode Evaluasi</label><input id="periode_tanggal" name="periode_tanggal" type="date" value="{{ old('periode_tanggal') }}" required>
                        @error('periode_tanggal')<p class="field-error">{{ $message }}</p>@enderror
                        @error('pasien_id')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    @foreach ([
                        'sensori' => 'Sensori',
                        'motorik_kasar' => 'Motorik Kasar',
                        'motorik_halus' => 'Motorik Halus',
                        'kognitif_perseptual' => 'Kognitif / Perseptual',
                        'kemandirian' => 'Kemandirian',
                    ] as $key => $label)
                        <div class="field"><label for="{{ $key }}">{{ $label }}</label><textarea id="{{ $key }}" name="{{ $key }}" rows="3" placeholder="Catatan perkembangan {{ strtolower($label) }}...">{{ old($key) }}</textarea>
                            @error($key)<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <div class="form-actions"><button class="button button-secondary" type="button" data-evaluation-close>Batal</button><button class="button button-primary" type="submit" data-evaluation-submit>Simpan Evaluasi</button></div>
                </form>
            </section>
        </div>

    <section class="panel history-panel">
        <div class="panel-heading">
            <div>
                <h2>Riwayat Kunjungan</h2>
                <p class="muted">{{ $pasien->kunjungan->count() }} kunjungan tercatat</p>
            </div><a class="text-link" href="{{ route('kunjungan.index', ['q' => $pasien->no_rekam_medis]) }}">Semua
                kunjungan →</a>
        </div>
        @if ($pasien->kunjungan->isEmpty())
            <div class="empty-state"><span class="empty-icon" aria-hidden="true">▤</span><strong>Belum ada riwayat
                    kunjungan</strong>
                <p>Jadwal kunjungan pasien akan tampil di sini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Cara Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasien->kunjungan as $kunjungan)
                            <tr>
                                <td>{{ $kunjungan->tanggal_kunjungan->locale('id')->translatedFormat('d F Y') }}</td>
                                <td>{{ $kunjungan->jam_kunjungan ? substr((string) $kunjungan->jam_kunjungan, 0, 5) : '—' }}</td>
                                <td><span
                                        class="badge badge-status-{{ str($kunjungan->status)->lower() }}">{{ $kunjungan->status_label }}</span>
                                </td>
                                <td><span
                                        class="badge badge-{{ strtolower($kunjungan->cara_bayar) }}">{{ $kunjungan->cara_bayar }}</span>
                                </td>
                                <td><a class="icon-button"
                                        href="{{ route('pasien.show', ['pasien' => $pasien->id, 'kunjungan' => $kunjungan->id]) }}">Lihat Kunjungan →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
