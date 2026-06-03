<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Delivery;
use App\Models\SupplierPayment;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('payments.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $deliveries = Delivery::where('supplier_id', $validated['supplier_id'])
            ->whereBetween('tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->with('items')
            ->get();

        if ($deliveries->isEmpty()) {
            return back()->with('error', 'Tidak ada kiriman pada periode tersebut.');
        }

        $productIds = $deliveries
            ->flatMap(fn($d) => $d->items->pluck('product_id'))
            ->unique()
            ->values();

        // Ambil harga beli per produk dari delivery (ambil yang terbaru jika ada perubahan harga)
        $hargaBeliMap = DB::table('delivery_items')
            ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
            ->where('deliveries.supplier_id', $validated['supplier_id'])
            ->whereBetween('deliveries.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->whereIn('delivery_items.product_id', $productIds)
            ->select('delivery_items.product_id', 'delivery_items.harga')
            ->orderByDesc('deliveries.tanggal')
            ->get()
            ->keyBy('product_id')
            ->map(fn($row) => $row->harga);

        // Ambil data penjualan per produk pada periode ini
        $saleItems = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.product_id', $productIds)
            ->whereBetween('sales.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->select(
                'sale_items.product_id',
                DB::raw('SUM(sale_items.laku) as total_laku'),
                DB::raw('SUM(sale_items.laku * sale_items.harga_jual) as total_pendapatan')
            )
            ->groupBy('sale_items.product_id')
            ->get();

        $totalBayarSupplier = 0;
        $totalPendapatan    = 0;

        foreach ($saleItems as $item) {
            $hargaBeli           = $hargaBeliMap->get($item->product_id, 0);
            $totalBayarSupplier += $item->total_laku * $hargaBeli;
            $totalPendapatan    += $item->total_pendapatan;
        }

        $keuntunganToko = $totalPendapatan - $totalBayarSupplier;

        $payment = DB::transaction(function () use ($validated, $totalBayarSupplier, $totalPendapatan, $keuntunganToko) {
            $payment = SupplierPayment::create([
                'supplier_id'      => $validated['supplier_id'],
                'periode_awal'     => $validated['periode_awal'],
                'periode_akhir'    => $validated['periode_akhir'],
                'total_pendapatan' => $totalPendapatan,
                'total_bayar'      => $totalBayarSupplier,
                'keuntungan_toko'  => $keuntunganToko,
                'status'           => 'sudah_dibayar',
                'tanggal_bayar'    => now(),
                'created_by'       => auth()->id(),
            ]);

            $payment->load('supplier');

            // Kas keluar: bayar supplier
            CashFlow::create([
                'tanggal'    => now(),
                'tipe'       => 'keluar',
                'kategori'   => 'bayar_supplier',
                'keterangan' => "Pembayaran ke supplier {$payment->supplier->nama_supplier} "
                              . "periode {$validated['periode_awal']} s/d {$validated['periode_akhir']}",
                'jumlah'     => $totalBayarSupplier,
                'created_by' => auth()->id(),
            ]);

            // Kas masuk: keuntungan toko dari selisih harga jual - harga beli
            if ($keuntunganToko > 0) {
                CashFlow::create([
                    'tanggal'    => now(),
                    'tipe'       => 'masuk',
                    'kategori'   => 'keuntungan_penjualan',
                    'keterangan' => "Keuntungan dari supplier {$payment->supplier->nama_supplier} "
                                  . "periode {$validated['periode_awal']} s/d {$validated['periode_akhir']}",
                    'jumlah'     => $keuntunganToko,
                    'created_by' => auth()->id(),
                ]);
            }

            return $payment;
        });

        return redirect()->route('payments.print', $payment->id)
            ->with('success', 'Pembayaran supplier berhasil dicatat.');
    }

    public function printNota($id)
    {
        $payment = SupplierPayment::with('supplier')->findOrFail($id);

        // Data kiriman per tanggal per produk (termasuk harga beli)
        $deliveryData = DB::table('delivery_items')
            ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
            ->join('products', 'delivery_items.product_id', '=', 'products.id')
            ->where('deliveries.supplier_id', $payment->supplier_id)
            ->whereBetween('deliveries.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select(
                'deliveries.tanggal',
                'products.id as product_id',
                'products.nama_produk',
                DB::raw('SUM(delivery_items.jumlah_kirim) as kirim'),
                'delivery_items.harga as harga_beli'
            )
            ->groupBy('deliveries.tanggal', 'products.id', 'products.nama_produk', 'delivery_items.harga')
            ->get()
            ->keyBy(fn($row) => $row->tanggal . '_' . $row->product_id);

        $productIds = $deliveryData->pluck('product_id')->unique();

        $saleData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereIn('sale_items.product_id', $productIds)
            ->whereBetween('sales.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select(
                'sales.tanggal',
                'sale_items.product_id',
                'products.nama_produk',
                DB::raw('SUM(sale_items.laku) as laku'),
                'sale_items.harga_jual as harga_jual'
            )
            ->groupBy('sales.tanggal', 'sale_items.product_id', 'products.nama_produk', 'sale_items.harga_jual')
            ->orderBy('sales.tanggal')
            ->get();

        $details = $saleData->map(function ($sale) use ($deliveryData) {
            $key       = $sale->tanggal . '_' . $sale->product_id;
            $delivery  = $deliveryData->get($key);
            $kirim     = $delivery?->kirim      ?? 0;
            $hargaBeli = $delivery?->harga_beli ?? 0;

            $bayarSupplier  = $sale->laku * $hargaBeli;
            $pendapatanJual = $sale->laku * $sale->harga_jual;
            $keuntungan     = $pendapatanJual - $bayarSupplier;

            return [
                'tanggal'         => $sale->tanggal,
                'nama_produk'     => $sale->nama_produk,
                'kirim'           => $kirim,
                'laku'            => $sale->laku,
                'sisa'            => $kirim - $sale->laku,
                'harga_beli'      => $hargaBeli,
                'harga_jual'      => $sale->harga_jual,
                'bayar_supplier'  => $bayarSupplier,
                'pendapatan_jual' => $pendapatanJual,
                'keuntungan'      => $keuntungan,
            ];
        })->groupBy('tanggal');

        return view('payments.nota', compact('payment', 'details'));
    }
}
