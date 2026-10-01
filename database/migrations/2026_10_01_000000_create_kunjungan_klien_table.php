<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_klien', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_pasien', 150);
            $table->string('no_rekam_medis', 30)->index();
            $table->date('tanggal_kunjungan')->index();
            $table->string('cara_bayar', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_klien');
    }
};
