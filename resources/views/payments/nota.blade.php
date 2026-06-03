<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Supplier - {{ $payment->supplier->nama_supplier }}</title>
    <style>
        /* ═══════════════════════════════════
           PRINT: 80mm thermal roll
        ═══════════════════════════════════ */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm 3mm;
            background: #fff;
            color: #000;
        }

        /* ─── Header ─── */
        .header { margin-bottom: 5px; }
        .header h3 {
            margin: 0 0 2px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p { margin: 1px 0; font-size: 9px; }

        /* ─── Divider ─── */
        .div-solid  { border-top: 1px solid #000; margin: 4px 0; }
        .div-dashed { border-top: 1px dashed #000; margin: 4px 0; }

        /* ─── Info section ─── */
        .info { font-size: 10px; margin-bottom: 2px; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }
        .info strong { font-size: 10px; }

        /* ─── Table ─── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
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

        /* Nama kolom: rata kiri, bisa wrap */
        td.nama, th.nama {
            text-align: left;
            white-space: normal;
            min-width: 22mm;
            max-width: 26mm;
            word-break: break-word;
            line-height: 1.25;
        }

        /* Angka: rata kanan */
        td.num, th.num { text-align: right; }

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

        /* Printed line */
        .printed-line {
            margin-top: 5px;
            font-size: 9px;
        }
        .printed-inner {
            display: flex;
            justify-content: space-between;
        }

        /* ─── Signature ─── */
        .signature {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .signature div {
            text-align: center;
            width: 33mm;
        }
        .sig-line {
            margin-top: 16mm;
            border-top: 1px solid #000;
            padding-top: 2px;
        }

        /* ─── No-print buttons ─── */
        .no-print {
            text-align: center;
            margin-top: 18px;
        }
        .btn {
            display: inline-block;
            padding: 6px 16px;
            margin: 0 4px;
            font-size: 13px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: sans-serif;
        }
        .btn-primary   { background: #0d6efd; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
    </style>
</head>
<body onload="window.print()">

    {{-- ═══ HEADER ═══ --}}
    <div class="header">
        <h3>TOKO KUE SARI REZEKI</h3>
        <p>Jl. Cipageran No.136 Cimahi</p>
        <p>Telp : 089655763820</p>
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
            <span>Hal: 1</span>
        </div>
    </div>

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
                <th class="nama">Nama Barang</th>
                <th>Krm</th>
                <th>Laku</th>
                <th>Sisa</th>
                <th class="num">Harga</th>
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
                        $grandPendapatan    += $item['pendapatan_jual'];
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

        {{-- Printed line: mirip gambar referensi --}}
        <div class="printed-line">
            <div class="printed-inner">
                <span>Printed : {{ now()->format('d/m/Y H:i:s') }}</span>
                <span>{{ $grandLaku }} &nbsp; Total : <strong>{{ number_format($grandBayarSupplier, 0, ',', '.') }}</strong></span>
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

    {{-- ═══ TOMBOL (tidak ikut cetak) ═══ --}}
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Nota
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Tutup</a>
    </div>

</body>
</html>