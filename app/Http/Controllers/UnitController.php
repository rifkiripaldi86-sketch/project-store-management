<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    // Method index yang sempat hilang
    public function index()
    {
        $units = Unit::latest()->get();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

public function store(Request $request)
{
$request->validate([
        'name' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
    ]);

    Unit::create([
        'name' => $request->name,
        'keterangan' => $request->keterangan,
    ]);

    return redirect()->route('units.index')->with('success', 'Berhasil!');
}
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.edit', compact('unit'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:units,name,' . $id,
        'keterangan' => 'nullable|string',
    ]);

    $unit = Unit::findOrFail($id);
    $unit->update($request->all());
    return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui!');
}
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->products()->exists()) {
            return redirect()->route('units.index')->with('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh beberapa produk!');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil dihapus!');
    }
}
