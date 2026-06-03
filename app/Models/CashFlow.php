<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = ['tanggal', 'tipe', 'kategori', 'keterangan', 'jumlah', 'created_by'];

    protected $dates = ['tanggal'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}