@extends('layouts.app')
@section('title', 'Data Supplier')

@push('styles')
<style>
    /* ─── Page Header ─────────────────────────────────── */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
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

    /* ─── Stats Row ───────────────────────────────────── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    @media (max-width: 640px) {
        .stats-row { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        animation: fadeSlideUp 0.4s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.10s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        color: var(--ink-muted);
        margin-bottom: 8px;
    }

    .stat-value {
        font-family: 'Sora', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1;
        margin-bottom: 6px;
    }

    .stat-value.text-md {
        font-size: 18px;
        line-height: 1.3;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--ink-muted);
    }

    .stat-accent {
        display: inline-block;
        width: 28px;
        height: 28px;
        background: var(--accent-soft);
        border-radius: var(--r-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--accent);
        margin-bottom: 10px;
    }

    /* ─── Main Card ───────────────────────────────────── */
    .main-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeSlideUp 0.4s 0.2s ease both;
    }

    .main-card-header {
        padding: 18px 24px;
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
        background: var(--accent-soft);
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: var(--accent);
        flex-shrink: 0;
    }

    .main-card-header h5 {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
        flex: 1;
    }

    /* ─── Search ──────────────────────────────────────── */
    .search-input-wrap {
        display: flex;
        align-items: center;
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        background: var(--surface);
        overflow: hidden;
        height: 36px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .search-input-wrap:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
    }

    .search-input-wrap input {
        border: none;
        outline: none;
        background: transparent;
        padding: 0 12px;
        font-size: 13px;
        color: var(--ink);
        width: 210px;
    }

    .search-input-wrap input::placeholder {
        color: var(--ink-muted);
    }

    .search-input-wrap button {
        border: none;
        background: var(--accent);
        color: #fff;
        padding: 0 14px;
        height: 100%;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .search-input-wrap button:hover {
        background: #1d4ed8;
    }

    /* ─── Table ───────────────────────────────────────── */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: var(--ink-muted);
        background: var(--bg);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.12s;
    }

    .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .data-table tbody tr:hover {
        background: var(--bg);
    }

    .data-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: var(--ink);
        vertical-align: middle;
    }

    /* ─── Cell: Supplier Name ─────────────────────────── */
    .supplier-name {
        font-weight: 600;
        color: var(--ink);
        font-size: 13.5px;
    }

    /* ─── Cell: Contact Badge ─────────────────────────── */
    .contact-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 999px;
        font-size: 12px;
        color: var(--ink-soft);
    }

    /* ─── Cell: Address ───────────────────────────────── */
    .address-text {
        font-size: 13px;
        color: var(--ink-muted);
        max-width: 240px;
    }

    /* ─── Cell: Product Count Badge ──────────────────── */
    .product-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: 36px;
        justify-content: center;
        height: 26px;
        background: var(--accent-soft);
        border-radius: 999px;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
    }

    /* ─── Action Buttons ──────────────────────────────── */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: var(--r-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.15s;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .action-view {
        background: var(--accent-soft);
        color: var(--accent);
        border-color: #bfdbfe;
    }

    .action-view:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .action-edit {
        background: #fef9c3;
        color: #ca8a04;
        border-color: #fde68a;
    }

    .action-edit:hover {
        background: #fef08a;
        color: #a16207;
    }

    .action-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .action-delete:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ─── Empty State ─────────────────────────────────── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        color: var(--ink-muted);
    }

    .empty-state-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--r-md);
        background: var(--bg);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--ink-muted);
        margin-bottom: 14px;
        opacity: 0.7;
    }

    .empty-state p {
        font-size: 13.5px;
        margin: 0;
    }

    /* ─── Pagination ──────────────────────────────────── */
    .pagination-wrap {
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        justify-content: flex-end;
    }

    /* ─── Alert Flash ─────────────────────────────────── */
    .flash-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 20px;
        animation: fadeSlideUp 0.3s ease both;
    }

    .flash-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .flash-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .flash-info {
        background: var(--accent-soft);
        border: 1px solid #bfdbfe;
        color: var(--accent);
    }
</style>
@endpush

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="flash-alert flash-success">
        <i class="fas fa-circle-check" style="font-size:14px;"></i>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flash-alert flash-error">
        <i class="fas fa-circle-xmark" style="font-size:14px;"></i>
        {{ session('error') }}
    </div>
@endif
@if(session('info'))
    <div class="flash-alert flash-info">
        <i class="fas fa-circle-info" style="font-size:14px;"></i>
        {{ session('info') }}
    </div>
@endif

{{-- Page Header --}}
<div class="page-header animate-in">
    <div>
        <h1>Data Supplier</h1>
        <p>Kelola daftar supplier toko kue Anda</p>
    </div>

    <a href="{{ route('suppliers.create') }}"
        class="btn btn-primary d-flex align-items-center gap-2"
        style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 18px;">
        <i class="fas fa-plus" style="font-size:11px;"></i>
        Tambah Supplier
    </a>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-accent"><i class="fas fa-truck"></i></div>
        <div class="stat-label">Total Supplier</div>
        <div class="stat-value">{{ $suppliers->total() }}</div>
        <div class="stat-sub">Supplier terdaftar</div>
    </div>

    <div class="stat-card">
        <div class="stat-accent"><i class="fas fa-list"></i></div>
        <div class="stat-label">Data Ditampilkan</div>
        <div class="stat-value">{{ $suppliers->count() }}</div>
        <div class="stat-sub">Pada halaman ini</div>
    </div>

    <div class="stat-card">
        <div class="stat-accent"><i class="fas fa-magnifying-glass"></i></div>
        <div class="stat-label">Pencarian</div>
        <div class="stat-value text-md">
            {{ request('search') ? 'Aktif' : 'Semua Data' }}
        </div>
        <div class="stat-sub">
            {{ request('search') ?: 'Tanpa filter pencarian' }}
        </div>
    </div>
</div>

{{-- Main card --}}
<div class="main-card">
    <div class="main-card-header">
        <div class="main-card-header-icon">
            <i class="fas fa-truck"></i>
        </div>

        <h5>Daftar Supplier</h5>

        <div class="search-box ms-auto">
            <form method="GET" action="{{ route('suppliers.index') }}">
                <div class="search-input-wrap">
                    <input type="text"
                        name="search"
                        placeholder="Cari supplier..."
                        value="{{ request('search') }}">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Nama Supplier</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th style="width:100px; text-align:center;">Jml Produk</th>
                    <th style="width:120px; text-align:center;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($suppliers as $index => $supplier)
                <tr>
                    <td style="color:var(--ink-muted); font-size:12px; font-weight:500;">
                        {{ $index + $suppliers->firstItem() }}
                    </td>

                    <td>
                        <div class="supplier-name">{{ $supplier->nama_supplier }}</div>
                    </td>

                    <td>
                        <span class="contact-badge">
                            <i class="fas fa-phone" style="font-size:10px;"></i>
                            {{ $supplier->telepon ?: '—' }}
                        </span>
                    </td>

                    <td class="address-text">
                        {{ $supplier->alamat ? Str::limit($supplier->alamat, 55) : '—' }}
                    </td>

                    <td style="text-align:center;">
                        <span class="product-count-badge">
                            <i class="fas fa-box" style="font-size:9px;"></i>
                            {{ $supplier->products_count ?? 0 }}
                        </span>
                    </td>

                    <td>
                        <div class="action-buttons" style="justify-content:center;">
                            <a href="{{ route('suppliers.show', $supplier) }}"
                                class="action-btn action-view"
                                title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('suppliers.edit', $supplier) }}"
                                class="action-btn action-edit"
                                title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>

                            <form action="{{ route('suppliers.destroy', $supplier) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="action-btn action-delete"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:0; border:none;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <p style="margin-bottom:4px; font-weight:500; color:var(--ink);">Belum ada data supplier</p>
                            <p style="font-size:12px;">
                                @if(request('search'))
                                    Tidak ada hasil untuk "<strong>{{ request('search') }}</strong>"
                                @else
                                    Mulai dengan menambah supplier pertama Anda
                                @endif
                            </p>
                            @if(!request('search'))
                            <a href="{{ route('suppliers.create') }}"
                                class="btn btn-primary mt-3"
                                style="height:38px; font-size:13px; border-radius:var(--r-sm); padding:0 16px;">
                                Tambah Supplier Pertama
                            </a>
                            @else
                            <a href="{{ route('suppliers.index') }}"
                                class="btn btn-secondary mt-3"
                                style="height:36px; font-size:13px; border-radius:var(--r-sm); padding:0 16px;">
                                <i class="fas fa-xmark me-1" style="font-size:11px;"></i>
                                Reset Pencarian
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
    <div class="pagination-wrap">
        {{ $suppliers->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
