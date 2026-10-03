<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->time('jam_kunjungan')->nullable()->after('tanggal_kunjungan');
            $table->enum('status', ['Antre', 'Berlangsung', 'Selesai', 'Batal'])->default('Antre')->change();
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->enum('status', ['Antre', 'Selesai', 'Batal'])->default('Antre')->change();
            $table->dropColumn('jam_kunjungan');
        });
    }
};
