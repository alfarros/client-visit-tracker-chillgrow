<?php

namespace App\Http\Controllers;

use App\Models\DokumenPasien;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DokumenPasienController extends Controller
{
    public function store(Request $request, Pasien $pasien): RedirectResponse
    {
        $data = $request->validate([
            'jenis_dokumen' => ['required', 'string', 'in:'.implode(',', DokumenPasien::JENIS_DOKUMEN)],
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen.in' => 'Jenis dokumen yang dipilih tidak valid.',
            'dokumen.required' => 'Berkas wajib dipilih.',
            'dokumen.file' => 'Berkas yang dipilih tidak valid.',
            'dokumen.mimes' => 'Format berkas harus PDF, JPG, atau PNG.',
            'dokumen.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $file = $request->file('dokumen');
        $path = Storage::disk('private')->putFile('dokumen_medis/'.$pasien->id, $file);

        $pasien->dokumenPasien()->create([
            'jenis_dokumen' => $data['jenis_dokumen'],
            'nama_file_asli' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);

        return redirect()->route('pasien.show', $pasien)->with('success', 'Dokumen medis berhasil diunggah.');
    }

    public function preview(Pasien $pasien, DokumenPasien $dokumenPasien): BinaryFileResponse
    {
        $this->ensureBelongsToPatient($pasien, $dokumenPasien);

        $disk = Storage::disk('private');
        abort_unless($disk->exists($dokumenPasien->file_path), 404);

        $mimeType = $disk->mimeType($dokumenPasien->file_path) ?: 'application/octet-stream';
        abort_unless(in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png'], true), 404);
        $filename = basename(str_replace('\\', '/', $dokumenPasien->nama_file_asli));
        $filename = preg_replace('/[\r\n"]/', '_', $filename) ?: 'dokumen';

        return response()->file($disk->path($dokumenPasien->file_path), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function download(Pasien $pasien, DokumenPasien $dokumenPasien): BinaryFileResponse
    {
        $this->ensureBelongsToPatient($pasien, $dokumenPasien);

        $disk = Storage::disk('private');
        abort_unless($disk->exists($dokumenPasien->file_path), 404);

        return response()->download($disk->path($dokumenPasien->file_path), $dokumenPasien->nama_file_asli, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function destroy(Pasien $pasien, DokumenPasien $dokumenPasien): RedirectResponse
    {
        $this->ensureBelongsToPatient($pasien, $dokumenPasien);

        Storage::disk('private')->delete($dokumenPasien->file_path);
        $dokumenPasien->delete();

        return redirect()->route('pasien.show', $pasien)->with('success', 'Dokumen medis berhasil dihapus.');
    }

    private function ensureBelongsToPatient(Pasien $pasien, DokumenPasien $dokumenPasien): void
    {
        abort_unless($dokumenPasien->pasien_id === $pasien->id, 404);
    }
}
