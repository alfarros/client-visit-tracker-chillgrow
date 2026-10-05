<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi_bulanans', function (Blueprint $table): void {
            $table->text('refleks_primitif')->nullable()->after('periode_tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_bulanans', function (Blueprint $table): void {
            $table->dropColumn('refleks_primitif');
        });
    }
};
