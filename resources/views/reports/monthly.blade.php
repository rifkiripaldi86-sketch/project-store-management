@extends('layouts.app')
@section('title', 'Laporan Bulanan')

@push('styles')
<style>
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .page-header h1 {
        font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700;
        color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px;
    }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

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
    .stat-card.blue::before    { background: #3b82f6; }
    .stat-card.yellow::before  { background: #f59e0b; }
    .stat-card.slate::before   { background: #64748b; }
    .stat-card.green::before   { background: #22c55e; }

    .stat-icon {
        width: 36px; height: 36px; border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; margin-bottom: 12px;
    }
    .stat-card.blue   .stat-icon { background: #eff6ff; color: #3b82f6; }
    .stat-card.yellow .stat-icon { background: #fffbeb; color: #d97706; }
    .stat-card.slate  .stat-icon { background: #f8fafc; color: #475569; }
    .stat-card.green  .stat-icon { background: #f0fdf4; color: #16a34a; }

    .stat-label { font-size: 11.5px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: var(--ink-muted); margin-bottom: 5px; }
    .stat-value { font-family: 'Sora', sans-serif; font-size: 17px; font-weight: 700; color: var(--ink); letter-spacing: -0.3px; }
    .stat-sub   { font-size: 11.5px; color: var(--ink-muted); margin-top: 3px; }

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
    .laba-bar-value { font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 700; color: #4ade80; letter-spacing: -0.5px; }
    .laba-bar-value.negative { color: #f87171; }

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
        width: 34px; height: 34px; background: var(--accent-soft); border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; color: var(--accent);
    }
    .section-card-header h6 { font-size: 13.5px; font-weight: 700; color: var(--ink); margin: 0; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.6px;
        text-transform: uppercase; color: var(--ink-muted);
        padding: 11px 16px; background: var(--bg);
        border-bottom: 1px solid var(--border);
    }
    .data-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid var(--border);
        font-size: 13.5px; color: var(--ink); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.12s; }
    .data-table tbody tr:hover { background: var(--bg); }
    .data-table tfoot td {
        padding: 12px 16px; font-size: 13px; font-weight: 700;
        background: var(--bg); border-top: 2px solid var(--border); color: var(--ink);
    }
    .text-right { text-align: right; }
    .mono { font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 500; }

    .week-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; border-radius: 50%;
        background: var(--accent-soft); color: var(--accent);
        font-size: 11px; font-weight: 700;
    }
    .profit-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 99px; font-size: 12px; font-weight: 600;
    }
    .profit-pill.pos { background: #f0fdf4; color: #16a34a; }
    .profit-pill.neg { background: #fef2f2; color: #dc2626; }

    .empty-row td { text-align:center; padding:28px !important; color:var(--ink-muted); font-size:13px; }

    .print-btn {
        height: 36px; padding: 0 16px; border-radius: var(--r-sm);
        border: 1px solid var(--border); background: var(--surface);
        color: var(--ink-soft); font-size: 13px; font-weight: 500;
        display: inline-flex; align-items: center; gap: 7px;
        cursor: pointer; transition: all 0.15s;
    }
    .print-btn:hover { background: var(--bg); color: var(--ink); }

    .section-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 24px 0 20px; color: var(--ink-muted); font-size: 11.5px;
        font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
    }
    .section-divider::before, .section-divider::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }

    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('content')

<div class="page-header animate-in no-print">
    <div>
        <h1>Laporan Bulanan</h1>
        <p>Rekap keuangan bulan {{ $month->translatedFormat('F Y') }}</p>
    </div>
    <button onclick="window.print()" class="print-btn">
        <i class="fas fa-print" style="font-size:12px;"></i> Cetak Laporan
    </button>
</div>

{{-- Filter --}}
<div class="filter-card no-print">
    <form method="GET" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; width:100%;">
        <div class="filter-field">
            <label class="filter-label">Pilih Bulan</label>
            <input type="month" name="month" class="form-control"
                value="{{ request('month', $month->format('Y-m')) }}">
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

@if($supplierId && $supplier)
<div class="supplier-mode-banner no-print">
    <span class="supplier-mode-banner-icon">🏪</span>
    <span class="supplier-mode-banner-text">
        Menampilkan laporan supplier <strong>{{ $supplier->nama_supplier }}</strong> untuk bulan {{ $month->translatedFormat('F Y') }}
    </span>
    <a href="{{ request()->fullUrlWithQuery(['supplier_id' => '']) }}" class="supplier-mode-banner-clear">
        ✕ Hapus Filter
    </a>
</div>
@endif

@if($supplierId && $supplier)
    @include('reports.partials.supplier-table', [
        'supplier'     => $supplier,
        'supplierRows' => $supplierRows,
        'periodLabel'  => $month->translatedFormat('F Y'),
    ])
    <div class="section-divider">Ringkasan Bulanan</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-cart-shopping"></i></div>
        <div class="stat-label">Total Penjualan</div>
        <div class="stat-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        <div class="stat-sub">Pendapatan bruto bulan ini</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-icon"><i class="fas fa-handshake"></i></div>
        <div class="stat-label">Bayar Supplier</div>
        <div class="stat-value">Rp {{ number_format($totalBayarSupplier, 0, ',', '.') }}</div>
        <div class="stat-sub">Total pembayaran supplier</div>
    </div>
    <div class="stat-card slate">
        <div class="stat-icon"><i class="fas fa-receipt"></i></div>
        <div class="stat-label">Pengeluaran Lain</div>
        <div class="stat-value">Rp {{ number_format($totalPengeluaranLain, 0, ',', '.') }}</div>
        <div class="stat-sub">Di luar bayar supplier</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-sack-dollar"></i></div>
        <div class="stat-label">Laba Bersih</div>
        <div class="stat-value">Rp {{ number_format($laba, 0, ',', '.') }}</div>
        <div class="stat-sub">Setelah semua pengeluaran</div>
    </div>
</div>

{{-- Laba bar --}}
<div class="laba-bar">
    <div class="laba-bar-left">
        <div class="laba-bar-icon">📊</div>
        <div>
            <div class="laba-bar-label">Laba Bersih Bulan Ini</div>
            <div class="laba-bar-sub">{{ $month->translatedFormat('F Y') }}</div>
        </div>
    </div>
    <div class="laba-bar-value {{ $laba < 0 ? 'negative' : '' }}">
        Rp {{ number_format(abs($laba), 0, ',', '.') }}
        @if($laba < 0)<span style="font-size:14px; font-weight:400;">(rugi)</span>@endif
    </div>
</div>

{{-- Tabel Mingguan --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-header-icon"><i class="fas fa-calendar-week"></i></div>
        <h6>Detail Per Minggu</h6>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Minggu ke-</th>
                <th class="text-right">Penjualan (Rp)</th>
                <th class="text-right">Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weeklyData ?? [] as $week)
            <tr>
                <td>
                    <span class="week-badge">{{ $week['week'] }}</span>
                    <span style="margin-left:8px; font-size:13px; color:var(--ink-soft);">Minggu {{ $week['week'] }}</span>
                </td>
                <td class="text-right mono">Rp {{ number_format($week['sales'], 0, ',', '.') }}</td>
                <td class="text-right">
                    <span class="profit-pill {{ $week['profit'] >= 0 ? 'pos' : 'neg' }}">
                        <i class="fas fa-{{ $week['profit'] >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}" style="font-size:10px;"></i>
                        Rp {{ number_format(abs($week['profit']), 0, ',', '.') }}
                    </span>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="3">Data mingguan tidak tersedia</td></tr>
            @endforelse
        </tbody>
        @if(!empty($weeklyData))
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right mono">Rp {{ number_format(collect($weeklyData)->sum('sales'), 0, ',', '.') }}</td>
                <td class="text-right mono">Rp {{ number_format(collect($weeklyData)->sum('profit'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

@endsection