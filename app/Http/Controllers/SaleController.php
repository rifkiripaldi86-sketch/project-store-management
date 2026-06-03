<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('createdBy')->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    /**
     * Menampilkan form tambah penjualan.
     */
    public function create()
    {
        // Ambil produk, lalu kirim ke view sebagai $products
        $products = Product::orderBy('nama_produk')->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Simpan penjualan baru.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'tanggal'                => 'required|date',
        'items'                  => 'required|array|min:1',
        'items.*.product_id'     => 'required|exists:products,id',
        'items.*.laku'           => 'required|integer|min:1',
        'items.*.harga_jual'     => 'required|integer|min:0', // diubah dari numeric ke integer
    ]);

    // Validasi stok untuk setiap item
    foreach ($validated['items'] as $key => $item) {
        $product = Product::find($item['product_id']);
        if (!$product) {
            throw ValidationException::withMessages([
                "items.$key.product_id" => "Produk tidak ditemukan."
            ]);
        }
        if ($product->current_stock < $item['laku']) {
            throw ValidationException::withMessages([
                "items.$key.laku" => "Stok {$product->nama_produk} tidak mencukupi (tersedia: {$product->current_stock})."
            ]);
        }
    }

    DB::transaction(function () use ($validated) {
        $sale = Sale::create([
            'tanggal'    => $validated['tanggal'],
            'total_bayar' => 0,
            'created_by' => auth()->id(),
        ]);

        $totalBayar = 0;

        foreach ($validated['items'] as $item) {
            // Cast ke integer untuk memastikan tidak ada floating point error
            $hargaJual = (int) $item['harga_jual'];
            $laku      = (int) $item['laku'];
            $subtotal  = $laku * $hargaJual;
            $totalBayar += $subtotal;

            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $item['product_id'],
                'laku'       => $laku,
                'harga_jual' => $hargaJual,     // simpan integer
                'sub_total'  => $subtotal,
            ]);

            // Kurangi stok
            Product::where('id', $item['product_id'])
                ->decrement('current_stock', $laku);
        }

        $sale->update(['total_bayar' => $totalBayar]);

        CashFlow::create([
            'tanggal'    => $validated['tanggal'],
            'tipe'       => 'masuk',
            'kategori'   => 'penjualan',
            'keterangan' => "Penjualan #{$sale->id}",
            'jumlah'     => $totalBayar,
            'created_by' => auth()->id(),
        ]);
    });

    return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dicatat.');
}

    public function show(Sale $sale)
    {
        $sale->load('items.product');
        return view('sales.show', compact('sale'));
    }

    /**
     * Hapus/void penjualan — kembalikan stok & hapus kas terkait.
     */
    public function destroy(Sale $sale)
    {
        $sale->load('items');

        DB::transaction(function () use ($sale) {
            // Kembalikan stok untuk setiap item
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('current_stock', $item->laku);
            }

            // Hapus CashFlow terkait penjualan ini
            CashFlow::where('kategori', 'penjualan')
                ->where('keterangan', "Penjualan #{$sale->id}")
                ->delete();

            // Hapus sale items dan sale
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus dan stok dikembalikan.');
    }
}