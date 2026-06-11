<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashFlow;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\DeliveryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
private function getSupplierReport(int $supplierId, Carbon $start, Carbon $end): array
{
    // Ambil data produk
    $products = Product::where('supplier_id', $supplierId)->get();

    $rows = [];
    $no = 1;

    foreach ($products as $product) {
        // GANTI DI SINI: Gunakan 'current_stock' sesuai nama kolom database
        $stok = $product->current_stock ?? 0;

        // Hitung produk laku pada rentang tanggal
        $laku = SaleItem::where('product_id', $product->id)
            ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->sum('laku');

        // Jika tidak ada stok dan tidak ada penjualan, jangan tampilkan di laporan
        if ($stok == 0 && $laku == 0) continue;

        // Ambil harga
        $hargaJual = SaleItem::where('product_id', $product->id)
            ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->value('harga_jual') ?? ($product->harga_jual ?? 0);

        $rows[] = [
            'no'        => $no++,
            'tanggal'   => $end->format('d/m/Y'),
            'produk'    => $product->nama_produk,
            'hargaBeli' => $product->harga_beli ?? 0,
            'stok'      => $stok, // Sekarang akan muncul 102
            'laku'      => $laku,
            'sisa'      => max(0, $stok - $laku),
            'penjualan' => $hargaJual * $laku,
        ];
    }
    return $rows;
}

public function daily(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $suppliers   = Supplier::orderBy('nama_supplier')->get();
        $supplierId  = $request->filled('supplier_id') ? (int) $request->supplier_id : null;
        $supplier    = $supplierId ? Supplier::find($supplierId) : null;

        $supplierRows = [];
        if ($supplierId) {
            $supplierRows = $this->getSupplierReport($supplierId, $date->copy()->startOfDay(), $date->copy()->endOfDay());
        }

        // --- QUERY UTAMA REPORT DAILY ---
        $salesQuery = Sale::whereDate('tanggal', $date);
        $itemsQuery = SaleItem::whereHas('sale', fn($q) => $q->whereDate('tanggal', $date));
        $cashInQuery = CashFlow::whereDate('tanggal', $date)->where('tipe', 'masuk');
        $cashOutQuery = CashFlow::whereDate('tanggal', $date)->where('tipe', 'keluar');

        // Filter data berdasarkan supplier jika parameter supplier_id aktif
        if ($supplierId) {
            $salesQuery->whereHas('items.product', fn($q) => $q->where('supplier_id', $supplierId));
            $itemsQuery->whereHas('product', fn($q) => $q->where('supplier_id', $supplierId));
        }

        $barangTerjual = $itemsQuery->sum('laku');

        // Ambil list produk untuk tabel detail
        $saleItems = $itemsQuery
        ->selectRaw('product_id, SUM(laku) as total_quantity, SUM(sub_total) as total_amount')
        ->with('product:id,nama_produk,harga_jual') // FIX: Hapus harga_beli dari sini
        ->groupBy('product_id')
        ->get();

        // FIX LOGIKA SINKRONISASI: Jika filter supplier aktif, sesuaikan kas & omzet murni dari item miliknya
        if ($supplierId) {
            $salesTotal = $saleItems->sum('total_amount');
            $kasMasuk   = $salesTotal;
            $kasKeluar  = 0; // Kas keluar toko tidak dibebankan ke supplier tunggal
            $cashIn     = collect(); // Kosongkan list kas masuk umum toko
            $cashOut    = collect(); // Kosongkan list kas keluar umum toko
        } else {
            $salesTotal = $salesQuery->sum('total_bayar');
            $kasMasuk   = $cashInQuery->sum('jumlah');
            $kasKeluar  = $cashOutQuery->sum('jumlah');
            $cashIn     = $cashInQuery->orderBy('id')->get();
            $cashOut    = $cashOutQuery->orderBy('id')->get();
        }

        $laba = $kasMasuk - $kasKeluar;

        return view('reports.daily', compact(
            'date', 'salesTotal', 'barangTerjual', 'kasMasuk', 'kasKeluar', 'laba',
            'saleItems', 'cashIn', 'cashOut',
            'suppliers', 'supplierId', 'supplier', 'supplierRows'
        ));
    }
public function monthly(Request $request)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month)
            : Carbon::now();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $supplierId = $request->filled('supplier_id') ? (int) $request->supplier_id : null;
        $supplier   = $supplierId ? Supplier::find($supplierId) : null;

        $supplierRows = [];
        if ($supplierId) {
            $supplierRows = $this->getSupplierReport($supplierId, $start, $end);
        }

        // --- HITUNG TOTAL DAN LABA ---
        if ($supplierId) {
            // Jika filter supplier aktif, total penjualan diambil dari ringkasan item supplier tersebut
            $totalPenjualan = collect($supplierRows)->sum('penjualan');
            $totalBayarSupplier = 0;
            $totalPengeluaranLain = 0;
            // Laba murni milik supplier di toko
            $laba = collect($supplierRows)->sum('laba');
        } else {
            $totalPenjualan       = Sale::whereBetween('tanggal', [$start, $end])->sum('total_bayar');
            $totalBayarSupplier   = SupplierPayment::whereBetween('tanggal_bayar', [$start, $end])->sum('total_bayar');
            $totalPengeluaranLain = CashFlow::whereBetween('tanggal', [$start, $end])
                ->where('tipe', 'keluar')
                ->where('kategori', '!=', 'bayar_supplier')
                ->sum('jumlah');
            $laba = $totalPenjualan - ($totalBayarSupplier + $totalPengeluaranLain);
        }

        // --- DATA GRAFIK MINGGUAN ---
        $weeklyData = [];
        $weekStart  = $start->copy()->startOfWeek();
        $weekNumber = 1;

        while ($weekStart->lte($end)) {
            $weekEnd  = $weekStart->copy()->endOfWeek()->min($end);

            if ($supplierId) {
                // Filter mingguan khusus item supplier
                $sales = SaleItem::whereHas('product', fn($q) => $q->where('supplier_id', $supplierId))
                    ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$weekStart, $weekEnd]))
                    ->get()
                    ->sum(function($item) {
                        return $item->laku * ($item->harga_jual ?? $item->product->harga_jual ?? 0);
                    });

                $profit = SaleItem::whereHas('product', fn($q) => $q->where('supplier_id', $supplierId))
                    ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$weekStart, $weekEnd]))
                    ->get()
                    ->sum(function($item) {
                        $hargaJual = $item->harga_jual ?? $item->product->harga_jual ?? 0;
                        $hargaBeli = $item->product->harga_beli ?? 0;
                        return ($hargaJual - $hargaBeli) * $item->laku;
                    });
            } else {
                $sales    = Sale::whereBetween('tanggal', [$weekStart, $weekEnd])->sum('total_bayar');
                $expenses = CashFlow::whereBetween('tanggal', [$weekStart, $weekEnd])
                    ->where('tipe', 'keluar')
                    ->sum('jumlah');
                $profit = $sales - $expenses;
            }

            $weeklyData[] = [
                'week'   => $weekNumber,
                'sales'  => $sales,
                'profit' => $profit,
            ];

            $weekStart->addWeek();
            $weekNumber++;
        }

        return view('reports.monthly', compact(
            'month', 'totalPenjualan', 'totalBayarSupplier', 'totalPengeluaranLain', 'laba',
            'weeklyData', 'suppliers', 'supplierId', 'supplier', 'supplierRows'
        ));
    }
public function yearly(Request $request)
    {
        $year        = (int) ($request->year ?? now()->year);
        $monthlyData = [];

        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $supplierId = $request->filled('supplier_id') ? (int) $request->supplier_id : null;
        $supplier   = $supplierId ? Supplier::find($supplierId) : null;

        $supplierRows = [];
        if ($supplierId) {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end   = Carbon::create($year, 12, 31)->endOfYear();
            $supplierRows = $this->getSupplierReport($supplierId, $start, $end);
        }

        // --- LOOP 12 BULAN UNTUK GRAFIK TAHUNAN ---
        for ($i = 1; $i <= 12; $i++) {
            $start = Carbon::create($year, $i, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            if ($supplierId) {
                // Filter bulanan khusus item supplier terpilih
                $penjualan = SaleItem::whereHas('product', fn($q) => $q->where('supplier_id', $supplierId))
                    ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                    ->get()
                    ->sum(function($item) {
                        return $item->laku * ($item->harga_jual ?? $item->product->harga_jual ?? 0);
                    });

                $laba = SaleItem::whereHas('product', fn($q) => $q->where('supplier_id', $supplierId))
                    ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                    ->get()
                    ->sum(function($item) {
                        $hargaJual = $item->harga_jual ?? $item->product->harga_jual ?? 0;
                        $hargaBeli = $item->product->harga_beli ?? 0;
                        return ($hargaJual - $hargaBeli) * $item->laku;
                    });
            } else {
                $penjualan   = Sale::whereBetween('tanggal', [$start, $end])->sum('total_bayar');
                $pengeluaran = CashFlow::whereBetween('tanggal', [$start, $end])
                    ->where('tipe', 'keluar')
                    ->sum('jumlah');
                $laba = $penjualan - $pengeluaran;
            }

            $monthlyData[] = [
                'bulan'     => $start->translatedFormat('F'),
                'penjualan' => $penjualan,
                'laba'      => $laba,
            ];
        }

        return view('reports.yearly', compact(
            'year', 'monthlyData',
            'suppliers', 'supplierId', 'supplier', 'supplierRows'
        ));
    }
}
