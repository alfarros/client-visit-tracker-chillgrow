<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cppt extends Model
{
    protected $fillable = ['kunjungan_klien_id', 'tanggal', 'penanggung_jawab', 'subjective', 'objective', 'assessment', 'planning'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function kunjunganKlien(): BelongsTo
    {
        return $this->belongsTo(KunjunganKlien::class, 'kunjungan_klien_id');
    }
}
