@extends('layouts.app')
@section('title', 'Laporan Tahunan')

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
    .data-table tbody tr:hover { background: var(--bg); }
    .data-table tfoot td {
        padding: 12px 16px; font-size: 13px; font-weight: 700;
        background: var(--bg); border-top: 2px solid var(--border); color: var(--ink);
    }
    .text-right { text-align: right; }
    .mono { font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 500; }

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

    .chart-container { padding: 20px; }

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
        <h1>Laporan Tahunan</h1>
        <p>Rekap keuangan tahun {{ $year }}</p>
    </div>
    <button onclick="window.print()" class="print-btn">
        <i class="fas fa-print" style="font-size:12px;"></i> Cetak Laporan
    </button>
</div>

{{-- Filter --}}
<div class="filter-card no-print">
    <form method="GET" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; width:100%;">
        <div class="filter-field">
            <label class="filter-label">Pilih Tahun</label>
            <input type="number" name="year" class="form-control"
                   value="{{ $year }}" min="2020" max="{{ date('Y')+5 }}">
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
        Menampilkan laporan supplier <strong>{{ $supplier->nama_supplier }}</strong> untuk tahun {{ $year }}
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
        'periodLabel'  => 'Tahun '.$year,
    ])
    <div class="section-divider">Ringkasan Tahunan</div>
@endif

@php
    $totalPenjualan  = array_sum(array_column($monthlyData, 'penjualan'));
    $totalLaba       = array_sum(array_column($monthlyData, 'laba'));
    $totalPengeluaran = $totalPenjualan - $totalLaba;
    $rataLaba        = $totalLaba / 12;
    $bulanTerbaik    = collect($monthlyData)->sortByDesc('laba')->first();
@endphp

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-label">Total Penjualan</div>
        <div class="stat-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        <div class="stat-sub">Pendapatan bruto tahun {{ $year }}</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-icon"><i class="fas fa-arrow-trend-down"></i></div>
        <div class="stat-label">Total Pengeluaran</div>
        <div class="stat-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        <div class="stat-sub">Semua biaya (supplier, operasional, dll)</div>
    </div>
    <div class="stat-card slate">
        <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
        <div class="stat-label">Rata-rata Laba / Bulan</div>
        <div class="stat-value">Rp {{ number_format($rataLaba, 0, ',', '.') }}</div>
        <div class="stat-sub">Pendapatan bersih rata-rata</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
        <div class="stat-label">Bulan Terbaik</div>
        <div class="stat-value">{{ $bulanTerbaik['bulan'] ?? '-' }}</div>
        <div class="stat-sub">Laba: Rp {{ number_format($bulanTerbaik['laba'] ?? 0, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Laba Bar --}}
<div class="laba-bar">
    <div class="laba-bar-left">
        <div class="laba-bar-icon">📊</div>
        <div>
            <div class="laba-bar-label">Laba Bersih Tahunan</div>
            <div class="laba-bar-sub">Tahun {{ $year }}</div>
        </div>
    </div>
    <div class="laba-bar-value {{ $totalLaba < 0 ? 'negative' : '' }}">
        Rp {{ number_format(abs($totalLaba), 0, ',', '.') }}
        @if($totalLaba < 0) <span style="font-size:14px; font-weight:400;">(rugi)</span> @endif
    </div>
</div>

{{-- Grafik --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-header-icon"><i class="fas fa-chart-column"></i></div>
        <h6>Grafik Pendapatan & Laba per Bulan</h6>
    </div>
    <div class="chart-container">
        <canvas id="yearlyChart" height="200" style="max-width:100%;"></canvas>
    </div>
</div>

{{-- Tabel Bulanan --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-header-icon"><i class="fas fa-table-list"></i></div>
        <h6>Rekap Bulanan Detail</h6>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Penjualan (Rp)</th>
                <th class="text-right">Pengeluaran (Rp)</th>
                <th class="text-right">Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyData as $data)
                @php $pengeluaran = $data['penjualan'] - $data['laba']; @endphp
                <tr>
                    <td>{{ $data['bulan'] }}</td>
                    <td class="text-right mono">Rp {{ number_format($data['penjualan'], 0, ',', '.') }}</td>
                    <td class="text-right mono">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <span class="profit-pill {{ $data['laba'] >= 0 ? 'pos' : 'neg' }}">
                            <i class="fas fa-{{ $data['laba'] >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}" style="font-size:10px;"></i>
                            Rp {{ number_format(abs($data['laba']), 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="4">Data tidak tersedia</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right"><strong>Total</strong></td>
                <td class="text-right mono"><strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong></td>
                <td class="text-right mono"><strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
                <td class="text-right mono"><strong>Rp {{ number_format($totalLaba, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(array_column($monthlyData, 'bulan')),
                datasets: [
                    {
                        label: 'Penjualan (Rp)',
                        data: @json(array_column($monthlyData, 'penjualan')),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barPercentage: 0.65
                    },
                    {
                        label: 'Laba (Rp)',
                        data: @json(array_column($monthlyData, 'laba')),
                        backgroundColor: '#22c55e',
                        borderRadius: 6,
                        barPercentage: 0.65
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `Rp ${ctx.raw.toLocaleString('id-ID')}`
                        }
                    },
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10 } }
                },
                scales: {
                    y: { ticks: { callback: (val) => 'Rp ' + val.toLocaleString('id-ID') }, beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush