<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganKlien extends Model
{
    protected $fillable = [
        'pasien_id',
        'tanggal_kunjungan',
        'jam_kunjungan',
        'cara_bayar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'Berlangsung' ? 'Terapi Berlangsung' : $this->status;
    }

    public function pasien(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function cppts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cppt::class, 'kunjungan_klien_id');
    }

    public function programTerapis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProgramTerapis::class, 'kunjungan_klien_id');
    }
}
