<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaJualToProductsTable extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'harga_beli')) {
                $table->integer('harga_beli')->default(0)->after('nama_produk');
            }
            if (!Schema::hasColumn('products', 'harga_jual')) {
                $table->integer('harga_jual')->default(0)->after('harga_beli');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['harga_beli', 'harga_jual']);
        });
    }
}
