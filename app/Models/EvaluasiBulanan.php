<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiBulanan extends Model
{
    protected $fillable = [
        'pasien_id',
        'periode_tanggal',
        'sensori',
        'motorik_kasar',
        'motorik_halus',
        'kognitif_perseptual',
        'kemandirian',
    ];

    protected function casts(): array
    {
        return ['periode_tanggal' => 'date'];
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }
}
