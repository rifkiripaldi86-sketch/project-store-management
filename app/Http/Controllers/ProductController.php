<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['supplier', 'category', 'unit'])
            ->when($request->search, function ($query, $search) {
                $query->where('nama_produk', 'like', "%{$search}%");
            })
            ->when($request->supplier_id, function ($query, $supplier_id) {
                $query->where('supplier_id', $supplier_id);
            })
            ->when($request->category_id, function ($query, $category_id) {
                $query->where('category_id', $category_id);
            })
            ->when($request->stock, function ($query, $stock) {
                if ($stock == 'low') {
                    $query->where('current_stock', '<=', 10);
                } elseif ($stock == 'medium') {
                    $query->whereBetween('current_stock', [11, 30]);
                } elseif ($stock == 'high') {
                    $query->where('current_stock', '>', 30);
                }
            })
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $units = Unit::all();

        return view('products.create', compact(
            'suppliers',
            'categories',
            'units'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'harga_jual'  => 'required|numeric',
            'harga_beli'  => 'required|numeric',
        ]);

        $existing = Product::withTrashed()
            ->where('nama_produk', $request->nama_produk)
            ->where('supplier_id', $request->supplier_id)
            ->first();

        if ($existing) {

            if ($existing->trashed()) {

                $existing->restore();

                return redirect()
                    ->route('products.index')
                    ->with(
                        'success',
                        'Produk ditemukan di sampah dan berhasil dipulihkan!'
                    );
            }

            return back()->with(
                'error',
                'Produk "' . $request->nama_produk . '" sudah terdaftar untuk supplier ini.'
            );
        }

        Product::create($request->all());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $units = Unit::all();

        return view('products.edit', compact(
            'product',
            'suppliers',
            'categories',
            'units'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'harga_jual'  => 'required|numeric',
            'harga_beli'  => 'required|numeric',
        ]);

        $product->update($request->all());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
?>
