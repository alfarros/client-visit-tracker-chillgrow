<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Pasien;

class StoreKunjunganKlienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pasien_id' => ['required', 'integer', Rule::exists((new Pasien())->getTable(), 'id')],
            'tanggal_kunjungan' => ['required', 'date_format:Y-m-d'],
            'jam_kunjungan' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_kunjungan'],
            'cara_bayar' => ['required', Rule::in(['Tunai', 'Transfer'])],
        ];
    }

    public function messages(): array
    {
        return [
            'pasien_id.required' => 'Pilih pasien terlebih dahulu.',
            'pasien_id.exists' => 'Pasien yang dipilih tidak ditemukan.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.date_format' => 'Masukkan tanggal kunjungan yang valid.',
            'jam_kunjungan.required' => 'Jam kunjungan wajib diisi.',
            'jam_kunjungan.date_format' => 'Masukkan jam kunjungan yang valid.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Masukkan jam selesai yang valid.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'cara_bayar.required' => 'Pilih cara bayar.',
            'cara_bayar.in' => 'Pilih salah satu cara bayar yang tersedia.',
        ];
    }
}
