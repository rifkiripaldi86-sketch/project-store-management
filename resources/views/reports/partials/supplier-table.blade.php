<div class="supplier-report-wrap">
    {{-- ── Header ── --}}
    <div class="sr-header">
        <div class="sr-header-left">
            <div class="sr-header-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
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
        </div>
        @endif
    </div>

    {{-- ── Tabel ── --}}
    <div class="sr-table-wrap">
        <table class="sr-table">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-tgl">Tgl.</th>
                    <th class="col-produk">Produk</th>
                    <th class="col-num">Harga Beli</th>
                    <th class="col-num">Stok</th>
                    <th class="col-num">Laku</th>
                    <th class="col-num">BS</th>
                    <th class="col-num">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplierRows as $row)
                <tr>
                    <td class="col-no">{{ $row['no'] }}</td>
                    <td class="col-tgl">{{ $row['tanggal'] }}</td>
                    <td class="col-produk">{{ $row['produk'] }}</td>
                    <td class="col-num">{{ number_format($row['hargaBeli'], 0, ',', '.') }}</td>
                    <td class="col-num">{{ number_format($row['stok'], 0, ',', '.') }}</td>
                    <td class="col-num">{{ number_format($row['laku'], 0, ',', '.') }}</td>
                    <td class="col-num {{ $row['sisa'] > 0 ? 'sisa-ada' : '' }}">{{ number_format($row['sisa'], 0, ',', '.') }}</td>
                    <td class="col-num mono-val">Rp {{ number_format($row['penjualan'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="sr-empty">Tidak ada data untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($supplierRows) > 0)
            <tfoot>
                <tr>
                    <td colspan="3" class="tfoot-label">Total</td>
                    <td class="col-num">—</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'stok')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'laku')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val">{{ number_format(array_sum(array_column($supplierRows, 'sisa')), 0, ',', '.') }}</td>
                    <td class="col-num tfoot-val mono-val">Rp {{ number_format(array_sum(array_column($supplierRows, 'penjualan')), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
    /* CSS disesuaikan untuk 8 kolom */
    .sr-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .sr-table th, .sr-table td { padding: 12px 14px; border-bottom: 1px solid var(--border); }
    .col-no { width: 40px; text-align: center; }
    .col-num { text-align: right; }
    .col-produk { font-weight: 600; }
    .mono-val { font-family: monospace; font-weight: 600; }
    .tfoot-label { font-weight: 700; }
    .tfoot-val { font-weight: 700; }

    @media print {
        .sr-table { font-size: 10px !important; }
        .sr-summary-pills { display: none !important; }
        .sr-table-wrap { overflow: visible !important; }
    }
</style>
