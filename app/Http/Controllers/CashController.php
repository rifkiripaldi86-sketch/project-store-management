<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index()
    {
        $cashFlows = CashFlow::latest()->paginate(10);

        // Hitung saldo sekali dengan satu query
        $saldo = CashFlow::selectRaw("
            SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) -
            SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as saldo
        ")->value('saldo') ?? 0;

        return view('cash.index', compact('cashFlows', 'saldo'));
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
