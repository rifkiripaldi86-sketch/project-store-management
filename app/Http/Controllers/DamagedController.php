<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamagedController extends Controller
{
    public function index(Request $request)
    {
        $query = DamagedItem::with([
            'product',
            'supplier',
            'createdBy'
        ]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {

                $q->whereHas('product', function ($qq) use ($request) {
                    $qq->where('nama_produk', 'like', '%' . $request->search . '%');
                });

                $q->orWhereHas('supplier', function ($qq) use ($request) {
                    $qq->where('nama_supplier', 'like', '%' . $request->search . '%');
                });

            });
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        $items = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $damaged = $items->groupBy(function ($item) {
            return $item->tanggal . '_' . $item->supplier_id;
        });

        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('damaged.index', compact(
            'damaged',
            'suppliers'
        ));
    }

    public function create()
    {
        $products = Product::orderBy('nama_produk')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('damaged.create', compact(
            'products',
            'suppliers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',

            'items' => 'required|array|min:1',

            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->items as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($item['jumlah'] > $product->current_stock) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => [
                            "Stok {$product->nama_produk} hanya tersedia {$product->current_stock} pcs."
                        ]
                    ]);
                }

                DamagedItem::create([
                    'tanggal' => $request->tanggal,
                    'supplier_id' => $request->supplier_id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                /**
                 * Jika project Anda memakai updateStock()
                 * jangan decrement manual.
                 */

                $product->updateStock();

                /**
                 * Jika TIDAK memakai updateStock(),
                 * ganti dua baris di atas menjadi:
                 *
                 * $product->decrement('current_stock', $item['jumlah']);
                 */
            }

        });

        return redirect()
            ->route('damaged.index')
            ->with('success', 'Barang rusak berhasil disimpan.');
    }

    public function destroy(DamagedItem $damaged)
    {
        DB::transaction(function () use ($damaged) {

            $product = $damaged->product;

            $damaged->delete();

            $product->updateStock();

        });

        return redirect()
            ->route('damaged.index')
            ->with('success', 'Data barang rusak berhasil dihapus.');
    }

    public function destroyGroup(Request $request)
    {
        DB::transaction(function () use ($request) {

            $items = DamagedItem::whereDate('tanggal', $request->tanggal)
                ->where('supplier_id', $request->supplier_id)
                ->get();

            foreach ($items as $item) {

                $product = $item->product;

                $item->delete();

                $product->updateStock();

            }

        });

        return redirect()
            ->route('damaged.index')
            ->with('success', 'Semua barang rusak berhasil dihapus.');
    }
}