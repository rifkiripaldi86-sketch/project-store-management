<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashController extends Controller
{
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