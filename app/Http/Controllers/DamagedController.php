<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamagedController extends Controller
{
    public function index()
    {
        $damaged = DamagedItem::with('product', 'supplier', 'createdBy')->latest()->paginate(10);
        return view('damaged.index', compact('damaged'));
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('damaged.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan' => 'nullable|string',
        ]);

        // Cek stok cukup tidak (optional, tapi disarankan)
        $product = Product::find($request->product_id);
        $currentStock = $product->current_stock;
        if ($request->jumlah > $currentStock) {
            return back()->with('error', 'Jumlah rusak melebihi stok tersedia (' . $currentStock . ')');
        }

        DamagedItem::create([
            'tanggal' => $request->tanggal,
            'product_id' => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        // Update stok produk
        $product->updateStock();

        return redirect()->route('damaged.index')->with('success', 'Barang rusak dicatat');
    }

    public function destroy(DamagedItem $damaged)
    {
        DB::transaction(function () use ($damaged) {
            $product = $damaged->product;
            $damaged->delete();
            $product->updateStock();
        });

        return redirect()->route('damaged.index')->with('success', 'Data rusak dihapus');
    }
}