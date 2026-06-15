<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            // Simpan nama toko saat nota dicetak agar cetak ulang tetap pakai nama yang sama
            $table->string('nama_toko', 100)->nullable()->after('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropColumn('nama_toko');
        });
    }
};
