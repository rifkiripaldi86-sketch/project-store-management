<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'nama_toko', 'periode_awal', 'periode_akhir', 'total_bayar', 'total_pendapatan', 'keuntungan_toko', 'status', 'tanggal_bayar', 'created_by'];

    protected $dates = ['periode_awal', 'periode_akhir', 'tanggal_bayar'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(SupplierPaymentDetail::class);
    }
}
