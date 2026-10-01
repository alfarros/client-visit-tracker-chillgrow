<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing visits are demo data and intentionally discarded during this normalization.
        DB::table('kunjungan_klien')->delete();
        Schema::rename('kunjungan_klien', 'kunjungan_kliens');

        Schema::create('pasiens', function (Blueprint $table): void {
            $table->id();
            $table->string('no_rekam_medis', 30)->unique();
            $table->string('nama_lengkap', 150);
            $table->date('tanggal_lahir');
            $table->string('diagnosa_awal')->nullable();
            $table->timestamps();
        });

        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->dropColumn(['nama_pasien', 'no_rekam_medis']);
            $table->foreignId('pasien_id')->after('id')->constrained('pasiens')->restrictOnDelete();
            $table->enum('cara_bayar', ['BPJS', 'UMUM', 'ASURANSI'])->change();
            $table->enum('status', ['Antre', 'Selesai', 'Batal'])->default('Antre')->after('cara_bayar');
        });

        Schema::create('cppts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kunjungan_klien_id')->constrained('kunjungan_kliens')->restrictOnDelete();
            $table->date('tanggal');
            $table->string('penanggung_jawab', 150);
            $table->text('subjective');
            $table->text('objective');
            $table->text('assessment');
            $table->text('planning');
            $table->timestamps();
        });

        Schema::create('program_terapis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kunjungan_klien_id')->constrained('kunjungan_kliens')->restrictOnDelete();
            $table->text('long_term_goals');
            $table->text('short_term_goals');
            $table->text('aktivitas_hari_ini');
            $table->text('respon_anak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_terapis');
        Schema::dropIfExists('cppts');

        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->string('nama_pasien', 150)->nullable();
            $table->string('no_rekam_medis', 30)->nullable()->index();
        });

        DB::table('kunjungan_kliens')
            ->join('pasiens', 'pasiens.id', '=', 'kunjungan_kliens.pasien_id')
            ->update([
                'kunjungan_kliens.nama_pasien' => DB::raw('pasiens.nama_lengkap'),
                'kunjungan_kliens.no_rekam_medis' => DB::raw('pasiens.no_rekam_medis'),
            ]);

        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->dropForeign(['pasien_id']);
            $table->dropColumn(['pasien_id', 'status']);
            $table->string('cara_bayar', 20)->change();
        });

        Schema::dropIfExists('pasiens');
        Schema::rename('kunjungan_kliens', 'kunjungan_klien');
    }
};
