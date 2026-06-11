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

        // 1. Ambil data harga beli terakhir berdasarkan periode kiriman
        $hargaBeliMap = DB::table('delivery_items as di')
            ->join('deliveries as d', 'di.delivery_id', '=', 'd.id')
            ->where('d.supplier_id', $validated['supplier_id'])
            ->whereBetween('d.tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->select('di.product_id', DB::raw('MAX(di.harga) as harga'))
            ->groupBy('di.product_id')
            ->pluck('harga', 'product_id');

        if ($hargaBeliMap->isEmpty()) {
            return back()->with('error', "Tidak ada data kiriman dari supplier ini pada periode " . $validated['periode_awal'] . " sampai " . $validated['periode_akhir'] . ". Pastikan tanggal dan supplier sudah benar.");
        }

        $productIds = $hargaBeliMap->keys();

        // 2. Ambil data penjualan produk yang dikirim oleh supplier ini
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
            return back()->with('error', 'Produk dari supplier ini belum ada yang terjual pada periode tersebut.');
        }

        $totalBayarSupplier = 0;
        $totalPendapatan    = 0;

        foreach ($saleItems as $item) {
            $hargaBeli           = $hargaBeliMap->get($item->product_id, 0);
            $totalBayarSupplier += $item->total_laku * $hargaBeli;
            $totalPendapatan    += $item->total_pendapatan;
        }

        $keuntunganToko = $totalPendapatan - $totalBayarSupplier;

        // 3. Proses Transaksi
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

            // Simpan detail pembayaran
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

            // Catat Kas Keluar
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

            // Jika rugi, catat kerugian
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
    // Pastikan relasi di model SupplierPaymentDetail sudah benar (menghubungkan ke delivery_items)
    $payment = SupplierPayment::with(['supplier', 'details.delivery.items.product'])->findOrFail($id);

    $groupedDetails = [];

    foreach ($payment->details as $detail) {
        $delivery = $detail->delivery;
        $tanggal = \Carbon\Carbon::parse($delivery->tanggal)->format('Y-m-d');

        foreach ($delivery->items as $item) {
            // Hitung laku untuk produk ini di periode tersebut
            $laku = \App\Models\SaleItem::where('product_id', $item->product_id)
                ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$payment->periode_awal, $payment->periode_akhir]))
                ->sum('laku');

            $groupedDetails[$tanggal][] = [
                'nama_produk'    => $item->product->nama_produk ?? 'N/A',
                'kirim'          => $item->jumlah_kirim, // Dari tabel delivery_items
                'laku'           => $laku,
                'sisa'           => $item->jumlah_kirim - $laku,
                'harga_beli'     => $item->harga,
                'bayar_supplier' => $laku * $item->harga,
            ];
        }
    }

    $storeName = "NAMA TOKO ANDA";
    return view('payments.nota', compact('payment', 'groupedDetails', 'storeName'));
}
}
