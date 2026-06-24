<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'tipe',
        'kategori',
        'keterangan',
        'jumlah',
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}