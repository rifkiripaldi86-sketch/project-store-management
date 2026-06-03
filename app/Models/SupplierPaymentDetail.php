<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_payment_id', 'delivery_id', 'amount'];

    public function payment()
    {
        return $this->belongsTo(SupplierPayment::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}