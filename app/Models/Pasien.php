<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $fillable = ['no_rekam_medis', 'nama_lengkap', 'tanggal_lahir', 'diagnosa_awal'];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function kunjungan(): HasMany
    {
        return $this->hasMany(KunjunganKlien::class);
    }
}
