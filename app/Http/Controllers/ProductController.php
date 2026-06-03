<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('supplier');

        // Filter klik nama supplier
        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        // Filter Search nama produk
        if ($search = $request->get('search')) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        // Filter jenis kondisi stok
        if ($stock = $request->get('stock')) {
            if ($stock == 'low') $query->where('current_stock', '<=', 10);
            elseif ($stock == 'medium') $query->where('current_stock', '>', 10)->where('current_stock', '<=', 30);
            elseif ($stock == 'high') $query->where('current_stock', '>', 30);
        }

        $products = $query->orderBy('supplier_id')
            ->orderBy('nama_produk')
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('products.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'   => 'required|string|max:255',
            'supplier_id'   => 'required|exists:suppliers,id',
            'current_stock' => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('products.edit', compact('product', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk'   => 'required|string|max:255',
            'supplier_id'   => 'required|exists:suppliers,id',
            'current_stock' => 'required|integer|min:0', // Validasi stok manual
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
