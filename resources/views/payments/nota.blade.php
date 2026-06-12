<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Supplier - {{ $payment->supplier->nama_supplier }}</title>
<style>
    /* ─── Base Reset ─── */
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

    /* ─── Responsive preview (hanya di layar) ─── */
    @media screen {
        body { margin: 20px auto; border: 1px dashed #ccc; border-radius: 4px; }
        .size-toggle { display: flex; justify-content: center; gap: 8px; margin-bottom: 12px; font-family: sans-serif; }
    }

    /* ─── Print 80mm (Default) ─── */
    @media print {
        @page { size: 80mm auto; margin: 0; }
        body { width: 80mm; padding: 3mm; }
        .no-print { display: none !important; }
    }

    /* ─── Print 58mm Override ─── */
    body.size-58mm { width: 58mm; font-size: 8px; }
    @media print {
        body.size-58mm { @page { size: 58mm auto; margin: 0; } width: 58mm; }
    }

    /* ─── Layout Tabel (Kunci Perbaikan) ─── */
    table {
        width: 100%;
        table-layout: fixed; /* Memaksa tabel tidak melar keluar */
        border-collapse: collapse;
        font-size: 8.5px;
        margin: 4px 0;
    }

    th, td {
        padding: 2px 1px;
        text-align: center;
        overflow-wrap: break-word; /* Memecah kata panjang */
        word-wrap: break-word;
    }

    /* Porsi Kolom: Nama Produk dapat ruang paling besar */
    .nama { width: 35%; text-align: left; }
    th:not(.nama), td:not(.nama) { width: 13%; }

    thead tr { border-top: 1px solid #000; border-bottom: 1px solid #000; }
    tbody tr:last-child { border-bottom: 1px solid #000; }
    .num { text-align: right; }

    /* ─── Header & Info ─── */
    .header { text-align: center; margin-bottom: 4px; }
    .header h3 { font-size: 13px; font-weight: bold; text-transform: uppercase; }
    .info { font-size: 9px; line-height: 1.4; }
    .div-solid { border-top: 1px solid #000; margin: 4px 0; }
    .div-dashed { border-top: 1px dashed #000; margin: 4px 0; }

    /* ─── Footer ─── */
    .footer { font-size: 9px; }
    .footer-row { display: flex; justify-content: space-between; margin: 1px 0; }
    .bold-total { font-weight: bold; font-size: 11px; margin-top: 2px; }

    /* ─── Signature & Others ─── */
    .signature { margin-top: 10px; display: flex; justify-content: space-between; font-size: 9px; }
    .sig-line { margin-top: 12mm; border-top: 1px solid #000; }

    /* ─── Buttons ─── */
    .no-print { text-align: center; margin-top: 16px; font-family: sans-serif; }
    .btn { padding: 7px 18px; border-radius: 5px; cursor: pointer; border: none; }
    .btn-primary { background: #0d6efd; color: #fff; }
    .btn-outline { background: #fff; border: 1px solid #ccc; }
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
                <span class="tgl-sistem" data-date="{{ \Carbon\Carbon::parse($payment->periode_awal)->toDateString() }}"></span>
                s/d
                <span class="tgl-sistem" data-date="{{ \Carbon\Carbon::parse($payment->periode_akhir)->toDateString() }}"></span>
            </span>
        </div>
        <div>Dicetak  : <span class="tgl-sistem" data-datetime="{{ now()->toIso8601String() }}"></span></div>
    </div>

    <div class="div-solid"></div>

    {{-- ═══ TABEL DETAIL PENJUALAN ═══ --}}
    @php
        $grandKirim = 0;
        $grandLaku = 0;
        $grandBayarSupplier = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th>Tgl</th>
                <th class="nama">Barang</th>
                <th>Krm</th>
                <th>Laku</th>
                <th>Sisa</th>
                <th class="num">Hrg</th>
                <th class="num">Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedDetails as $tanggal => $items)
                @foreach($items as $row)
                    @php
                        // Akumulasi perhitungan
                        $grandKirim += $row['kirim'];
                        $grandLaku += $row['laku'];
                        $grandBayarSupplier += $row['bayar_supplier'];
                    @endphp
                    <tr>
                        <td><span class="tgl-sistem-short" data-date="{{ $tanggal }}"></span></td>
                        <td class="nama">{{ $row['nama_produk'] }}</td>
                        <td>{{ $row['kirim'] }}</td>
                        <td>{{ $row['laku'] }}</td>
                        <td>{{ $row['sisa'] }}</td>
                        <td class="num">{{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($row['bayar_supplier'], 0, ',', '.') }}</td>
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
    // ─── Format tanggal mengikuti locale/sistem komputer user ───
    document.querySelectorAll('.tgl-sistem').forEach(el => {
        if (el.dataset.date) {
            const d = new Date(el.dataset.date + 'T00:00:00');
            el.textContent = d.toLocaleDateString();
        } else if (el.dataset.datetime) {
            const d = new Date(el.dataset.datetime);
            el.textContent = d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    });

    // Versi singkat untuk kolom tabel (tgl/bln saja, ikut urutan locale)
    document.querySelectorAll('.tgl-sistem-short').forEach(el => {
        const d = new Date(el.dataset.date + 'T00:00:00');
        el.textContent = d.toLocaleDateString(undefined, { day: '2-digit', month: '2-digit' });
    });

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
