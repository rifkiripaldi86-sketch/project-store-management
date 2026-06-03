<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    // relasi
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class, 'created_by');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'created_by');
    }
}