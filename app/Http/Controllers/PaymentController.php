<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentDetail;
use App\Models\CashFlow;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create()
    {
        $suppliers        = Supplier::orderBy('nama_supplier')->get();
        $defaultStoreName = config('app.store_name', config('app.name', 'TOKO'));
        return view('payments.create', compact('suppliers', 'defaultStoreName'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'nama_toko'     => 'nullable|string|max:100',
        ]);

        // Ambil harga beli & jumlah kiriman per produk dari delivery dalam periode
        $deliveryItems = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $validated['supplier_id'])
            ->whereBetween('d.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->select(
                'di.product_id',
                DB::raw('MAX(di.harga) as harga'),
                DB::raw('SUM(di.jumlah_kirim) as total_kirim')
            )
            ->groupBy('di.product_id')
            ->get();
$hargaBeliMap = $deliveryItems->pluck('harga', 'product_id');
$kirimMap     = $deliveryItems->pluck('total_kirim', 'product_id');
$productIds   = $hargaBeliMap->keys();

$saleItems = DB::table('sale_items as si')
    ->join('sales as s', 'si.sale_id', '=', 's.id')
    ->whereIn('si.product_id', $productIds)
    ->whereBetween('s.tanggal', [
        $validated['periode_awal'],
        $validated['periode_akhir']
    ])
    ->select(
        'si.product_id',
        DB::raw('SUM(si.laku) as total_laku'),
        DB::raw('SUM(si.laku * si.harga_jual) as total_pendapatan')
    )
    ->groupBy('si.product_id')
    ->get();

      if ($deliveryItems->isEmpty()) {
            return back()->with('error', 'Tidak ada kiriman pada periode tersebut.');
        }

        $hargaBeliMap = $deliveryItems->pluck('harga', 'product_id');
        $kirimMap     = $deliveryItems->pluck('total_kirim', 'product_id');
        $productIds   = $hargaBeliMap->keys();

        $saleItems = DB::table('sale_items as si')
            ->join('sales as s', 'si.sale_id', '=', 's.id')
            ->whereIn('si.product_id', $productIds)
            ->whereBetween('s.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->select(
                'si.product_id',
                DB::raw('SUM(si.laku) as total_laku'),
                DB::raw('SUM(si.laku * si.harga_jual) as total_pendapatan')
            )
            ->groupBy('si.product_id')
            ->get();

        if ($saleItems->isEmpty()) {
            return back()->with('error', 'Tidak ada penjualan produk dari supplier ini pada periode tersebut.');
        }

        $totalBayarSupplier = 0;
        $totalPendapatan    = 0;

        foreach ($saleItems as $item) {
            $hargaBeli           = $hargaBeliMap->get($item->product_id, 0);
            // Bayar supplier = min(laku, kirim) × harga beli
            // Agar tidak bayar lebih dari yang dikirim jika penjualan melebihi kiriman
            $kirim               = $kirimMap->get($item->product_id, 0);
            $lakuEfektif         = min((int) $item->total_laku, (int) $kirim);
            $totalBayarSupplier += $lakuEfektif * $hargaBeli;
            $totalPendapatan    += $item->total_pendapatan;
        }

        $keuntunganToko = $totalPendapatan - $totalBayarSupplier;
        $namaToko       = trim($validated['nama_toko'] ?? '') ?: config('app.store_name', config('app.name', 'TOKO'));

        $payment = DB::transaction(function () use (
            $validated, $totalBayarSupplier, $totalPendapatan,
            $keuntunganToko, $hargaBeliMap, $kirimMap, $namaToko
        ) {
            $payment = SupplierPayment::create([
                'supplier_id'      => $validated['supplier_id'],
                'nama_toko'        => $namaToko,
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

            $deliveries = \App\Models\Delivery::where('supplier_id', $validated['supplier_id'])
                ->whereBetween('tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
                ->with('items')
                ->get();

            foreach ($deliveries as $delivery) {
                $amount = 0;
                foreach ($delivery->items as $di) {
                    $sold      = DB::table('sale_items as si')
                        ->join('sales as s', 'si.sale_id', '=', 's.id')
                        ->where('si.product_id', $di->product_id)
                        ->whereBetween('s.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
                        ->sum('si.laku');
                    $kirim     = $kirimMap->get($di->product_id, $di->jumlah_kirim ?? 0);
                    $lakuEfek  = min((int) $sold, (int) $kirim);
                    $hargaBeli = $hargaBeliMap->get($di->product_id, $di->harga);
                    $amount   += $lakuEfek * $hargaBeli;
                }

                SupplierPaymentDetail::create([
                    'supplier_payment_id' => $payment->id,
                    'delivery_id'         => $delivery->id,
                    'amount'              => $amount,
                ]);
            }

            $namaSupplier = $payment->supplier->nama_supplier;
            $periode      = "{$validated['periode_awal']} s/d {$validated['periode_akhir']}";

            CashFlow::create([
                'tanggal'    => now(),
                'tipe'       => 'keluar',
                'kategori'   => 'bayar_supplier',
                'keterangan' => "Pembayaran ke supplier {$namaSupplier} periode {$periode}",
                'jumlah'     => $totalBayarSupplier,
                'created_by' => auth()->id(),
            ]);

            if ($keuntunganToko < 0) {
                CashFlow::create([
                    'tanggal'    => now(),
                    'tipe'       => 'keluar',
                    'kategori'   => 'kerugian_penjualan',
                    'keterangan' => "Kerugian dari supplier {$namaSupplier} periode {$periode}",
                    'jumlah'     => abs($keuntunganToko),
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

    $storeName = $payment->nama_toko
        ?? config('app.store_name', config('app.name', 'TOKO'));

    /*
    |--------------------------------------------------------------------------
    | Total kiriman per produk (dipakai untuk pembayaran supplier)
    |--------------------------------------------------------------------------
    */
    $deliveryAgg = DB::table('delivery_items as di')
        ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
        ->where('d.supplier_id', $payment->supplier_id)
        ->whereBetween('d.tanggal', [
            $payment->periode_awal,
            $payment->periode_akhir
        ])
        ->select(
            'di.product_id',
            DB::raw('MAX(di.harga) as harga'),
            DB::raw('SUM(di.jumlah_kirim) as total_kirim')
        )
        ->groupBy('di.product_id')
        ->get();

    $hargaBeliMap = $deliveryAgg->pluck('harga', 'product_id');
    $kirimMap     = $deliveryAgg->pluck('total_kirim', 'product_id');

    /*
    |--------------------------------------------------------------------------
    | Kiriman PER HARI (dipakai untuk Barang Sisa)
    |--------------------------------------------------------------------------
    */
    $deliveryPerDay = DB::table('delivery_items as di')
        ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
        ->where('d.supplier_id', $payment->supplier_id)
        ->whereBetween('d.tanggal', [
            $payment->periode_awal,
            $payment->periode_akhir
        ])
        ->select(
            'd.tanggal',
            'di.product_id',
            DB::raw('SUM(di.jumlah_kirim) as kirim')
        )
        ->groupBy('d.tanggal', 'di.product_id')
        ->get()
        ->keyBy(function ($row) {
            return $row->tanggal . '_' . $row->product_id;
        });

    /*
    |--------------------------------------------------------------------------
    | Penjualan PER HARI
    |--------------------------------------------------------------------------
    */
    $saleData = DB::table('sale_items as si')
        ->join('sales as s', 'si.sale_id', '=', 's.id')
        ->join('products as p', 'si.product_id', '=', 'p.id')
        ->whereIn('si.product_id', $hargaBeliMap->keys())
        ->whereBetween('s.tanggal', [
            $payment->periode_awal,
            $payment->periode_akhir
        ])
        ->select(
            's.tanggal',
            'si.product_id',
            'p.nama_produk',
            DB::raw('SUM(si.laku) as laku'),
            DB::raw('MAX(si.harga_jual) as harga_jual')
        )
        ->groupBy(
            's.tanggal',
            'si.product_id',
            'p.nama_produk'
        )
        ->orderBy('s.tanggal')
        ->get();

    $details = $saleData->map(function ($sale) use (
        $hargaBeliMap,
        $kirimMap,
        $deliveryPerDay
    ) {

        $hargaBeli = (int) $hargaBeliMap->get($sale->product_id, 0);

        /*
        |--------------------------------------------------------------------------
        | Barang Sisa = Kiriman Hari Ini - Penjualan Hari Ini
        |--------------------------------------------------------------------------
        */
        $key = $sale->tanggal . '_' . $sale->product_id;

        $stokHariIni = 0;

        if ($deliveryPerDay->has($key)) {
            $stokHariIni = (int) $deliveryPerDay[$key]->kirim;
        }

        $lakuHariIni = (int) $sale->laku;

        $barangSisa = max(0, $stokHariIni - $lakuHariIni);

        /*
        |--------------------------------------------------------------------------
        | Pembayaran Supplier tetap berdasarkan total kiriman periode
        |--------------------------------------------------------------------------
        */
        $stokPeriode = (int) $kirimMap->get($sale->product_id, 0);

        $lakuEfektif = min($lakuHariIni, $stokPeriode);

        $bayarSupplier = $lakuEfektif * $hargaBeli;

        $pendapatan = $lakuHariIni * (int) $sale->harga_jual;

        return [
            'tanggal'        => $sale->tanggal,
            'nama_produk'    => $sale->nama_produk,
            'stok'           => $stokHariIni,
            'laku'           => $lakuHariIni,
            'sisa'           => $barangSisa,
            'harga_beli'     => $hargaBeli,
            'harga_jual'     => (int) $sale->harga_jual,
            'bayar_supplier' => $bayarSupplier,
            'pendapatan'     => $pendapatan,
            'keuntungan'     => $pendapatan - $bayarSupplier,
        ];

    })->groupBy('tanggal');
    
    return view('payments.nota', compact(
        'payment',
        'details',
        'storeName'
    ));

} 

public function history(Request $request)
{
    $query = SupplierPayment::with('supplier')
        ->latest();

    if ($request->supplier_id) {
        $query->where('supplier_id', $request->supplier_id);
    }

    $payments = $query->paginate(20);

    $suppliers = Supplier::orderBy('nama_supplier')->get();

    return view('payments.history', compact(
        'payments',
        'suppliers'
    ));
}

public function destroy($id)
{
    $payment = SupplierPayment::findOrFail($id);
    $payment->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus');
}
}