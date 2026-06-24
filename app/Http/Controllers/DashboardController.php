<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CashFlow;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

// penjualan
$totalPenjualanHariIni = Sale::whereDate('tanggal', $today)->sum('total_bayar');

$totalBarangTerjual = SaleItem::whereHas('sale', function ($q) use ($today) {
    $q->whereDate('tanggal', $today);
})->sum('laku');

// kas
$kasMasuk = CashFlow::whereDate('tanggal', $today)->where('tipe', 'masuk')->sum('jumlah');

$kasKeluar = CashFlow::whereDate('tanggal', $today)->where('tipe', 'keluar')->sum('jumlah');

$labaHariIni = $kasMasuk - $kasKeluar;

        // Grafik penjualan 7 hari (termasuk hari ini, isi 0 untuk hari tanpa transaksi)
$salesRaw = Sale::whereBetween('tanggal', [
        Carbon::today()->subDays(6),
        Carbon::today()->endOfDay()
    ])
    ->selectRaw('DATE(tanggal) as date, SUM(total_bayar) as total')
    ->groupBy(DB::raw('DATE(tanggal)'))
    ->pluck('total', 'date');

$sales7days = [];
$labels7days = [];

for ($i = 6; $i >= 0; $i--) {
    $date = Carbon::today()->subDays($i);

    $labels7days[] = $date->format('d M');
    $sales7days[] = (int) ($salesRaw[$date->format('Y-m-d')] ?? 0);
}
    
        // Grafik laba bulanan (6 bulan terakhir)
$monthlyProfit = [];

for ($i = 5; $i >= 0; $i--) {
    $month = Carbon::now()->subMonths($i);
    $monthName = $month->format('M Y');

    $masuk = CashFlow::whereMonth('tanggal', $month->month)
        ->whereYear('tanggal', $month->year)
        ->where('tipe', 'masuk')
        ->sum('jumlah');

    $keluar = CashFlow::whereMonth('tanggal', $month->month)
        ->whereYear('tanggal', $month->year)
        ->where('tipe', 'keluar')
        ->sum('jumlah');

    $monthlyProfit[$monthName] = $masuk - $keluar;
}

        return view('dashboard', compact(
    'totalPenjualanHariIni',
    'totalBarangTerjual',
    'kasMasuk',
    'kasKeluar',
    'labaHariIni',
    'sales7days',
    'labels7days',
    'monthlyProfit'
));
    }
}