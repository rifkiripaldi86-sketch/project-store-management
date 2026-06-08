<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentDetail;
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

        $hargaBeliMap = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $validated['supplier_id'])
            ->whereBetween('d.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->select('di.product_id', DB::raw('MAX(di.harga) as harga'))
            ->groupBy('di.product_id')
            ->pluck('harga', 'product_id');

        if ($hargaBeliMap->isEmpty()) {
            return back()->with('error', 'Tidak ada kiriman pada periode tersebut.');
        }

        $productIds = $hargaBeliMap->keys();

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
            $totalBayarSupplier += $item->total_laku * $hargaBeli;
            $totalPendapatan    += $item->total_pendapatan;
        }

        $keuntunganToko = $totalPendapatan - $totalBayarSupplier;

        $payment = DB::transaction(function () use ($validated, $totalBayarSupplier, $totalPendapatan, $keuntunganToko, $hargaBeliMap) {
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

            $deliveries = \App\Models\Delivery::where('supplier_id', $validated['supplier_id'])
                ->whereBetween('tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
                ->with('items')
                ->get();

            foreach ($deliveries as $delivery) {
                $amount = 0;
                foreach ($delivery->items as $di) {
                    $sold = DB::table('sale_items as si')
                        ->join('sales as s', 'si.sale_id', '=', 's.id')
                        ->where('si.product_id', $di->product_id)
                        ->whereBetween('s.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
                        ->sum('si.laku');
                    $hargaBeli = $hargaBeliMap->get($di->product_id, $di->harga);
                    $amount   += $sold * $hargaBeli;
                }

                SupplierPaymentDetail::create([
                    'supplier_payment_id' => $payment->id,
                    'delivery_id'         => $delivery->id,
                    'amount'              => $amount,
                ]);
            }

            $namaSupplier = $payment->supplier->nama_supplier;
            $periode      = "{$validated['periode_awal']} s/d {$validated['periode_akhir']}";

            // [BUG #1 FIX] Hanya catat kas KELUAR untuk bayar supplier.
            // Kas masuk dari penjualan sudah dicatat di SaleController — tidak boleh dicatat lagi di sini.
            CashFlow::create([
                'tanggal'    => now(),
                'tipe'       => 'keluar',
                'kategori'   => 'bayar_supplier',
                'keterangan' => "Pembayaran ke supplier {$namaSupplier} periode {$periode}",
                'jumlah'     => $totalBayarSupplier,
                'created_by' => auth()->id(),
            ]);

            // [BUG #2 FIX] Jika toko rugi (keuntungan negatif), catat selisih sebagai
            // kas keluar tambahan agar saldo kas mencerminkan kondisi nyata.
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

            // [BUG #1 FIX] Blok kas masuk "keuntungan_penjualan" dihapus sepenuhnya.
            // Keuntungan sudah tercermin dari: kas masuk (penjualan) - kas keluar (bayar_supplier).

            return $payment;
        });

        return redirect()->route('payments.print', $payment->id)
            ->with('success', 'Pembayaran supplier berhasil dicatat.');
    }

    public function printNota($id)
    {
        $payment = SupplierPayment::with('supplier')->findOrFail($id);

        $hargaBeliMap = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $payment->supplier_id)
            ->whereBetween('d.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select('di.product_id', DB::raw('MAX(di.harga) as harga'))
            ->groupBy('di.product_id')
            ->pluck('harga', 'product_id');

        $kirimPerTanggal = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $payment->supplier_id)
            ->whereBetween('d.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select(
                'd.tanggal',
                'di.product_id',
                DB::raw('SUM(di.jumlah_kirim) as total_kirim')
            )
            ->groupBy('d.tanggal', 'di.product_id')
            ->get()
            ->groupBy(fn($r) => $r->tanggal . '|' . $r->product_id)
            ->map(fn($g) => $g->first()->total_kirim);

        $kirimTotalMap = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $payment->supplier_id)
            ->whereBetween('d.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select('di.product_id', DB::raw('SUM(di.jumlah_kirim) as total_kirim'))
            ->groupBy('di.product_id')
            ->pluck('total_kirim', 'product_id');

        $saleData = DB::table('sale_items as si')
            ->join('sales as s', 'si.sale_id', '=', 's.id')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->whereIn('si.product_id', $hargaBeliMap->keys())
            ->whereBetween('s.tanggal', [$payment->periode_awal, $payment->periode_akhir])
            ->select(
                's.tanggal',
                'si.product_id',
                'p.nama_produk',
                DB::raw('SUM(si.laku) as laku'),
                DB::raw('MAX(si.harga_jual) as harga_jual')
            )
            ->groupBy('s.tanggal', 'si.product_id', 'p.nama_produk')
            ->orderBy('s.tanggal')
            ->get();

        $details = $saleData->map(function ($sale) use ($hargaBeliMap, $kirimPerTanggal, $kirimTotalMap) {
            $hargaBeli     = $hargaBeliMap->get($sale->product_id, 0);
            $kirimKey      = $sale->tanggal . '|' . $sale->product_id;
            $kirim         = $kirimPerTanggal->get($kirimKey, $kirimTotalMap->get($sale->product_id, 0));
            $bayarSupplier = $sale->laku * $hargaBeli;
            $pendapatan    = $sale->laku * $sale->harga_jual;

            return [
                'tanggal'        => $sale->tanggal,
                'nama_produk'    => $sale->nama_produk,
                'kirim'          => $kirim,
                'laku'           => $sale->laku,
                'sisa'           => $kirim - $sale->laku,
                'harga_beli'     => $hargaBeli,
                'harga_jual'     => $sale->harga_jual,
                'bayar_supplier' => $bayarSupplier,
                'pendapatan'     => $pendapatan,
                'keuntungan'     => $pendapatan - $bayarSupplier,
            ];
        })->groupBy('tanggal');

        $storeName = config('app.store_name', config('app.name', 'TOKO'));

        return view('payments.nota', compact('payment', 'details', 'storeName'));
    }
}
