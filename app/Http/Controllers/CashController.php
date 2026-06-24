<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashController extends Controller
{
<<<<<<< HEAD
public function index(Request $request)
    {
        // 1. Inisialisasi Query dasar dengan relasi createdBy
        $query = CashFlow::with('createdBy')->latest();

        // 2. Terapkan Filter Search (Keterangan / Kategori)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Terapkan Filter Tipe (Masuk / Keluar)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // 4. Terapkan Filter Rentang Tanggal (Dari & Sampai)
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        // 5. Hitung Total Masuk & Keluar secara akurat berdasarkan filter (sebelum ter-paginate)
        $totalMasuk  = (clone $query)->where('tipe', 'masuk')->sum('jumlah') ?? 0;
        $totalKeluar = (clone $query)->where('tipe', 'keluar')->sum('jumlah') ?? 0;

        // 6. Ambil data akhir dengan pagination
        $cashFlows = $query->paginate(10);

        return view('cash.index', compact('cashFlows', 'totalMasuk', 'totalKeluar'));
    }
=======
   public function index(Request $request)
{
    // =========================
    // TABLE QUERY (PAKAI FILTER)
    // =========================
    $tableQuery = CashFlow::query();

    if ($request->filled('search')) {
        $tableQuery->where(function ($q) use ($request) {
            $q->where('keterangan', 'like', '%' . $request->search . '%')
              ->orWhere('kategori', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('tipe')) {
        $tableQuery->where('tipe', $request->tipe);
    }

    if ($request->filled('dari')) {
        $tableQuery->whereDate('tanggal', '>=', $request->dari);
    }

    if ($request->filled('sampai')) {
        $tableQuery->whereDate('tanggal', '<=', $request->sampai);
    }

    // =========================
    // TABLE DATA
    // =========================
    $cashFlows = (clone $tableQuery)
        ->latest()
        ->paginate(10);

    // =========================
    // SUMMARY (TANPA FILTER)
    // =========================
    $kasMasuk = CashFlow::where('tipe', 'masuk')->sum('jumlah');

$kasKeluar = CashFlow::where('tipe', 'keluar')->sum('jumlah');

$saldo = $kasMasuk - $kasKeluar;

    return view('cash.index', compact(
        'cashFlows',
        'kasMasuk',
        'kasKeluar',
        'saldo'
    ));
}

>>>>>>> 009963ac02f0fe5109ac149256394b6c224ec3b8
    public function create()
    {
        return view('cash.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'    => 'required|date',
            'tipe'       => 'required|in:masuk,keluar',
            'kategori'   => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:500',
            'jumlah'     => 'required|numeric|min:1',
        ]);

        CashFlow::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('cash.index')->with('success', 'Kas berhasil dicatat.');
    }

    public function destroy(CashFlow $cash)
    {
        $cash->delete();
        return redirect()->route('cash.index')->with('success', 'Data kas berhasil dihapus.');
    }
}