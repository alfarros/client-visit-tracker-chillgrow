<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_bulanans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->date('periode_tanggal');
            $table->text('sensori')->nullable();
            $table->text('motorik_kasar')->nullable();
            $table->text('motorik_halus')->nullable();
            $table->text('kognitif_perseptual')->nullable();
            $table->text('kemandirian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_bulanans');
    }
};
