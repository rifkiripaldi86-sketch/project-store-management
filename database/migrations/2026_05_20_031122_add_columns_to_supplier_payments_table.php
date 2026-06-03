<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->decimal('total_pendapatan', 12, 2)->default(0)->after('total_bayar');
            $table->decimal('keuntungan_toko', 12, 2)->default(0)->after('total_pendapatan');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropColumn(['total_pendapatan', 'keuntungan_toko']);
        });
    }
};
