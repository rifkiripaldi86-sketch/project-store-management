@extends('layouts.app')
@section('title', 'Data Penjualan')

@push('styles')
<style>
    /* Header */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .page-header-left { display: flex; align-items: center; gap: 12px; }
    .page-header h1 {
        font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700;
        color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px;
    }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* Stats row */
    .stats-row {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 14px; margin-bottom: 20px;
        animation: fadeSlideUp 0.4s 0.05s ease both;
    }
    @media (max-width: 600px) { .stats-row { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); padding: 18px 20px;
        box-shadow: var(--shadow-sm);
    }
    .stat-label { font-size: 11.5px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: var(--ink-muted); margin-bottom: 6px; }
    .stat-value { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); letter-spacing: -0.3px; }
    .stat-sub   { font-size: 12px; color: var(--ink-muted); margin-top: 3px; }

    /* Main card */
    .main-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden;
        animation: fadeSlideUp 0.4s 0.1s ease both;
    }
    .main-card-header {
        padding: 16px 24px; border-bottom: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: 10px;
    }
    .main-card-header-icon {
        width: 36px; height: 36px; background: var(--accent-soft);
        border-radius: var(--r-md); display: flex; align-items: center;
        justify-content: center; font-size: 14px; color: var(--accent);
    }
    .main-card-header h5 { font-size: 14px; font-weight: 700; color: var(--ink); margin: 0; }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.7px;
        text-transform: uppercase; color: var(--ink-muted);
        padding: 11px 16px; background: var(--bg);
        border-bottom: 1px solid var(--border); white-space: nowrap;
    }
    .data-table tbody td {
        padding: 13px 16px; border-bottom: 1px solid var(--border);
        font-size: 13.5px; color: var(--ink); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.12s; }
    .data-table tbody tr:hover { background: var(--bg); }

    /* Badges & pills */
    .date-cell { font-size: 13px; color: var(--ink-soft); }
    .kasir-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 3px 10px; border-radius: 99px;
        background: var(--bg); border: 1px solid var(--border);
        font-size: 12px; color: var(--ink-soft);
    }
    .total-cell {
        font-family: 'Sora', sans-serif; font-size: 13.5px;
        font-weight: 600; color: var(--ink);
    }
    .action-btn {
        width: 32px; height: 32px; border-radius: var(--r-sm);
        border: 1px solid var(--border); background: var(--surface);
        color: var(--ink-soft); font-size: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        text-decoration: none; transition: all 0.15s;
    }
    .action-btn:hover { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 48px 24px;
    }
    .empty-state-icon {
        width: 56px; height: 56px; border-radius: var(--r-xl);
        background: var(--bg); border: 1px solid var(--border);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 22px; color: var(--ink-muted); margin-bottom: 14px;
    }
    .empty-state p { font-size: 14px; color: var(--ink-muted); margin: 0; }

    /* Pagination */
    .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--border); background: var(--bg); }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <div class="page-header-left">
        <div>
            <h1>Data Penjualan</h1>
            <p>Rekap seluruh transaksi penjualan</p>
        </div>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
        style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 18px;">
        <i class="fas fa-plus" style="font-size:11px;"></i> Tambah Penjualan
    </a>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ $sales->total() }}</div>
        <div class="stat-sub">Seluruh periode</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" style="font-size:18px;">
            Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}
        </div>
        <div class="stat-sub">Dari semua transaksi</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-rata Transaksi</div>
        <div class="stat-value" style="font-size:18px;">
            Rp {{ $sales->total() > 0 ? number_format(($totalPendapatan ?? 0) / $sales->total(), 0, ',', '.') : '0' }}
        </div>
        <div class="stat-sub">Per transaksi</div>
    </div>
</div>

{{-- Table card --}}
<div class="main-card">
    <div class="main-card-header">
        <div class="main-card-header-icon"><i class="fas fa-cash-register"></i></div>
        <h5>Daftar Penjualan</h5>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:48px;">No</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th>Kasir</th>
                    <th style="width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $index => $sale)
                <tr>
                    <td style="color:var(--ink-muted); font-size:12px;">
                        {{ $index + $sales->firstItem() }}
                    </td>
                    <td class="date-cell">
                        <i class="fas fa-calendar-day" style="font-size:11px; margin-right:6px; color:var(--ink-muted);"></i>
                        {{ \Carbon\Carbon::parse($sale->tanggal)->translatedFormat('d F Y') }}
                    </td>
                    <td class="total-cell">
                        Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="kasir-badge">
                            <i class="fas fa-user" style="font-size:10px;"></i>
                            {{ $sale->createdBy->name }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="action-btn" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:0; border:none;">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-cash-register"></i></div>
                            <p>Belum ada data penjualan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
    <div class="pagination-wrap">
        {{ $sales->links() }}
    </div>
    @endif
</div>

@endsection