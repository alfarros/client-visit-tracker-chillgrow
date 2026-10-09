<?php

namespace App\Http\Controllers\Concerns;

use App\Models\KunjunganKlien;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Mengisi file template Word (.docx) yang berada di resources/templates
 * dengan data rekam medis, lalu mengunduhnya.
 *
 * Template bisa diedit bebas di Microsoft Word. Gunakan placeholder
 * dengan format ${nama_placeholder}. Daftar placeholder lihat
 * resources/templates/README.md.
 */
trait ExportsMedicalRecordWord
{
    /**
     * Isi template, simpan ke berkas sementara, lalu kembalikan sebagai unduhan .docx.
     *
     * @param  string  $templateName  nama file relatif di resources/templates, mis. "cppt.docx"
     * @param  array<string, string|null>  $values  pasangan placeholder => nilai (tanpa `${}`)
     */
    protected function downloadWordFromTemplate(string $templateName, array $values, string $filename): BinaryFileResponse
    {
        $templatePath = resource_path('templates/'.$templateName);

        if (! is_file($templatePath)) {
            abort(500, 'Template dokumen Word tidak ditemukan.');
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'emr_word_');

        if ($tempPath === false) {
            abort(500, 'Tidak dapat membuat berkas sementara untuk ekspor Word.');
        }

        try {
            $processor = new TemplateProcessor($templatePath);
            $processor->setValues($this->normalizeWordValues($values));
            $processor->saveAs($tempPath);
        } catch (Throwable $exception) {
            @unlink($tempPath);
            report($exception);
            abort(500, 'Gagal membuat dokumen Word. Silakan coba lagi.');
        }

        return response()
            ->download($tempPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->deleteFileAfterSend(true);
    }

    /**
     * Nilai identitas pasien + kunjungan yang dipakai di semua template.
     *
     * @return array<string, string>
     */
    protected function patientWordValues(KunjunganKlien $kunjungan): array
    {
        $pasien = $kunjungan->pasien;

        return [
            'nama_pasien' => $pasien?->nama_lengkap ?: '—',
            'no_rekam_medis' => $pasien?->no_rekam_medis ?: '—',
            'tanggal_lahir' => $pasien?->tanggal_lahir?->locale('id')->translatedFormat('d F Y') ?: '—',
            'usia' => $pasien?->tanggal_lahir ? $pasien->tanggal_lahir->age.' tahun' : '—',
            'tanggal_kunjungan' => $kunjungan->tanggal_kunjungan->locale('id')->translatedFormat('d F Y'),
            'waktu_terapi' => $this->visitTimeRange($kunjungan),
            'diagnosa_awal' => $pasien?->diagnosa_awal ?: 'Belum dicatat',
        ];
    }

    /**
     * Ubah bagian nama file menjadi aman untuk diunduh.
     */
    protected function sanitizeFilenamePart(string $value, string $fallback = 'Pasien'): string
    {
        $value = preg_replace('/[^A-Za-z0-9]+/', '_', $value) ?? '';
        $value = trim($value, '_');

        return $value !== '' ? $value : $fallback;
    }

    /**
     * Pastikan semua nilai berupa string agar aman saat ditulis ke XML template.
     *
     * @param  array<string, string|null>  $values
     * @return array<string, string>
     */
    private function normalizeWordValues(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            $normalized[$key] = (string) ($value ?? '');
        }

        return $normalized;
    }

    private function visitTimeRange(KunjunganKlien $kunjungan): string
    {
        if (! $kunjungan->jam_kunjungan || ! $kunjungan->jam_selesai) {
            return '—';
        }

        return substr((string) $kunjungan->jam_kunjungan, 0, 5)
            .'–'.substr((string) $kunjungan->jam_selesai, 0, 5);
    }
}
