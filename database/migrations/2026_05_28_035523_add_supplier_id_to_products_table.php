<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // 1. Drop index lama pada nama_produk (jika ada)
            $indexes = collect(DB::select("SHOW INDEX FROM products WHERE Column_name = 'nama_produk'"))
                ->pluck('Key_name')
                ->filter(fn($name) => $name !== 'PRIMARY')
                ->unique()
                ->values();

            foreach ($indexes as $indexName) {
                $table->dropIndex($indexName);
            }

            // 2. Tambah kolom supplier_id hanya jika belum ada
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')
                      ->nullable()
                      ->after('nama_produk')
                      ->constrained('suppliers')
                      ->nullOnDelete();
            }

            // 3. Tambah unique (nama_produk, supplier_id) hanya jika belum ada
            $compositeExists = collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_nama_produk_supplier_id_unique'"))
                ->isNotEmpty();

            if (!$compositeExists) {
                $table->unique(['nama_produk', 'supplier_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'supplier_id')) {
                $compositeExists = collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_nama_produk_supplier_id_unique'"))
                    ->isNotEmpty();

                if ($compositeExists) {
                    $table->dropUnique(['nama_produk', 'supplier_id']);
                }

                $table->dropConstrainedForeignId('supplier_id');
            }

            // Kembalikan unique pada nama_produk saja
            $namaUnique = collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_nama_produk_unique'"))
                ->isNotEmpty();

            if (!$namaUnique) {
                $table->unique('nama_produk');
            }
        });
    }
};