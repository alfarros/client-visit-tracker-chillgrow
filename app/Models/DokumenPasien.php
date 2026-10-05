<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPasien extends Model
{
    public const JENIS_DOKUMEN = [
        'Asesmen Awal',
        'Re-Asesmen',
        'Informed Consent',
        'Lainnya',
    ];

    protected $fillable = [
        'pasien_id',
        'jenis_dokumen',
        'nama_file_asli',
        'file_path',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }
}
