<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Sudah benar di sini

class Product extends Model
{
    use HasFactory, SoftDeletes; // Gabungkan trait di sini

    protected $fillable = [
        'nama_produk',
        'thumbnail',
        'barcode',
        'supplier_id',
        'current_stock',
        'harga_jual',
        'harga_beli',
        'unit_id',
        'category_id',
        'image',
    ];

    // ─── Relasi ────────────────────────────────────────────────

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

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)->withTimestamps();
    }

    // ─── Business Logic ────────────────────────────────────────

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
