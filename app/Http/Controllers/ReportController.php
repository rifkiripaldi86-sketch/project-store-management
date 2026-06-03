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
    /**
     * Ambil data laporan per supplier untuk rentang tanggal tertentu.
     * Kolom: No, Tgl (dari delivery), Produk, Harga Beli, Harga Jual,
     *        Stok (kiriman), Laku, Sisa, Penjualan, Laba
     */
    private function getSupplierReport(int $supplierId, Carbon $start, Carbon $end): array
    {
        // Ambil semua produk milik supplier ini (dari product.supplier_id)
        // beserta delivery item terakhirnya (untuk harga beli & tanggal)
        $products = Product::where('supplier_id', $supplierId)
            ->with(['deliveryItems' => function ($q) {
                // Urutkan DESC agar delivery terakhir mudah diambil
                $q->with('delivery')->orderByDesc('id');
            }])
            ->get();

        $rows = [];
        $no   = 1;

        foreach ($products as $product) {
            // Ambil delivery item terakhir untuk harga beli & tanggal referensi
            $lastDeliveryItem = $product->deliveryItems->first();

            // Harga beli: dari delivery item terakhir; 0 jika belum pernah ada kiriman
            $hargaBeli = $lastDeliveryItem?->harga ?? 0;

            // Tanggal: tanggal delivery terakhir; jika belum ada, pakai $start
            $tanggal = $lastDeliveryItem?->delivery?->tanggal
                ? Carbon::parse($lastDeliveryItem->delivery->tanggal)->format('d/m/Y')
                : $start->format('d/m/Y');

            // Total stok dikirim dari supplier ini s.d. akhir periode
            $stok = $product->deliveryItems()
                ->whereHas('delivery', fn($q) => $q
                    ->where('supplier_id', $supplierId)
                    ->where('tanggal', '<=', $end))
                ->sum('jumlah_kirim');

            // Harga jual: ambil dari sale_items produk ini dalam periode
            $hargaJual = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                ->value('harga_jual') ?? 0;

            // Laku: total terjual dalam periode yang dipilih
            $laku = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                ->sum('laku');

            // Lewati produk yang tidak ada kiriman sama sekali DAN tidak laku
            if ($stok == 0 && $laku == 0) {
                continue;
            }

            $sisa      = max(0, $stok - $laku);
            $penjualan = $hargaJual * $laku;
            $laba      = ($hargaJual - $hargaBeli) * $laku;

            $rows[] = [
                'no'        => $no++,
                'tanggal'   => $tanggal,
                'produk'    => $product->nama_produk,
                'hargaBeli' => $hargaBeli,
                'hargaJual' => $hargaJual,
                'stok'      => $stok,
                'laku'      => $laku,
                'sisa'      => $sisa,
                'penjualan' => $penjualan,
                'laba'      => $laba,
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

        // Data supplier report (mode filter supplier)
        $supplierRows = [];
        if ($supplierId) {
            $supplierRows = $this->getSupplierReport($supplierId, $date->copy()->startOfDay(), $date->copy()->endOfDay());
        }

        $salesTotal    = Sale::whereDate('tanggal', $date)->sum('total_bayar');
        $barangTerjual = SaleItem::whereHas('sale', fn($q) => $q->whereDate('tanggal', $date))->sum('laku');
        $kasMasuk      = CashFlow::whereDate('tanggal', $date)->where('tipe', 'masuk')->sum('jumlah');
        $kasKeluar     = CashFlow::whereDate('tanggal', $date)->where('tipe', 'keluar')->sum('jumlah');
        $laba          = $kasMasuk - $kasKeluar;

        $saleItems = SaleItem::whereHas('sale', fn($q) => $q->whereDate('tanggal', $date))
            ->selectRaw('product_id, SUM(laku) as total_quantity, SUM(sub_total) as total_amount')
            ->with('product:id,nama_produk')
            ->groupBy('product_id')
            ->get()
            ->map(fn($item) => (object) [
                'product_name'   => $item->product->nama_produk,
                'total_quantity' => $item->total_quantity,
                'total_amount'   => $item->total_amount,
            ]);

        $cashIn  = CashFlow::whereDate('tanggal', $date)->where('tipe', 'masuk')->orderBy('id')->get();
        $cashOut = CashFlow::whereDate('tanggal', $date)->where('tipe', 'keluar')->orderBy('id')->get();

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

        $totalPenjualan       = Sale::whereBetween('tanggal', [$start, $end])->sum('total_bayar');
        $totalBayarSupplier   = SupplierPayment::whereBetween('tanggal_bayar', [$start, $end])->sum('total_bayar');
        $totalPengeluaranLain = CashFlow::whereBetween('tanggal', [$start, $end])
            ->where('tipe', 'keluar')
            ->where('kategori', '!=', 'bayar_supplier')
            ->sum('jumlah');
        $laba = $totalPenjualan - ($totalBayarSupplier + $totalPengeluaranLain);

        $weeklyData = [];
        $weekStart  = $start->copy()->startOfWeek();
        $weekNumber = 1;

        while ($weekStart->lte($end)) {
            $weekEnd  = $weekStart->copy()->endOfWeek()->min($end);
            $sales    = Sale::whereBetween('tanggal', [$weekStart, $weekEnd])->sum('total_bayar');
            $expenses = CashFlow::whereBetween('tanggal', [$weekStart, $weekEnd])
                ->where('tipe', 'keluar')
                ->sum('jumlah');

            $weeklyData[] = [
                'week'   => $weekNumber,
                'sales'  => $sales,
                'profit' => $sales - $expenses,
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

        for ($i = 1; $i <= 12; $i++) {
            $start = Carbon::create($year, $i, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $penjualan   = Sale::whereBetween('tanggal', [$start, $end])->sum('total_bayar');
            $pengeluaran = CashFlow::whereBetween('tanggal', [$start, $end])
                ->where('tipe', 'keluar')
                ->sum('jumlah');

            $monthlyData[] = [
                'bulan'     => $start->translatedFormat('F'),
                'penjualan' => $penjualan,
                'laba'      => $penjualan - $pengeluaran,
            ];
        }

        return view('reports.yearly', compact(
            'year', 'monthlyData',
            'suppliers', 'supplierId', 'supplier', 'supplierRows'
        ));
    }
}
