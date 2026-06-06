@extends('layouts.app')
@section('title', 'Data Produk')

@push('styles')
<style>
    /* CSS Lengkap */
    .page-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .page-header h1 { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 2px; }
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
    .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 18px 20px; box-shadow: var(--shadow-sm); }
    .stat-value { font-size: 22px; font-weight: 700; color: var(--ink); }
    .main-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
    .main-card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .search-box { position: relative; min-width: 250px; }
    .search-input { width: 100%; height: 38px; padding: 0 14px 0 36px; border-radius: var(--r-sm); border: 1px solid var(--border); }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { font-size: 10.5px; text-transform: uppercase; color: var(--ink-muted); padding: 12px 16px; background: var(--bg); border-bottom: 1px solid var(--border); }
    .data-table td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13.5px; }
    .stock-badge { padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; border: 1px solid; }
    .stock-low { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .stock-high { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .action-btn { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border); display: inline-flex; align-items: center; justify-content: center; }
</style>
@endpush

@section('content')
<div class="page-header animate-in">
    <div>
        <h1>Data Produk</h1>
        <p>Kelola seluruh produk toko</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height:38px; padding:0 18px;">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Produk</div>
        <div class="stat-value">{{ $products->total() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Stok</div>
        <div class="stat-value">{{ number_format($products->sum('current_stock')) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value">{{ $products->where('current_stock', '<=', 10)->count() }}</div>
    </div>
</div>

{{-- Main Table --}}
<div class="main-card">
    <div class="main-card-header">
        <h5>Daftar Produk</h5>
        <form method="GET" action="{{ route('products.index') }}" class="ms-auto d-flex gap-2">
            <div class="search-box">
                <i class="fas fa-search" style="position:absolute; left:12px; top:12px; color:var(--ink-muted);"></i>
                <input type="text" name="search" class="search-input" placeholder="Cari produk..." value="{{ request('search') }}">
            </div>
            <select name="stock" class="form-select" style="width:150px;" onchange="this.form.submit()">
                <option value="">Semua Stok</option>
                <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
            </select>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $prod)
                <tr>
                    <td>{{ $index + $products->firstItem() }}</td>
                    <td class="fw-bold">{{ $prod->nama_produk }}</td>
                    <td>{{ $prod->category->nama_kategori ?? '-' }}</td>
                    <td>{{ $prod->unit->name ?? '-' }}</td>
                    <td>Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</td>
                    <td>
                        <span class="stock-badge {{ $prod->current_stock <= 10 ? 'stock-low' : 'stock-high' }}">
                            {{ $prod->current_stock }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.edit', $prod) }}" class="action-btn" style="background:#fef3c7; color:#d97706;"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('products.destroy', $prod) }}" method="POST" onsubmit="return confirm('Hapus produk?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn" style="background:#fee2e2; color:#dc2626; border:none;"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $products->links() }}</div>
</div>
@endsection
