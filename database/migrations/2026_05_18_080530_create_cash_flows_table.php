<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->string('kategori'); // penjualan, bayar_supplier, operasional, lainnya
            $table->string('keterangan')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('tanggal');
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};