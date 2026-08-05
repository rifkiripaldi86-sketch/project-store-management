<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'nama_produk'   => 'required|string|max:255',
            'barcode' => 'required|unique:products,barcode,NULL,id,deleted_at,NULL',
            'supplier_id'   => 'required|exists:suppliers,id',
            'category_id'   => 'required|exists:categories,id',
            'unit_id'       => 'required|exists:units,id',
            'harga_jual'    => 'required|numeric|min:0',
            'harga_beli'    => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $existing = Product::withTrashed()
        ->where('nama_produk', $request->nama_produk)
        ->where('supplier_id', $request->supplier_id)
        ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->update([
                    'category_id'   => $request->category_id,
                    'unit_id'       => $request->unit_id,
                    'harga_beli'    => (int) $request->harga_beli,
                    'harga_jual'    => (int) $request->harga_jual,
                    'current_stock' => (int) $request->current_stock,
            ]);

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

            if ($request->hasFile('thumbnail')) {

    $file = $request->file('thumbnail');

    $filename = time().'_'.$file->getClientOriginalName();

    $file->storeAs(
        'products',
        $filename,
        'public'
    );

    $data['thumbnail'] = $filename;
}
}


DB::transaction(function () use ($request) {

    $product = Product::create([
        'nama_produk'   => $request->nama_produk,
        'barcode' => $request->barcode,
        'image' => $request->file('image') ? $request->file('image')->store('products', 'public') : null,
        'supplier_id'   => $request->supplier_id,
        'category_id'   => $request->category_id,
        'unit_id'       => $request->unit_id,
        'harga_beli'    => (int) $request->harga_beli,
        'harga_jual'    => (int) $request->harga_jual,
        'current_stock' => (int) $request->current_stock,
    ]);

    if ((int) $request->current_stock > 0) {

        $delivery = Delivery::create([
            'supplier_id' => $request->supplier_id,
            'tanggal'     => now()->toDateString(),
            'created_by'  => auth()->id(),
        ]);

        DeliveryItem::create([
            'delivery_id'  => $delivery->id,
            'product_id'   => $product->id,
            'jumlah_kirim' => (int) $request->current_stock,
            'harga'        => (int) $request->harga_beli,
        ]);
    }
});

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
'nama_produk'   => 'required|string|max:255',
'barcode' => 'required|string|unique:products,barcode,' . $product->id . ',id,deleted_at,NULL',
'supplier_id'   => 'required|exists:suppliers,id',
'category_id'   => 'required|exists:categories,id',
'unit_id'       => 'required|exists:units,id',
'harga_jual'    => 'required|numeric|min:0',
'harga_beli'    => 'required|numeric|min:0',
'current_stock' => 'required|integer|min:0',
'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
]);

DB::transaction(function () use ($request, $product) {

    $stokLama = (int) $product->current_stock;
    $stokBaru = (int) $request->current_stock;

    $selisih = $stokBaru - $stokLama;

    $product->update([
        'nama_produk'   => $request->nama_produk,
        'barcode' => 'required|string|unique:products,barcode,' . $product->id . ',id,deleted_at,NULL',
        'image' => $request->file('image') ? $request->file('image')->store('products', 'public') : $product->image,
        'supplier_id'   => $request->supplier_id,
        'category_id'   => $request->category_id,
        'unit_id'       => $request->unit_id,
        'harga_beli'    => (int) $request->harga_beli,
        'harga_jual'    => (int) $request->harga_jual,
        'current_stock' => $stokBaru,
    ]);

    // Jika stok ditambah, buat kiriman otomatis
    if ($selisih > 0) {

        $delivery = Delivery::create([
            'supplier_id' => $request->supplier_id,
            'tanggal'     => now()->toDateString(),
            'created_by'  => auth()->id(),
        ]);

        DeliveryItem::create([
            'delivery_id'  => $delivery->id,
            'product_id'   => $product->id,
            'jumlah_kirim' => $selisih,
            'harga'        => (int) $request->harga_beli,
        ]);
    }
});

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
