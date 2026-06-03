<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@tokosarirezeki.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Operator
        User::create([
            'name' => 'Operator Toko',
            'email' => 'operator@tokosarirezeki.com',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
        ]);

        // Contoh supplier
        Supplier::create(['nama_supplier' => 'RIN', 'telepon' => '08123456789', 'alamat' => 'Jl. Cimahi']);
        Supplier::create(['nama_supplier' => 'Sumber Makmur', 'telepon' => '08234567890', 'alamat' => 'Jl. Bandung']);

        // Contoh produk
        Product::create(['nama_produk' => 'Pie Buah', 'current_stock' => 0]);
        Product::create(['nama_produk' => 'Bolu', 'current_stock' => 0]);
        Product::create(['nama_produk' => 'Brownies', 'current_stock' => 0]);
        Product::create(['nama_produk' => 'Donat', 'current_stock' => 0]);
    }
}