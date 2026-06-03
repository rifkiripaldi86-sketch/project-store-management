<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('products')
            ->orderBy('nama_supplier')
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store supplier baru sekaligus membuat & attach produk yang disuplai.
     */
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
                /*
                 * firstOrCreate SCOPE PER SUPPLIER:
                 * "Tepung Terigu" dari Supplier A → record baru
                 * "Tepung Terigu" dari Supplier B → record baru yang berbeda
                 * "Tepung Terigu" dari Supplier A lagi → pakai yang sudah ada
                 */
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
        // Produk milik supplier ini (by supplier_id — sumber kebenaran utama)
        $supplierProducts = Product::where('supplier_id', $supplier->id)
            ->orderBy('nama_produk')
            ->get();

        // Produk yang bisa di-assign (belum punya supplier)
        $availableProducts = Product::whereNull('supplier_id')
            ->orderBy('nama_produk')
            ->get();

        return view('suppliers.show', compact('supplier', 'supplierProducts', 'availableProducts'));
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
        if ($supplier->deliveries()->exists() || $supplier->supplierPayments()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki data kiriman atau pembayaran.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function attachProduct(Request $request, Supplier $supplier)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $product = Product::findOrFail($request->product_id);

        // Catat supplier_id jika produk belum punya supplier
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

        // Lepas supplier_id jika produk ini memang milik supplier yang di-detach
        if ($product->supplier_id === $supplier->id) {
            $product->update(['supplier_id' => null]);
        }

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Produk berhasil dihapus dari supplier.');
    }
}