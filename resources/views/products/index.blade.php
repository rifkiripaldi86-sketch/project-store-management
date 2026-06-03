@extends('layouts.app')
@section('title', 'Data Produk')

@push('styles')
<style>
    /* Header */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .page-header h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 2px;
        letter-spacing: -0.3px;
    }

    .page-header p {
        font-size: 13px;
        color: var(--ink-muted);
        margin: 0;
    }

    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
        animation: fadeSlideUp 0.4s 0.05s ease both;
    }

    @media (max-width: 640px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        color: var(--ink-muted);
        margin-bottom: 6px;
    }

    .stat-value {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -0.3px;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--ink-muted);
        margin-top: 4px;
    }

    /* Main card */
    .main-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeSlideUp 0.4s 0.1s ease both;
    }

    .main-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .main-card-header-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--r-md);
        background: var(--accent-soft);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .main-card-header h5 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
    }

    /* Filter area */
    .toolbar {
        margin-left: auto;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: var(--ink-muted);
    }

    .search-input,
    .filter-select {
        height: 38px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        font-size: 13px;
        color: var(--ink);
        transition: all 0.15s;
    }

    .search-input {
        padding: 0 14px 0 36px;
        width: 100%;
    }

    .filter-select {
        padding: 0 12px;
        min-width: 170px;
    }

    .search-input:focus,
    .filter-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
        outline: none;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        color: var(--ink-muted);
        padding: 12px 16px;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
        color: var(--ink);
        vertical-align: middle;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr {
        transition: background 0.15s;
    }

    .data-table tbody tr:hover {
        background: var(--bg);
    }

    /* Product */
    .product-name {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .product-icon {
        width: 34px;
        height: 34px;
        border-radius: var(--r-md);
        background: var(--accent-soft);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    /* Stock badge */
    .stock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid;
    }

    .stock-high {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }

    .stock-medium {
        background: #fefce8;
        color: #ca8a04;
        border-color: #fde68a;
    }

    .stock-low {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Actions */
    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.15s;
        font-size: 12px;
    }

    .action-btn-edit {
        background: #fef3c7;
        color: #d97706;
        border-color: #fde68a;
    }

    .action-btn-edit:hover {
        background: #fde68a;
        color: #b45309;
    }

    .action-btn-delete {
        background: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .action-btn-delete:hover {
        background: #fecaca;
        color: #b91c1c;
    }

    /* Empty */
    .empty-state {
        text-align: center;
        padding: 52px 24px;
    }

    .empty-state-icon {
        width: 58px;
        height: 58px;
        border-radius: var(--r-xl);
        background: var(--bg);
        border: 1px solid var(--border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--ink-muted);
        margin-bottom: 14px;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
        color: var(--ink-muted);
    }

    /* Pagination */
    .pagination-wrap {
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        background: var(--bg);
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <div>
        <h1>Data Produk</h1>
        <p>Kelola seluruh produk toko</p>
    </div>

    <a href="{{ route('products.create') }}"
       class="btn btn-primary d-flex align-items-center gap-2"
       style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 18px;">
        <i class="fas fa-plus" style="font-size:11px;"></i>
        Tambah Produk
    </a>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Produk</div>
        <div class="stat-value">{{ $products->total() }}</div>
        <div class="stat-sub">Produk terdaftar</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Stok</div>
        <div class="stat-value">
            {{ number_format($products->sum('current_stock')) }}
        </div>
        <div class="stat-sub">Seluruh stok produk</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value">
            {{ $products->where('current_stock', '<=', 10)->count() }}
        </div>
        <div class="stat-sub">Produk stok rendah</div>
    </div>
</div>

{{-- Main Table --}}
<div class="main-card">

    {{-- Header --}}
    <div class="main-card-header">
        <div class="main-card-header-icon">
            <i class="fas fa-cake-candles"></i>
        </div>

        <h5>Daftar Produk</h5>

        {{-- Filter & Search --}}
        <form method="GET"
              action="{{ route('products.index') }}"
              class="toolbar"
              id="filterForm">

            {{-- Simpan filter supplier yang sedang aktif saat ganti search/stok --}}
            @if(request('supplier_id'))
                <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
            @endif

            {{-- Info filter supplier aktif --}}
            @if(request('supplier_id'))
                <div class="d-flex align-items-center me-2">
                    <span class="badge bg-secondary d-flex align-items-center gap-2" style="height: 38px; padding: 0 12px; font-size: 12px; border-radius: var(--r-sm);">
                        Filter: {{ $products->first()?->supplier?->nama_supplier ?? 'Supplier Selected' }}
                        <a href="{{ route('products.index', request()->except('supplier_id')) }}" class="text-white text-decoration-none ms-1">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </span>
                </div>
            @endif

            {{-- Search --}}
            <div class="search-box">
                <i class="fas fa-search"></i>

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Cari produk..."
                    value="{{ request('search') }}"
                >
            </div>

            {{-- Filter Stok --}}
            <select
                name="stock"
                class="filter-select"
                onchange="this.form.submit()"
            >
                <option value="">Semua Stok</option>
                <option value="high" {{ request('stock') == 'high' ? 'selected' : '' }}>
                    Stok Aman
                </option>
                <option value="medium" {{ request('stock') == 'medium' ? 'selected' : '' }}>
                    Stok Sedang
                </option>
                <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>
                    Stok Menipis
                </option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Produk</th>
                    <th>Supplier Resmi</th> {{-- Kolom Baru --}}
                    <th style="width:180px;">Stok Saat Ini</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $index => $product)

                @php
                    $stockClass = 'stock-high';

                    if($product->current_stock <= 10) {
                        $stockClass = 'stock-low';
                    } elseif($product->current_stock <= 30) {
                        $stockClass = 'stock-medium';
                    }
                @endphp

                <tr>
                    <td style="color:var(--ink-muted); font-size:12px;">
                        {{ $index + $products->firstItem() }}
                    </td>

                    <td>
                        <div class="product-name">
                            <div class="product-icon">
                                <i class="fas fa-box"></i>
                            </div>

                            <div>
                                {{ $product->nama_produk }}
                            </div>
                        </div>
                    </td>

                    {{-- Data Supplier dengan Link Filter Otomatis --}}
                    <td>
                        @if($product->supplier)
                            <a href="{{ route('products.index', array_merge(request()->query(), ['supplier_id' => $product->supplier_id])) }}"
                               class="text-decoration-none fw-bold text-accent"
                               style="color: var(--accent); font-size: 13px;"
                               title="Klik untuk filter supplier ini">
                                <i class="fas fa-truck-field me-1" style="font-size: 11px;"></i>
                                {{ $product->supplier->nama_supplier }}
                            </a>
                        @else
                            <span class="text-muted" style="font-size: 12px; font-style: italic;">Belum di-set</span>
                        @endif
                    </td>

                    <td>
                        <span class="stock-badge {{ $stockClass }}">
                            {{ number_format($product->current_stock) }} Stok
                        </span>
                    </td>

                    <td>
                        <div class="action-buttons">

                            <a href="{{ route('products.edit', $product) }}"
                               class="action-btn action-btn-edit"
                               title="Edit Produk">
                                <i class="fas fa-pen"></i>
                            </a>

                            <form action="{{ route('products.destroy', $product) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus produk ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn action-btn-delete"
                                        title="Hapus Produk">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="5" style="padding:0; border:none;"> {{-- Diubah ke colspan 5 agar sejajar --}}
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-box-open"></i>
                            </div>

                            <p>Belum ada data produk</p>

                            <div class="mt-3">
                                <a href="{{ route('products.create') }}"
                                   class="btn btn-primary btn-sm">
                                    Tambah Produk Pertama
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="pagination-wrap">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
