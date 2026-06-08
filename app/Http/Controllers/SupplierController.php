<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $suppliers = Supplier::withCount('products')
            ->when($search, fn($q) =>
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
            )
            ->orderBy('nama_supplier')
            ->paginate(10)
            ->withQueryString(); // FIX: search parameter ikut ke link pagination

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $products = collect($request->input('products', []))
            ->map(fn($p) => trim($p))
            ->filter(fn($p) => $p !== '')
            ->values()
            ->toArray();

        $request->merge(['products' => $products]);

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
            'telepon'       => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'products'      => 'nullable|array',
            'products.*'    => 'string|max:255|distinct',
        ], [
            'products.*.string'   => 'Nama produk harus berupa teks.',
            'products.*.max'      => 'Nama produk maksimal 255 karakter.',
            'products.*.distinct' => 'Ada nama produk yang duplikat.',
        ]);

        DB::transaction(function () use ($validated) {
            $supplier = Supplier::create([
                'nama_supplier' => $validated['nama_supplier'],
                'telepon'       => $validated['telepon'] ?? null,
                'alamat'        => $validated['alamat'] ?? null,
            ]);

            $productIds = [];

            foreach ($validated['products'] ?? [] as $namaProduk) {
                $product = Product::firstOrCreate(
                    ['nama_produk' => $namaProduk, 'supplier_id' => $supplier->id],
                    ['current_stock' => 0]
                );
                $productIds[] = $product->id;
            }

            if (!empty($productIds)) {
                $supplier->products()->syncWithoutDetaching($productIds);
            }
        });

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('products.unit', 'products.category');

        // FIX: hanya produk yang belum terdaftar di supplier ini
        $attachedIds       = $supplier->products->pluck('id');
        $availableProducts = Product::whereNotIn('id', $attachedIds)
            ->orderBy('nama_produk')
            ->get();

        return view('suppliers.show', compact('supplier', 'availableProducts'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => "required|string|max:255|unique:suppliers,nama_supplier,{$supplier->id}",
            'telepon'       => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diupdate.');
    }

    public function destroy(Supplier $supplier)
    {
        // FIX: cek kiriman, pembayaran, DAN produk aktif
        if ($supplier->deliveries()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki data kiriman.');
        }

        if ($supplier->supplierPayments()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki data pembayaran.');
        }

        if ($supplier->products()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki produk terdaftar. Hapus atau pindahkan produk terlebih dahulu.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function attachProduct(Request $request, Supplier $supplier)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $product = Product::findOrFail($request->product_id);

        if (is_null($product->supplier_id)) {
            $product->update(['supplier_id' => $supplier->id]);
        }

        if (!$supplier->products()->where('product_id', $request->product_id)->exists()) {
            $supplier->products()->attach($request->product_id);
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Produk berhasil ditambahkan ke supplier.');
        }

        return redirect()->route('suppliers.show', $supplier)
            ->with('info', 'Produk sudah terdaftar untuk supplier ini.');
    }

    public function detachProduct(Supplier $supplier, Product $product)
    {
        $supplier->products()->detach($product->id);

        if ($product->supplier_id === $supplier->id) {
            $product->update(['supplier_id' => null]);
        }

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Produk berhasil dihapus dari supplier.');
    }
}
