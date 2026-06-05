<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nama_produk', 'supplier_id', 'current_stock', 'harga_jual',];

    // ─── Relasi ────────────────────────────────────────────────

    /**
     * Setiap produk dimiliki oleh satu supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function damagedItems()
    {
        return $this->hasMany(DamagedItem::class);
    }

    /**
     * Relasi many-to-many tetap dipertahankan jika digunakan di tempat lain.
     * Namun dengan adanya supplier_id, relasi primer sudah lewat belongsTo.
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)->withTimestamps();
    }

    // ─── Business Logic ────────────────────────────────────────

    /**
     * Nama tampilan lengkap: "Tepung Terigu (PT Sumber Rezeki)"
     * Berguna untuk dropdown atau label di UI.
     */
    public function getDisplayNameAttribute(): string
    {
        $supplierLabel = $this->supplier?->nama_supplier ?? 'Tanpa Supplier';
        return "{$this->nama_produk} ({$supplierLabel})";
    }

    public function updateStock(): void
    {
        $totalIn      = $this->deliveryItems()->sum('jumlah_kirim');
        $totalOut     = $this->saleItems()->sum('laku');
        $totalDamaged = $this->damagedItems()->sum('jumlah');

        $this->current_stock = $totalIn - $totalOut - $totalDamaged;
        $this->saveQuietly();
    }
}
