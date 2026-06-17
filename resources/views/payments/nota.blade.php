<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Supplier - {{ $payment->supplier->nama_supplier }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        width: 80mm;
        margin: 0 auto;
        padding: 5mm 4mm;
        background: #fff;
        color: #000;
        line-height: 1.45;
    }

    @media screen {
        body {
            margin: 30px auto;
            border: 1px dashed #ccc;
            border-radius: 6px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
    }

    @media print {
        @page { size: 80mm auto; margin: 0; }
        body { width: 80mm; padding: 3mm; }
        .no-print { display: none !important; }
    }

    /* ═══════════════════════════════════════
       58mm override — kertas cetak ~48mm
       ═══════════════════════════════════════ */
    body.size-58mm {
        width: 58mm;
        font-size: 8px;
        padding: 1.5mm 1.5mm;
        line-height: 1.3;
    }

    /* Header */
    body.size-58mm .header h3 {
        font-size: 11px;
        letter-spacing: 0.3px;
    }
    body.size-58mm .header .sub {
        font-size: 7px;
        letter-spacing: 0;
    }

    /* Info rows: singkat label, perkecil font */
    body.size-58mm .info-section { margin: 2px 0; }
    body.size-58mm .info-row {
        font-size: 7.5px;
        line-height: 1.4;
    }
    body.size-58mm .info-row .label {
        font-size: 7px;
        min-width: 0;
    }
    body.size-58mm .info-row .value {
        font-size: 7.5px;
    }

    /* Table: 58mm harus lebih kecil */
    body.size-58mm table { font-size: 7px; }
    body.size-58mm th { font-size: 6.5px; padding: 2px 1px; }
    body.size-58mm td { padding: 1px 1px; font-size: 7px; }
    body.size-58mm .col-nama  { width: 30%; }
    body.size-58mm .col-stok  { width: 11%; }
    body.size-58mm .col-laku  { width: 11%; }
    body.size-58mm .col-hrg   { width: 22%; }
    body.size-58mm .col-bayar { width: 26%; }

    /* Date header */
    body.size-58mm .date-header {
        font-size: 7px;
        padding: 1px 2px;
        margin: 2px 0 1px;
    }

    /* Footer & totals */
    body.size-58mm .footer-line { font-size: 7.5px; line-height: 1.5; }
    body.size-58mm .total-box { margin: 3px 0; padding: 3px 0; }
    body.size-58mm .total-line { font-size: 10px; }

    /* Signature */
    body.size-58mm .signature { margin-top: 8px; }
    body.size-58mm .sig-block { font-size: 7px; }
    body.size-58mm .sig-label { font-size: 7px; }
    body.size-58mm .sig-line {
        width: 17mm;
        margin-top: 7mm;
        font-size: 6px;
    }

    /* Notes & misc */
    body.size-58mm .note-section { font-size: 6.5px; }
    body.size-58mm .thank-you { font-size: 7px; margin-top: 5px; }
    body.size-58mm .div-double,
    body.size-58mm .div-solid,
    body.size-58mm .div-dashed { margin: 2px 0; }

    @media print {
        body.size-58mm { width: 58mm; padding: 1.5mm; }
    }

    /* ─── Header ─── */
    .header {
        text-align: center;
        padding-bottom: 4px;
    }
    .header h3 {
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }
    .header .sub {
        font-size: 9px;
        letter-spacing: 0.3px;
    }

    /* ─── Dividers ─── */
    .div-double {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        height: 3px;
        margin: 4px 0;
    }
    .div-solid {
        border-top: 1px solid #000;
        margin: 4px 0;
    }
    .div-dashed {
        border-top: 1px dashed #000;
        margin: 4px 0;
    }
    .div-dots {
        text-align: center;
        font-size: 8px;
        letter-spacing: 2px;
        color: #666;
        margin: 3px 0;
        overflow: hidden;
        white-space: nowrap;
    }

    /* ─── Info rows ─── */
    .info-section {
        margin: 4px 0;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        line-height: 1.6;
        gap: 2px;
    }
    .info-row .label {
        color: #333;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .info-row .value {
        font-weight: bold;
        text-align: right;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ─── Date group header ─── */
    .date-header {
        font-size: 9px;
        font-weight: bold;
        background: #000;
        color: #fff;
        padding: 2px 4px;
        margin: 4px 0 1px;
        letter-spacing: 0.3px;
    }

    /* ─── Table ─── */
    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 9px;
        margin: 0;
    }

    th, td {
        padding: 2px 1px;
        vertical-align: top;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Column widths — 5 columns */
    .col-nama  { width: 30%; text-align: left; }
    .col-stok  { width: 12%; text-align: center; }
    .col-laku  { width: 12%; text-align: center; }
    .col-hrg   { width: 22%; text-align: right; }
    .col-bayar { width: 24%; text-align: right; }

    thead tr {
        border-bottom: 1px solid #000;
    }
    tbody tr {
        border-bottom: 1px dotted #ccc;
    }
    tbody tr:last-child {
        border-bottom: none;
    }

    th {
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 3px 1px;
        letter-spacing: 0.3px;
    }

    td.col-nama {
        white-space: normal;
        word-break: break-word;
        line-height: 1.3;
    }

    .row-warning td {
        /* Visual indicator for laku > stok */
    }
    .sisa-note {
        font-size: 7px;
        color: #666;
        display: inline;
    }

    /* ─── Footer totals ─── */
    .footer-section {
        margin: 4px 0;
    }
    .footer-line {
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        line-height: 1.7;
    }
    .footer-line .label {
        color: #333;
        flex-shrink: 0;
    }
    .footer-line .value { font-weight: bold; }

    .total-box {
        margin: 5px 0;
        padding: 4px 0;
        border-top: 1px solid #000;
        border-bottom: 1px double #000;
    }
    .total-line {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0.3px;
    }

    /* ─── Notes ─── */
    .note-section {
        font-size: 7.5px;
        color: #555;
        margin: 3px 0;
        line-height: 1.5;
    }

    /* ─── Signature ─── */
    .signature {
        margin-top: 12px;
        display: flex;
        justify-content: space-between;
        text-align: center;
    }
    .sig-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 8.5px;
    }
    .sig-label {
        font-weight: bold;
        margin-bottom: 1px;
    }
    .sig-line {
        margin-top: 12mm;
        border-top: 1px solid #000;
        width: 24mm;
        padding-top: 2px;
        font-size: 8px;
    }

    /* ─── Thank you ─── */
    .thank-you {
        text-align: center;
        font-size: 8px;
        color: #666;
        margin-top: 8px;
        letter-spacing: 0.3px;
    }

    /* ─── Buttons (no print) ─── */
    .no-print {
        text-align: center;
        margin-top: 16px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .btn {
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        border: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary {
        background: #1d4ed8;
        color: #fff;
    }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline {
        background: #fff;
        border: 1px solid #d1d5db;
        color: #374151;
    }
    .btn-outline:hover { background: #f9fafb; }
    .btn-outline.active {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
    }
    .btn-secondary {
        background: #6b7280;
        color: #fff;
        margin-left: 8px;
    }
    .btn-secondary:hover { background: #4b5563; }

    .size-toggle {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 14px;
    }
</style>
</head>
<body id="notaBody">

    {{-- Tombol ukuran (tidak cetak) --}}
    <div class="no-print size-toggle">
        <button onclick="setSize(80)" class="btn btn-outline" id="btn80">📄 80mm</button>
        <button onclick="setSize(58)" class="btn btn-outline active" id="btn58">🧾 58mm</button>
    </div>

    {{-- ═══ Header ═══ --}}
    <div class="header">
        <h3>{{ strtoupper($storeName) }}</h3>
        <div class="sub">NOTA PEMBAYARAN SUPPLIER</div>
    </div>

    <div class="div-double"></div>

    {{-- ═══ Info ═══ --}}
    <div class="info-section">
        <div class="info-row">
            <span class="label">No.Nota</span>
            <span class="value">#{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Supplier</span>
            <span class="value">{{ $payment->supplier->nama_supplier }}</span>
        </div>
        <div class="info-row">
            <span class="label">Periode</span>
            <span class="value">
                <span class="tgl-short" data-date="{{ \Carbon\Carbon::parse($payment->periode_awal)->toDateString() }}"></span>
                -
                <span class="tgl-short" data-date="{{ \Carbon\Carbon::parse($payment->periode_akhir)->toDateString() }}"></span>
            </span>
        </div>
        <div class="info-row">
            <span class="label">Cetak</span>
            <span class="value"><span class="tgl-sys-dt" data-dt="{{ now()->toIso8601String() }}"></span></span>
        </div>
    </div>

    <div class="div-solid"></div>

    {{-- ═══ Tabel detail ═══ --}}
    @php
        $grandStok        = 0;
        $grandLaku        = 0;
        $grandSisa        = 0;
        $grandBayar       = 0;
        $adaLakuMelebihi  = false;
        $itemCount        = 0;
    @endphp

    @foreach($details as $tanggal => $items)
        {{-- Date group header --}}
        <div class="date-header">
            <span class="tgl-long" data-date="{{ $tanggal }}"></span>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-nama">Barang</th>
                    <th class="col-stok">Stok</th>
                    <th class="col-laku">Jual</th>
                    <th class="col-hrg">Hrg</th>
                    <th class="col-bayar">Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                    @php
                        $grandStok  += $row['stok'];
                        $grandLaku  += $row['laku'];
                        $grandSisa  += $row['sisa'];
                        $grandBayar += $row['bayar_supplier'];
                        $itemCount++;
                        if ($row['laku'] > $row['stok']) { $adaLakuMelebihi = true; }
                    @endphp
                    <tr @if($row['laku'] > $row['stok']) class="row-warning" @endif>
                        <td class="col-nama">{{ $row['nama_produk'] }}</td>
                        <td class="col-stok">{{ $row['stok'] }}</td>
                        <td class="col-laku">
                            {{ $row['laku'] }}
                            @if($row['laku'] > $row['stok'])
                                <span class="sisa-note">*</span>
                            @endif
                        </td>
                        <td class="col-hrg">{{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td class="col-bayar">{{ number_format($row['bayar_supplier'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="div-solid"></div>

    {{-- ═══ Keterangan ═══ --}}
    @if($adaLakuMelebihi)
    <div class="note-section">
        * Laku melebihi stok kiriman.<br>
        &nbsp; Sisa = 0, bayar dihitung dari<br>
        &nbsp; jumlah kiriman saja.
    </div>
    <div class="div-dashed"></div>
    @endif

    {{-- ═══ Ringkasan ═══ --}}
    <div class="footer-section">
        <div class="footer-line">
            <span class="label">Jml Item</span>
            <span class="value">{{ $itemCount }} brg</span>
        </div>
        <div class="footer-line">
            <span class="label">Ttl Stok</span>
            <span class="value">{{ $grandStok }} pcs</span>
        </div>
        <div class="footer-line">
            <span class="label">Ttl Laku</span>
            <span class="value">{{ $grandLaku }} pcs</span>
        </div>
        <div class="footer-line">
            <span class="label">Ttl Sisa</span>
            <span class="value">{{ $grandSisa }} pcs</span>
        </div>
    </div>

    {{-- ═══ Grand Total ═══ --}}
    <div class="total-box">
        <div class="total-line">
            <span>TOTAL</span>
            <span>Rp {{ number_format($grandBayar, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ═══ Tanda tangan ═══ --}}
    <div class="signature">
        <div class="sig-block">
            <span class="sig-label">Penerima,</span>
            <div class="sig-line">(...................)</div>
        </div>
        <div class="sig-block">
            <span class="sig-label">Hormat Kami,</span>
            <div class="sig-line">(...................)</div>
        </div>
    </div>

    {{-- ═══ Thank you ═══ --}}
    <div class="thank-you">
        — Terima Kasih —
    </div>

    {{-- Tombol aksi (tidak cetak) --}}
    <div class="no-print" style="margin-top:20px;">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Nota</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">✕ Tutup</a>
    </div>

</body>
<script>
    // Format tanggal pendek (d/m/yy) — hemat ruang di 58mm
    document.querySelectorAll('.tgl-short').forEach(el => {
        const d = new Date(el.dataset.date + 'T00:00:00');
        const dd = d.getDate();
        const mm = d.getMonth() + 1;
        const yy = d.getFullYear();
        el.textContent = dd + '/' + mm + '/' + yy;
    });

    // Format tanggal + jam
    document.querySelectorAll('.tgl-sys-dt').forEach(el => {
        const d = new Date(el.dataset.dt);
        const dd = d.getDate();
        const mm = d.getMonth() + 1;
        const yy = d.getFullYear();
        const hh = String(d.getHours()).padStart(2, '0');
        const mi = String(d.getMinutes()).padStart(2, '0');
        el.textContent = dd + '/' + mm + '/' + yy + ' ' + hh + ':' + mi;
    });

    // Format tanggal panjang di date group header
    document.querySelectorAll('.tgl-long').forEach(el => {
        const d = new Date(el.dataset.date + 'T00:00:00');
        const dd = d.getDate();
        const mm = d.getMonth() + 1;
        const yy = d.getFullYear();
        el.textContent = dd + '/' + mm + '/' + yy;
    });

    // Pilih ukuran thermal
    function setSize(mm) {
        const body = document.getElementById('notaBody');
        body.classList.toggle('size-58mm', mm === 58);

        document.getElementById('btn58').classList.toggle('active', mm === 58);
        document.getElementById('btn80').classList.toggle('active', mm === 80);

        let s = document.getElementById('dps');
        if (!s) { s = document.createElement('style'); s.id = 'dps'; document.head.appendChild(s); }
        s.textContent = `@media print { @page { size: ${mm}mm auto; margin:0; } body { width:${mm}mm; padding:1.5mm; } }`;
    }

    // Default: 58mm (sesuai printer thermal user)
    setSize(58);
    window.addEventListener('load', () => window.print());
</script>
</html>
