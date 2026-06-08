<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Supplier - {{ $payment->supplier->nama_supplier }}</title>
    <style>
        /* ═══════════════════════════════════════════════
           THERMAL PRINT — dual-size support
           58mm  → @media print applies 58mm page width
           80mm  → default (no override needed for 80mm)
        ═══════════════════════════════════════════════ */

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm 3mm;
            background: #fff;
            color: #000;
        }

        /* ─── Responsive preview di layar ─── */
        @media screen {
            body {
                margin: 20px auto;
                border: 1px dashed #ccc;
                border-radius: 4px;
            }
            .size-toggle {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-bottom: 12px;
                font-family: sans-serif;
            }
        }

        /* ─── Print: 80mm (default) ─── */
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body   { width: 80mm; padding: 3mm 3mm; margin: 0; }
            .no-print { display: none !important; }
        }

        /* ─── Print: 58mm override (ditambah class via JS) ─── */
        body.size-58mm { width: 58mm; font-size: 9px; }
        @media print {
            body.size-58mm { @page { size: 58mm auto; margin: 0; } width: 58mm; }
        }

        /* ─── Header ─── */
        .header { text-align: center; margin-bottom: 4px; }
        .header h3 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .header p { font-size: 9px; line-height: 1.4; }

        /* ─── Dividers ─── */
        .div-solid  { border-top: 1px solid #000; margin: 4px 0; }
        .div-dashed { border-top: 1px dashed #000; margin: 4px 0; }

        /* ─── Info section ─── */
        .info { font-size: 9.5px; margin-bottom: 2px; line-height: 1.5; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        /* ─── Table ─── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        thead tr {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        tbody tr:last-child { border-bottom: 1px solid #000; }
        th, td {
            padding: 2px 1px;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
        }
        th { font-weight: bold; }
        td.nama, th.nama {
            text-align: left;
            white-space: normal;
            min-width: 20mm;
            max-width: 24mm;
            word-break: break-word;
            line-height: 1.25;
        }
        td.num, th.num { text-align: right; }

        /* 58mm: sempitkan kolom nama */
        body.size-58mm td.nama,
        body.size-58mm th.nama { min-width: 14mm; max-width: 17mm; font-size: 8px; }

        /* ─── Footer totals ─── */
        .footer { font-size: 9px; }
        .footer-row {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
        }
        .footer-row.bold-total {
            font-weight: bold;
            font-size: 11px;
        }

        /* ─── Printed line ─── */
        .printed-line { margin-top: 4px; font-size: 8.5px; }
        .printed-inner { display: flex; justify-content: space-between; }

        /* ─── Signature ─── */
        .signature {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .signature div { text-align: center; width: 33%; }
        .sig-line {
            margin-top: 14mm;
            border-top: 1px solid #000;
            padding-top: 2px;
        }

        /* ─── Buttons (screen only) ─── */
        .no-print { text-align: center; margin-top: 16px; font-family: sans-serif; }
        .btn {
            display: inline-block;
            padding: 7px 18px;
            margin: 0 4px;
            font-size: 13px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-primary   { background: #0d6efd; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-outline   {
            background: #fff; color: #333;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body id="notaBody">

    {{-- ═══ TOMBOL UKURAN (tidak ikut cetak) ═══ --}}
    <div class="no-print size-toggle">
        <button onclick="setSize(80)" class="btn btn-outline" id="btn80">80mm</button>
        <button onclick="setSize(58)" class="btn btn-outline" id="btn58">58mm</button>
    </div>

    {{-- ═══ HEADER ═══ --}}
    <div class="header">
        <h3>{{ strtoupper($storeName) }}</h3>
        <p>Nota Pembayaran Supplier</p>
    </div>

    <div class="div-solid"></div>

    {{-- ═══ INFO SUPPLIER & PERIODE ═══ --}}
    <div class="info">
        <div>Supplier : <strong>{{ $payment->supplier->nama_supplier }}</strong></div>
        <div class="info-row">
            <span>
                Periode :
                {{ \Carbon\Carbon::parse($payment->periode_awal)->format('d/m/Y') }}
                s/d
                {{ \Carbon\Carbon::parse($payment->periode_akhir)->format('d/m/Y') }}
            </span>
        </div>
        <div>Dicetak  : {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="div-solid"></div>

    {{-- ═══ TABEL DETAIL ═══ --}}
    @php
        $grandKirim         = 0;
        $grandLaku          = 0;
        $grandBayarSupplier = 0;
        $grandPendapatan    = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width:9px;">Tgl</th>
                <th class="nama">Barang</th>
                <th>Krm</th>
                <th>Laku</th>
                <th>Sisa</th>
                <th class="num">Hrg</th>
                <th class="num">Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $tanggal => $items)
                @foreach($items as $item)
                    @php
                        $grandKirim         += $item['kirim'];
                        $grandLaku          += $item['laku'];
                        $grandBayarSupplier += $item['bayar_supplier'];
                        $grandPendapatan    += $item['pendapatan'];
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tanggal)->format('d') }}</td>
                        <td class="nama">{{ $item['nama_produk'] }}</td>
                        <td>{{ $item['kirim'] }}</td>
                        <td>{{ $item['laku'] }}</td>
                        <td>{{ $item['sisa'] }}</td>
                        <td class="num">{{ number_format($item['harga_beli'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($item['bayar_supplier'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- ═══ FOOTER TOTALS ═══ --}}
    <div class="footer">
        <div class="footer-row">
            <span>Total Kirim</span>
            <span>{{ $grandKirim }} pcs</span>
        </div>
        <div class="footer-row">
            <span>Total Laku</span>
            <span>{{ $grandLaku }} pcs</span>
        </div>
        <div class="footer-row">
            <span>Total Sisa / Retur</span>
            <span>{{ $grandKirim - $grandLaku }} pcs</span>
        </div>

        <div class="div-dashed"></div>

        <div class="footer-row bold-total">
            <span>TOTAL BAYAR SUPPLIER</span>
            <span>Rp {{ number_format($grandBayarSupplier, 0, ',', '.') }}</span>
        </div>

        <div class="div-solid"></div>

        <div class="printed-line">
            <div class="printed-inner">
                <span>Total laku: {{ $grandLaku }} pcs</span>
                <span>Total: <strong>{{ number_format($grandBayarSupplier, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    {{-- ═══ TANDA TANGAN ═══ --}}
    <div class="signature">
        <div>
            Penerima,
            <div class="sig-line">(.................)</div>
        </div>
        <div>
            Hormat Kami,
            <div class="sig-line">(.................)</div>
        </div>
    </div>

    {{-- ═══ TOMBOL AKSI (tidak ikut cetak) ═══ --}}
    <div class="no-print" style="margin-top:20px;">
        <button onclick="window.print()" class="btn btn-primary">&#128438; Cetak Nota</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Tutup</a>
    </div>

</body>
<script>
    // Pilih ukuran thermal (58mm / 80mm) sebelum cetak
    function setSize(mm) {
        const body = document.getElementById('notaBody');
        body.classList.toggle('size-58mm', mm === 58);
        // Update tombol aktif
        document.getElementById('btn58').style.background = mm === 58 ? '#0d6efd' : '';
        document.getElementById('btn58').style.color      = mm === 58 ? '#fff'    : '';
        document.getElementById('btn80').style.background = mm === 80 ? '#0d6efd' : '';
        document.getElementById('btn80').style.color      = mm === 80 ? '#fff'    : '';
        // Override @page size di runtime via injected style
        let styleTag = document.getElementById('dynamic-page-size');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'dynamic-page-size';
            document.head.appendChild(styleTag);
        }
        styleTag.textContent = `@media print { @page { size: ${mm}mm auto; margin: 0; } body { width: ${mm}mm; } }`;
    }

    // Default: 80mm aktif
    setSize(80);

    // Auto-print saat halaman pertama kali dibuka
    window.addEventListener('load', () => window.print());
</script>
</html>
