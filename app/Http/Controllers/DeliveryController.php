<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\DeliveryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk dropdown filter
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        // Query dasar dengan eager loading yang diperlukan
        $query = Delivery::with(['supplier', 'createdBy', 'items.product']);

        // Filter: search by supplier name
        if ($search = $request->get('search')) {
            $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%");
            });
        }

        // Filter: supplier_id
        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        // Filter: date range
        if ($dari = $request->get('dari')) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai = $request->get('sampai')) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        // Hitung total kiriman (berdasarkan filter)
        $totalKiriman = (clone $query)->count();

        // Hitung total produk (distinct product_id) dari semua delivery item yang ter-filter
        $totalProduk = (clone $query)
            ->join('delivery_items', 'deliveries.id', '=', 'delivery_items.delivery_id')
            ->distinct('delivery_items.product_id')
            ->count('delivery_items.product_id');

        // Hitung total nilai (sum jumlah_kirim * harga)
        $totalNilai = (clone $query)
            ->join('delivery_items', 'deliveries.id', '=', 'delivery_items.delivery_id')
            ->selectRaw('SUM(delivery_items.jumlah_kirim * delivery_items.harga) as total')
            ->value('total') ?? 0;

        // Paginasi hasil
        $deliveries = $query->latest('tanggal')->paginate(10);

        return view('deliveries.index', compact(
            'deliveries',
            'suppliers',
            'totalKiriman',
            'totalProduk',
            'totalNilai'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $products  = Product::orderBy('nama_produk')->get();

        return view('deliveries.create', compact('suppliers', 'products'));
    }

    /**
     * Mengambil daftar produk berdasarkan supplier.
     */
    public function getProductsBySupplier($supplier_id)
    {
        $products = Product::where('supplier_id', $supplier_id)
            ->orderBy('nama_produk')
            ->get();

        return response()->json($products);
    }

public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'tanggal'              => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.jumlah_kirim' => 'required|integer|min:1',
            'items.*.harga'        => 'required|numeric|min:0',
            'items.*.harga_jual'   => 'required|numeric|min:0', // Menambahkan validasi harga jual
        ]);

        DB::transaction(function () use ($validated) {
            $delivery = Delivery::create([
                'supplier_id' => $validated['supplier_id'],
                'tanggal'     => $validated['tanggal'],
                'created_by'  => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                // 1. Simpan item kiriman beserta harga beli dan harga jualnya
                DeliveryItem::create([
                    'delivery_id'  => $delivery->id,
                    'product_id'   => $item['product_id'],
                    'jumlah_kirim' => $item['jumlah_kirim'],
                    'harga'        => $item['harga'],
                    'harga_jual'   => $item['harga_jual'], // Ikut menyimpan harga jual ke riwayat kiriman
                ]);

                // 2. Update stok dan perbarui harga jual di tabel master produk
                Product::where('id', $item['product_id'])->update([
                    'harga_jual' => $item['harga_jual'] // Update harga jual produk terbaru
                ]);

                Product::where('id', $item['product_id'])
                    ->increment('current_stock', $item['jumlah_kirim']);
            }
        });

        return redirect()
            ->route('deliveries.index')
            ->with('success', 'Kiriman barang berhasil disimpan.');
    }
    public function show(Delivery $delivery)
    {
        $delivery->load('items.product', 'supplier');

        return view('deliveries.show', compact('delivery'));
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->load('items.product');

        // Cek apakah stok mencukupi untuk rollback
        foreach ($delivery->items as $item) {
            if ($item->product->current_stock < $item->jumlah_kirim) {
                return redirect()
                    ->route('deliveries.index')
                    ->with(
                        'error',
                        "Tidak bisa hapus: stok {$item->product->nama_produk} tidak mencukupi untuk dikurangi (tersedia: {$item->product->current_stock}, dibutuhkan: {$item->jumlah_kirim})."
                    );
            }
        }

        DB::transaction(function () use ($delivery) {
            foreach ($delivery->items as $item) {
                Product::where('id', $item->product_id)
                    ->decrement('current_stock', $item->jumlah_kirim);
            }

            $delivery->delete();
        });

        return redirect()
            ->route('deliveries.index')
            ->with('success', 'Data kiriman berhasil dihapus.');
    }
}
