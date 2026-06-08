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
        $sales = Sale::with('createdBy')->latest()->paginate(10);
        return view('sales.index', compact('sales'));
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
            ];
        })->values();

        return view('sales.create', compact('products', 'suppliers', 'products_json'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'                => 'required|date',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.laku'           => 'required|integer|min:1',
            'items.*.harga_jual'     => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $key => $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw ValidationException::withMessages([
                    "items.$key.product_id" => 'Produk tidak ditemukan.',
                ]);
            }
            if ($product->current_stock < $item['laku']) {
                throw ValidationException::withMessages([
                    "items.$key.laku" => "Stok {$product->nama_produk} tidak mencukupi (tersedia: {$product->current_stock}).",
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $sale = Sale::create([
                'tanggal'     => $validated['tanggal'],
                'total_bayar' => 0,
                'created_by'  => auth()->id(),
            ]);

            $totalBayar = 0;

            foreach ($validated['items'] as $item) {
                $hargaJual  = (int) $item['harga_jual'];
                $laku       = (int) $item['laku'];
                $subtotal   = $laku * $hargaJual;
                $totalBayar += $subtotal;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'laku'       => $laku,
                    'harga_jual' => $hargaJual,
                    'sub_total'  => $subtotal,
                ]);

                Product::where('id', $item['product_id'])
                    ->decrement('current_stock', $laku);
            }

            $sale->update(['total_bayar' => $totalBayar]);

            // [BUG #1 FIX] Kas masuk dicatat HANYA di sini saat penjualan terjadi.
            // PaymentController TIDAK boleh membuat kas masuk "keuntungan_penjualan" lagi.
            // [BUG #4 FIX] Simpan sale_id agar penghapusan kas bisa pakai relasi,
            // bukan string-matching yang rapuh.
            CashFlow::create([
                'tanggal'    => $validated['tanggal'],
                'tipe'       => 'masuk',
                'kategori'   => 'penjualan',
                'keterangan' => "Penjualan #{$sale->id}",
                'jumlah'     => $totalBayar,
                'sale_id'    => $sale->id,
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
