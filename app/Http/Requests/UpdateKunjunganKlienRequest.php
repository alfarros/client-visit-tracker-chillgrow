<?php

namespace App\Http\Requests;

class UpdateKunjunganKlienRequest extends StoreKunjunganKlienRequest
{
    public function rules(): array
    {
        return array_replace(parent::rules(), [
            'jam_kunjungan' => ['nullable', 'date_format:H:i'],
        ]);
    }
}
