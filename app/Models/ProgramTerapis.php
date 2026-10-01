<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramTerapis extends Model
{
    protected $fillable = ['kunjungan_klien_id', 'long_term_goals', 'short_term_goals', 'aktivitas_hari_ini', 'respon_anak'];

    public function kunjunganKlien(): BelongsTo
    {
        return $this->belongsTo(KunjunganKlien::class, 'kunjungan_klien_id');
    }
}
