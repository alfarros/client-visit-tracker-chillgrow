<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->time('jam_selesai')->nullable()->after('jam_kunjungan');
            $table->enum('status', ['Antre', 'Berlangsung', 'Menunggu Diselesaikan', 'Selesai', 'Batal'])->default('Antre')->change();
        });
    }

    public function down(): void
    {
        DB::table('kunjungan_kliens')
            ->where('status', 'Menunggu Diselesaikan')
            ->update(['status' => 'Berlangsung']);

        Schema::table('kunjungan_kliens', function (Blueprint $table): void {
            $table->enum('status', ['Antre', 'Berlangsung', 'Selesai', 'Batal'])->default('Antre')->change();
            $table->dropColumn('jam_selesai');
        });
    }
};
