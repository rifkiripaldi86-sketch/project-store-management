<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_items', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained(); // opsional: dari supplier mana
            $table->integer('jumlah');
            $table->string('keterangan')->nullable(); // misal: expired, pecah, dll
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('tanggal');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_items');
    }
};