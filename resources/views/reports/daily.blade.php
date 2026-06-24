@extends('layouts.app')
@section('title', 'Laporan Harian')

@push('styles')
<style>
    /* ── Page header ── */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .page-header h1 {
        font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700;
        color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px;
    }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* ── Filter card ── */
    .filter-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); box-shadow: var(--shadow-sm);
        padding: 18px 24px; margin-bottom: 20px;
        display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
        animation: fadeSlideUp 0.3s ease both;
    }
    .filter-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 160px; }
    .filter-label { font-size: 12px; font-weight: 600; color: var(--ink-soft); letter-spacing: 0.3px; }
    .form-control, .form-select {
        border: 1px solid var(--border); border-radius: var(--r-sm);
        font-size: 13.5px; color: var(--ink); padding: 9px 13px; height: auto;
        background: var(--surface); transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,78,216,0.08); outline: none;
    }

    /* ── Supplier mode banner ── */
    .supplier-mode-banner {
        display: flex; align-items: center; gap: 10px;
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: var(--r-xl); padding: 12px 18px; margin-bottom: 20px;
        animation: fadeSlideUp 0.3s ease both;
    }
    .supplier-mode-banner-icon { font-size: 16px; }
    .supplier-mode-banner-text { font-size: 13px; color: #1d4ed8; font-weight: 500; flex: 1; }
    .supplier-mode-banner-clear {
        font-size: 12px; color: #3b82f6; font-weight: 600;
        text-decoration: none; padding: 4px 10px;
        border: 1px solid #bfdbfe; border-radius: var(--r-sm);
        background: #fff; transition: all 0.15s;
    }
    .supplier-mode-banner-clear:hover { background: #dbeafe; color: #1d4ed8; }

    /* ── Stats grid ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 14px; margin-bottom: 20px;
        animation: fadeSlideUp 0.35s 0.05s ease both;
    }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); padding: 18px 20px;
        box-shadow: var(--shadow-sm); position: relative; overflow: hidden;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: var(--r-xl) var(--r-xl) 0 0;
    }
    .stat-card.blue::before   { background: #3b82f6; }
    .stat-card.cyan::before   { background: #06b6d4; }
    .stat-card.green::before  { background: #22c55e; }
    .stat-card.red::before    { background: #ef4444; }

    .stat-icon {
        width: 36px; height: 36px; border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; margin-bottom: 12px;
    }
    .stat-card.blue  .stat-icon { background: #eff6ff; color: #3b82f6; }
    .stat-card.cyan  .stat-icon { background: #ecfeff; color: #0891b2; }
    .stat-card.green .stat-icon { background: #f0fdf4; color: #16a34a; }
    .stat-card.red   .stat-icon { background: #fef2f2; color: #dc2626; }

    .stat-label { font-size: 11.5px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: var(--ink-muted); margin-bottom: 5px; }
    .stat-value { font-family: 'Sora', sans-serif; font-size: 19px; font-weight: 700; color: var(--ink); letter-spacing: -0.3px; }

    /* ── Laba bar ── */
    .laba-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--ink); border-radius: var(--r-xl);
        padding: 18px 24px; margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        animation: fadeSlideUp 0.35s 0.08s ease both;
    }
    .laba-bar-left { display: flex; align-items: center; gap: 12px; }
    .laba-bar-icon {
        width: 40px; height: 40px; background: rgba(255,255,255,0.1);
        border-radius: var(--r-md); display: flex; align-items: center;
        justify-content: center; font-size: 18px;
    }
    .laba-bar-label { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 2px; }
    .laba-bar-sub   { font-size: 11px; color: rgba(255,255,255,0.4); }
    .laba-bar-value {
        font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 700;
        color: #fff; letter-spacing: -0.5px;
    }
    .laba-bar-value.positive { color: #4ade80; }
    .laba-bar-value.negative { color: #f87171; }

    /* ── Section cards ── */
    .section-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); box-shadow: var(--shadow-sm);
        overflow: hidden; margin-bottom: 20px;
        animation: fadeSlideUp 0.4s 0.1s ease both;
    }
    .section-card-header {
        padding: 16px 22px; border-bottom: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: 10px;
    }
    .section-card-header-icon {
        width: 34px; height: 34px; border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center; font-size: 13px;
    }
    .section-card-header-icon.blue  { background: #eff6ff; color: #3b82f6; }
    .section-card-header-icon.green { background: #f0fdf4; color: #16a34a; }
    .section-card-header-icon.red   { background: #fef2f2; color: #dc2626; }
    .section-card-header h6 { font-size: 13.5px; font-weight: 700; color: var(--ink); margin: 0; }

    /* ── Data table ── */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.6px;
        text-transform: uppercase; color: var(--ink-muted);
        padding: 11px 16px; background: var(--bg);
        border-bottom: 1px solid var(--border); white-space: nowrap;
    }
    .data-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid var(--border);
        font-size: 13.5px; color: var(--ink); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.12s; }
    .data-table tbody tr:hover { background: var(--bg); }
    .data-table tfoot td, .data-table tfoot th {
        padding: 11px 16px; font-size: 13px; font-weight: 600;
        background: var(--bg); border-top: 1px solid var(--border); color: var(--ink);
    }
    .text-right { text-align: right; }
    .mono { font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 500; }

    /* ── Two-col grid ── */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .two-col { grid-template-columns: 1fr; } }

    /* ── Empty state ── */
    .empty-row td {
        text-align: center; padding: 28px !important;
        color: var(--ink-muted); font-size: 13px;
    }

    /* ── Print button ── */
    .print-btn {
        height: 36px; padding: 0 16px; border-radius: var(--r-sm);
        border: 1px solid var(--border); background: var(--surface);
        color: var(--ink-soft); font-size: 13px; font-weight: 500;
        display: inline-flex; align-items: center; gap: 7px;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .print-btn:hover { background: var(--bg); color: var(--ink); }

    /* ── Divider ── */
    .section-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 24px 0 20px; color: var(--ink-muted); font-size: 11.5px;
        font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
    }
    .section-divider::before, .section-divider::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* ── Print styles ── */
    @media print {
        .no-print { display: none !important; }
        .section-card, .laba-bar, .stats-grid { animation: none !important; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in no-print">
    <div>
        <h1>Laporan Harian</h1>
        <p>Ringkasan penjualan & arus kas tanggal {{ $date->translatedFormat('d F Y') }}</p>
    </div>
    <button onclick="window.print()" class="print-btn">
        <i class="fas fa-print" style="font-size:12px;"></i> Cetak Laporan
    </button>
</div>

{{-- Filter --}}
<div class="filter-card no-print">
    <form method="GET" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; width:100%;">
        <div class="filter-field">
            <label class="filter-label">Pilih Tanggal</label>
            <input type="date" name="date" class="form-control"
                value="{{ request('date', $date->format('Y-m-d')) }}">
        </div>
        <div class="filter-field">
            <label class="filter-label">Filter Supplier <span style="font-weight:400; opacity:.6;">(opsional)</span></label>
            <select name="supplier_id" class="form-select">
                <option value="">— Semua Supplier —</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>
                        {{ $s->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
            style="height:40px; font-size:13px; font-weight:600; border-radius:var(--r-sm); padding:0 18px; white-space:nowrap;">
            <i class="fas fa-search" style="font-size:11px;"></i> Tampilkan
        </button>
    </form>
</div>

{{-- Supplier mode banner --}}
@if($supplierId && $supplier)
<div class="supplier-mode-banner no-print">
    <span class="supplier-mode-banner-icon">🏪</span>
    <span class="supplier-mode-banner-text">
        Menampilkan laporan supplier <strong>{{ $supplier->nama_supplier }}</strong> untuk tanggal {{ $date->translatedFormat('d F Y') }}
    </span>
    <a href="{{ request()->fullUrlWithQuery(['supplier_id' => '']) }}" class="supplier-mode-banner-clear">
        ✕ Hapus Filter
    </a>
</div>
@endif

{{-- LAPORAN SUPPLIER --}}
@if($supplierId && $supplier)
    @include('reports.partials.supplier-table', [
        'supplier'     => $supplier,
        'supplierRows' => $supplierRows,
        'periodLabel'  => $date->translatedFormat('d F Y'),
    ])
    <div class="section-divider">Ringkasan Harian</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-cart-shopping"></i></div>
        <div class="stat-label">Total Penjualan</div>
        <div class="stat-value">Rp {{ number_format($salesTotal, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
        <div class="stat-label">Barang Terjual</div>
        <div class="stat-value">{{ number_format($barangTerjual, 0, ',', '.') }} <span style="font-size:13px; font-weight:500; color:var(--ink-muted);">pcs</span></div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
        <div class="stat-label">Kas Masuk</div>
        <div class="stat-value">Rp {{ number_format($kasMasuk, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
        <div class="stat-label">Kas Keluar</div>
        <div class="stat-value">Rp {{ number_format($kasKeluar, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Laba bar --}}
<div class="laba-bar">
    <div class="laba-bar-left">
        <div class="laba-bar-icon">📈</div>
        <div>
            <div class="laba-bar-label">Laba Hari Ini</div>
            <div class="laba-bar-sub">{{ $date->translatedFormat('d F Y') }}</div>
        </div>
    </div>
    <div class="laba-bar-value {{ $laba >= 0 ? 'positive' : 'negative' }}">
        Rp {{ number_format(abs($laba), 0, ',', '.') }}
        @if($laba < 0)<span style="font-size:14px; font-weight:400;">(rugi)</span>@endif
    </div>
</div>

{{-- Detail Penjualan Produk --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-header-icon blue"><i class="fas fa-chart-line"></i></div>
        <h6>Detail Penjualan Produk</h6>
    </div>
    <table class="data-table">
        <thead>
        <tr>
        <th>No.</th>
        <th>Tanggal</th>
        <th>Produk</th>
        <th class="text-right">Harga Beli</th>
        <th class="text-right">Harga Jual</th>
        <th class="text-right">Laku</th>
        <th class="text-right">Stok</th>
        <th class="text-right">Total Penjualan</th>
    </tr>
        </thead>
<tbody>
@forelse($saleItems ?? [] as $i => $item)
    @php
        $namaProduk = $item->product_name ?? ($item->product->nama_produk ?? 'Produk Tidak Diketahui');

        $jumlahTerjual = $item->total_quantity ?? ($item->laku ?? ($item->jumlah ?? 0));

        $hargaJual = $item->harga_jual
            ?? $item->harga
            ?? ($item->product->harga_jual ?? 0);

        $hargaBeli = $item->harga_beli
            ?? ($item->product->harga_beli ?? 0);

        $stokSisa = $item->stock_remaining
            ?? $item->sisa_stok
            ?? ($item->product->current_stock ?? 0);

        $tanggal = $item->created_at
            ?? $item->tanggal
            ?? now();

        $totalPenjualan = $jumlahTerjual * $hargaJual;
    @endphp

    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</td>
        <td>{{ $namaProduk }}</td>
        <td class="text-right mono">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
        <td class="text-right mono">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($jumlahTerjual, 0, ',', '.') }} pcs</td>
        <td class="text-right">{{ number_format($stokSisa, 0, ',', '.') }} pcs</td>
        <td class="text-right mono">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
    </tr>
@empty
    <tr class="empty-row">
        <td colspan="8">Tidak ada penjualan hari ini</td>
    </tr>
@endforelse
</tbody>

        {{-- FIX: Cek jika data ada menggunakan method Collection, dan perbaikan penutup tag tfoot --}}
        @if(isset($saleItems) && $saleItems->isNotEmpty())
        <tfoot>
    <tr>
        <td colspan="5" style="font-weight:600;">Total</td>

        {{-- TOTAL LAKU (kolom 6) --}}
        <td class="text-right" style="font-weight:600;">
            {{ number_format($barangTerjual, 0, ',', '.') }} pcs
        </td>

        {{-- STOK (biasanya kosong / tidak dijumlahkan) --}}
        <td class="text-right" style="font-weight:600;">
            -
        </td>

        {{-- TOTAL PENJUALAN (kolom 8) --}}
        <td class="text-right mono" style="font-weight:700;">
            Rp {{ number_format($saleItems->sum('total_amount'), 0, ',', '.') }}
        </td>
    </tr>
</tfoot>
        @endif
    </table>
{{-- Kas Masuk & Keluar --}}
<div class="two-col">
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header">
            <div class="section-card-header-icon green"><i class="fas fa-arrow-down"></i></div>
            <h6>Kas Masuk</h6>
            <span style="margin-left:auto; font-family:'Sora',sans-serif; font-size:13px; font-weight:700; color:#16a34a;">
                Rp {{ number_format($kasMasuk, 0, ',', '.') }}
            </span>
        </div>
        <table class="data-table">
            <thead><tr><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @forelse($cashIn as $cash)
                <tr>
                    <td style="font-size:13px;">{{ $cash->keterangan ?? $cash->kategori }}</td>
                    <td class="text-right mono">Rp {{ number_format($cash->jumlah, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Tidak ada kas masuk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header">
            <div class="section-card-header-icon red"><i class="fas fa-arrow-up"></i></div>
            <h6>Kas Keluar</h6>
            <span style="margin-left:auto; font-family:'Sora',sans-serif; font-size:13px; font-weight:700; color:#dc2626;">
                Rp {{ number_format($kasKeluar, 0, ',', '.') }}
            </span>
        </div>
        <table class="data-table">
            <thead><tr><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @forelse($cashOut as $cash)
                <tr>
                    <td style="font-size:13px;">{{ $cash->keterangan ?? $cash->kategori }}</td>
                    <td class="text-right mono">Rp {{ number_format($cash->jumlah, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Tidak ada kas keluar</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
