<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
public function index()
{
    // Ambil data penjualan dengan pagination
    $sales = Sale::with('createdBy')->latest()->paginate(10);

    // Hitung total pendapatan dari SEMUA transaksi
    $totalPendapatan = Sale::sum('total_bayar');

    return view('sales.index', compact('sales', 'totalPendapatan'));
}

    public function create(\Illuminate\Http\Request $request)
    {
        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $supplierId = $request->query('supplier_id');

        if ($supplierId) {
            $products = Product::where('supplier_id', $supplierId)->orderBy('nama_produk')->get();
        } else {
            $products = Product::orderBy('nama_produk')->get();
        }

        $products_json = $products->map(function ($p) {
    return [
        'id'          => $p->id,
        'nama_produk' => $p->nama_produk,
        'stok'        => $p->current_stock,
        'supplier_id' => $p->supplier_id,
        'harga_jual'  => $p->harga_jual,
    ];
})->values();

        return view('sales.create', compact('products', 'suppliers', 'products_json'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'tanggal' => 'required|date',
        'items'   => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.laku'       => 'required|integer|min:1',
        'items.*.harga_jual' => 'required|numeric|min:0', // Pastikan input ini dikirim dari form
    ]);

    DB::transaction(function () use ($validated) {
        // 1. Buat data penjualan awal
        $sale = Sale::create([
            'tanggal' => $validated['tanggal'],
            'total_bayar' => 0,
            'created_by' => auth()->id()
        ]);

        $totalBayar = 0; // Inisialisasi awal

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $laku = (int) $item['laku'];
            $harga = (float) $item['harga_jual']; // Ambil harga yang diketik kasir
            $subTotal = $laku * $harga;

            // 2. Simpan item penjualan
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $product->id,
                'laku'       => $laku,
                'harga_jual' => $harga,
                'sub_total'  => $subTotal,
            ]);

            // 3. Update total bayar secara bertahap
            $totalBayar += $subTotal;

            // 4. Kurangi stok
            $product->decrement('current_stock', $laku);
        }

        // 5. Update total_bayar yang asli ke tabel sales
        $sale->update(['total_bayar' => $totalBayar]);

        // 6. Catat arus kas
        CashFlow::create([
            'tanggal'    => $validated['tanggal'],
            'tipe'       => 'masuk',
            'kategori'   => 'penjualan',
            'keterangan' => "Penjualan #{$sale->id}",
            'jumlah'     => $totalBayar,
            'sale_id'    => $sale->id,
            'created_by' => auth()->id()
        ]);
    });

    return redirect()->route('sales.index')->with('success', 'Penjualan tersimpan!');
}
public function show(Sale $sale)
{
    // Pastikan load relasi itemnya
    $sale->load('items.product');

    // Kirim ke view, kalau butuh $details, namakan $details
    $details = $sale->items;

    return view('sales.show', compact('sale', 'details'));
}
    public function destroy(Sale $sale)
    {
        $sale->load('items');

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('current_stock', $item->laku);
            }

            // [BUG #4 FIX] Hapus kas lewat sale_id (relasi langsung), bukan string-matching.
            // Fallback ke string-matching untuk data lama yang belum punya sale_id.
            $deleted = CashFlow::where('sale_id', $sale->id)->delete();
            if (!$deleted) {
                CashFlow::where('kategori', 'penjualan')
                    ->where('keterangan', "Penjualan #{$sale->id}")
                    ->delete();
            }

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus dan stok dikembalikan.');
    }
}
