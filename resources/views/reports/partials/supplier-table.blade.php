{{--
    Partial: reports/partials/supplier-table.blade.php
    Include di daily, monthly, yearly dengan:
        @include('reports.partials.supplier-table', [
            'supplier'     => $supplier,
            'supplierRows' => $supplierRows,
            'periodLabel'  => '23 Mei 2026',  // string bebas
        ])
--}}

<div class="supplier-report-wrap">

    {{-- ── Header seksi ── --}}
    <div class="sr-header">
        <div class="sr-header-left">
            <div class="sr-header-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <div>
                <div class="sr-title">Laporan Supplier</div>
                <div class="sr-sub">{{ $supplier->nama_supplier }} &mdash; {{ $periodLabel }}</div>
            </div>
        </div>

        @if(count($supplierRows) > 0)
        <div class="sr-summary-pills">
            <div class="sr-pill sr-pill-blue">
                <span class="sr-pill-label">Total Penjualan</span>
                <span class="sr-pill-value">Rp {{ number_format(array_sum(array_column($supplierRows, 'penjualan')), 0, ',', '.') }}</span>
            </div>
            <div class="sr-pill sr-pill-green">
                <span class="sr-pill-label">Total Laba</span>
                <span class="sr-pill-value">Rp {{ number_format(array_sum(array_column($supplierRows, 'laba')), 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Tabel ── --}}
    <div class="sr-table-wrap">
        <table class="sr-table">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-tgl">Tgl</th>
                    <th class="col-produk">Produk</th>
                    <th class="col-num">Harga Beli</th>
                    <th class="col-num">Harga Jual</th>
                    <th class="col-num">Stok</th>
                    <th class="col-num">Laku</th>
                    <th class="col-num">Sisa</th>
                    <th class="col-num">Penjualan</th>
                    <th class="col-num">Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplierRows as $row)
                <tr>
                    <td class="col-no">{{ $row['no'] }}</td>
                    <td class="col-tgl">{{ $row['tanggal'] }}</td>
                    <td class="col-produk">{{ $row['produk'] }}</td>
                    <td class="col-num">{{ number_format($row['hargaBeli'], 0, ',', '.') }}</td>
                    <td class="col-num">{{ number_format($row['hargaJual'], 0, ',', '.') }}</td>
                    <td class="col-num">{{ number_format($row['stok'], 0, ',', '.') }}</td>
                    <td class="col-num">{{ number_format($row['laku'], 0, ',', '.') }}</td>
                    <td class="col-num {{ $row['sisa'] > 0 ? 'sisa-ada' : '' }}">{{ number_format($row['sisa'], 0, ',', '.') }}</td>
                    <td class="col-num mono-val">Rp {{ number_format($row['penjualan'], 0, ',', '.') }}</td>
                    <td class="col-num mono-val {{ $row['laba'] >= 0 ? 'laba-pos' : 'laba-neg' }}">
                        Rp {{ number_format(abs($row['laba']), 0, ',', '.') }}
                        @if($row['laba'] < 0)<span class="rugi-tag">rugi</span>@endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="sr-empty">
                        <div class="sr-empty-inner">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.35; margin-bottom:6px;">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                            <div>Tidak ada data pengiriman dari supplier ini pada periode yang dipilih</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

            @if(count($supplierRows) > 0)
            <tfoot>
                <tr>
                    <td colspan="3" class="tfoot-label">Total</td>
                    <td class="col-num tfoot-val">—</td>
                    <td class="col-num tfoot-val">—</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'stok')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'laku')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'sisa')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val mono-val">Rp {{ number_format(array_sum(array_column($supplierRows, 'penjualan')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val mono-val laba-pos">Rp {{ number_format(array_sum(array_column($supplierRows, 'laba')), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
/* ── Wrapper ── */
.supplier-report-wrap {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 20px;
    animation: fadeSlideUp 0.4s 0.12s ease both;
}

/* ── Header ── */
.sr-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
}
.sr-header-left { display: flex; align-items: center; gap: 12px; }
.sr-header-icon {
    width: 36px; height: 36px;
    background: #eff6ff; color: #3b82f6;
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sr-title { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.sr-sub   { font-size: 11.5px; color: var(--ink-muted); margin-top: 1px; }

/* ── Summary pills ── */
.sr-summary-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.sr-pill {
    display: flex; flex-direction: column;
    padding: 7px 14px; border-radius: var(--r-md);
    min-width: 130px;
}
.sr-pill-blue  { background: #eff6ff; }
.sr-pill-green { background: #f0fdf4; }
.sr-pill-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: .6; margin-bottom: 2px; }
.sr-pill-blue  .sr-pill-label { color: #1d4ed8; }
.sr-pill-green .sr-pill-label { color: #15803d; }
.sr-pill-value { font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700; }
.sr-pill-blue  .sr-pill-value { color: #1d4ed8; }
.sr-pill-green .sr-pill-value { color: #15803d; }

/* ── Table scroll wrap ── */
.sr-table-wrap { overflow-x: auto; }

/* ── Table ── */
.sr-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 780px;
}
.sr-table thead th {
    font-size: 10.5px; font-weight: 700; letter-spacing: 0.6px;
    text-transform: uppercase; color: var(--ink-muted);
    padding: 10px 14px; background: var(--bg);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.sr-table thead th.col-num { text-align: right; }
.sr-table tbody td {
    padding: 11px 14px;
    border-bottom: 1px solid var(--border);
    font-size: 13px; color: var(--ink);
    vertical-align: middle;
}
.sr-table tbody tr:last-child td { border-bottom: none; }
.sr-table tbody tr { transition: background 0.12s; }
.sr-table tbody tr:hover { background: var(--bg); }

/* Column sizing */
.col-no    { width: 42px; text-align: center; color: var(--ink-muted); font-size: 12px; }
.col-tgl   { white-space: nowrap; font-size: 12.5px; color: var(--ink-soft); }
.col-produk{ font-weight: 600; }
.col-num   { text-align: right; }

/* Special cell styles */
.mono-val  { font-family: 'Sora', sans-serif; font-size: 12.5px; font-weight: 500; }
.laba-pos  { color: #16a34a; font-weight: 600; }
.laba-neg  { color: #dc2626; font-weight: 600; }
.sisa-ada  { color: #d97706; font-weight: 500; }
.rugi-tag  {
    display: inline-block; margin-left: 4px;
    font-size: 9px; background: #fee2e2; color: #dc2626;
    padding: 1px 5px; border-radius: 4px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.3px;
}

/* Footer */
.sr-table tfoot td {
    padding: 11px 14px;
    border-top: 2px solid var(--border);
    background: var(--bg);
    font-size: 13px;
}
.tfoot-label { font-weight: 700; color: var(--ink); }
.tfoot-val   { font-weight: 700; color: var(--ink); text-align: right; }

/* Empty state */
.sr-empty { padding: 32px 16px !important; }
.sr-empty-inner {
    display: flex; flex-direction: column; align-items: center;
    text-align: center; gap: 4px;
    color: var(--ink-muted); font-size: 13px;
}

@media (max-width: 640px) {
    .sr-header { flex-direction: column; align-items: flex-start; }
    .sr-summary-pills { width: 100%; }
    .sr-pill { flex: 1; min-width: 0; }
}
</style>