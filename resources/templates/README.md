# Template Word (DOCX)

Folder ini menyimpan file template `.docx` yang dipakai untuk fitur Export Word.
Dokumen **dibuat dari file ini** — jadi Anda bisa membuka dan mengedit tampilannya
langsung di Microsoft Word (font, warna, tabel, logo, tanda tangan, dll.), lalu simpan.

## Cara edit

1. Buka file template (mis. `cppt.docx`) di Microsoft Word.
2. Atur tampilan sesuka hati (font, ukuran, warna, border tabel, margin, dll.).
3. **Jangan hapus atau ubah tulisan placeholder** berformat `${nama_placeholder}`
   (mis. `${nama_pasien}`). Isinya akan diganti otomatis oleh aplikasi.
4. Simpan dalam format yang sama (`.docx`, Word Document).
5. Timpa file lama dengan hasil simpanan Anda.

Kotak placeholder cukup ditulis sebagai teks biasa. Anda boleh memindahkannya ke
posisi mana pun, memberi bold/italic/ukuran berapa pun — yang penting teks
`${...}` tetap utuh dalam satu paragraf (jangan ada spasi di dalam kurung).

Nilai yang panjang (mis. SOAP) dapat berisi banyak baris; aplikasi mengubah baris
baru menjadi `<w:br/>` sehingga tetap rapi di dalam sel/paragraf.

## Daftar placeholder

### Umum (dipakai di semua template)

| Placeholder | Isi |
| --- | --- |
| `${nama_pasien}` | Nama lengkap pasien |
| `${no_rekam_medis}` | Nomor rekam medis |
| `${tanggal_lahir}` | Tanggal lahir (mis. `20 Mei 2018`) |
| `${usia}` | Usia (mis. `8 tahun`) |
| `${tanggal_kunjungan}` | Tanggal kunjungan |
| `${waktu_terapi}` | Rentang jam (mis. `09:00–10:00`) |
| `${diagnosa_awal}` | Diagnosa awal pasien |

### `cppt.docx` — Catatan Perkembangan Pasien Terintegrasi

| Placeholder | Isi |
| --- | --- |
| `${tanggal_catatan}` | Tanggal catatan CPPT |
| `${penanggung_jawab}` | Nama penanggung jawab/terapis |
| `${subjective}` | Subjective |
| `${objective}` | Objective |
| `${assessment}` | Assessment |
| `${planning}` | Planning |

### `program-terapi.docx` — Lembar Program Terapi

| Placeholder | Isi |
| --- | --- |
| `${long_term_goals}` | Tujuan Jangka Panjang |
| `${short_term_goals}` | Tujuan Jangka Pendek |
| `${aktivitas_hari_ini}` | Aktivitas Hari Ini |
| `${respon_anak}` | Respon Anak |

## Catatan teknis

- Nama file template diatur di `CpptController::exportWord` (`cppt.docx`) dan
  `ProgramTerapisController::exportWord` (`program-terapi.docx`).
- Logika pengisian ada di trait
  `app/Http/Controllers/Concerns/ExportsMedicalRecordWord.php`.
- Jika ingin menambah placeholder baru, tambahkan `${nama_baru}` di template dan
  pasang value-nya pada `array_merge(...)` di controller terkait.
- Menambah/menghapus baris tabel tidak masalah selama placeholder tetap ada.
