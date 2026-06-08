<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            // [BUG #4 FIX] Tambah relasi langsung ke tabel sales agar penghapusan
            // kas saat void penjualan tidak bergantung pada string-matching keterangan.
            // nullable karena entri kas manual dan bayar_supplier tidak punya sale_id.
            $table->foreignId('sale_id')
                  ->nullable()
                  ->after('jumlah')
                  ->constrained('sales')
                  ->nullOnDelete();

            $table->index('sale_id');
        });
    }

    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropIndex(['sale_id']);
            $table->dropColumn('sale_id');
        });
    }
};
