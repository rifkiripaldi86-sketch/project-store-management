<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required']);
        Category::create($request->all());
        return back()->with('success', 'Kategori berhasil ditambah!');
    }

    public function destroy(Category $category)
    {
        // Ingat planning kita: Pakai logic agar tidak bisa hapus jika ada produk
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus, masih ada produk!');
        }

        $category->delete();
        return back()->with('success', 'Kategori dihapus!');
    }
}
