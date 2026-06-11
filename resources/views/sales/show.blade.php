@extends('layouts.app')
@section('title', 'Detail Penjualan')

@push('styles')
<style>
    /* Header */
    .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .back-btn {
        width: 34px; height: 34px; border: 1px solid var(--border); border-radius: var(--r-sm);
        background: var(--surface); color: var(--ink-soft);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; text-decoration: none; transition: all 0.15s; flex-shrink: 0;
    }
    .back-btn:hover { background: var(--bg); color: var(--ink); }
    .page-header h1 {
        font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700;
        color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px;
    }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* Main card */
    .form-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden;
        animation: fadeSlideUp 0.4s 0.05s ease both;
    }
    .form-card-header {
        padding: 20px 28px 18px; border-bottom: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: 12px;
    }
    .form-card-header-icon {
        width: 40px; height: 40px; background: var(--accent-soft); border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: var(--accent);
    }
    .form-card-header-text h5 { font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 2px; }
    .form-card-header-text p  { font-size: 12px; color: var(--ink-muted); margin: 0; }

    /* Body */
    .form-body { padding: 26px 28px; }
    .section-title {
        font-size: 12px; font-weight: 700; letter-spacing: 0.8px;
        text-transform: uppercase; color: var(--ink-soft);
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }

    /* Info grid */
    .info-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;
    }
    @media (max-width: 520px) { .info-grid { grid-template-columns: 1fr; } }

    .info-field { display: flex; flex-direction: column; gap: 5px; }
    .info-field-label {
        font-size: 12px; font-weight: 600; letter-spacing: 0.4px;
        text-transform: uppercase; color: var(--ink-muted);
    }
    .info-field-value {
        font-size: 14px; font-weight: 500; color: var(--ink);
        padding: 9px 13px; border: 1px solid var(--border);
        border-radius: var(--r-sm); background: var(--bg);
        min-height: 40px; display: flex; align-items: center;
    }

    /* Items table */
    .items-header {
        display: grid;
        grid-template-columns: 1fr 100px 140px 130px;
        gap: 10px; padding: 0 14px 8px;
        border-bottom: 1px solid var(--border); margin-bottom: 10px;
    }
    .items-header span {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.7px;
        text-transform: uppercase; color: var(--ink-muted);
    }
    .items-header span:not(:first-child) { text-align: right; }

    .item-row {
        display: grid;
        grid-template-columns: 1fr 100px 140px 130px;
        gap: 10px; align-items: center;
        padding: 12px 14px;
        background: var(--bg); border: 1px solid var(--border);
        border-radius: var(--r-md); margin-bottom: 8px;
    }

    .item-product-name { font-size: 13.5px; font-weight: 500; color: var(--ink); }
    .item-cell { font-size: 13px; color: var(--ink-soft); text-align: right; }
    .item-cell.mono {
        font-family: 'Sora', sans-serif; font-size: 13px;
        font-weight: 500; color: var(--ink);
    }

    /* Grand total bar */
    .grand-total-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--ink); border-radius: var(--r-md);
        padding: 14px 18px; margin-top: 16px;
    }
    .grand-total-label { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.7); }
    .grand-total-value {
        font-family: 'Sora', sans-serif; font-size: 20px;
        font-weight: 700; color: #fff; letter-spacing: -0.3px;
    }
    .grand-total-count { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }

    /* Footer */
    .form-footer {
        padding: 16px 28px; border-top: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }

    @media (max-width: 640px) {
        .items-header { display: none; }
        .item-row {
            grid-template-columns: 1fr 1fr;
            grid-template-areas: "name name" "qty price" "subtotal subtotal";
        }
        .item-row > *:first-child { grid-area: name; }
        .item-cell:nth-child(2) { grid-area: qty; text-align: left; }
        .item-cell:nth-child(3) { grid-area: price; }
        .item-cell:nth-child(4) { grid-area: subtotal; font-size: 14px; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <a href="{{ route('sales.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Detail Penjualan</h1>
        <p>Transaksi #{{ $sale->id }}</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-cash-register"></i></div>
        <div class="form-card-header-text">
            <h5>Transaksi Penjualan #{{ $sale->id }}</h5>
            <p>Diinput oleh {{ $sale->createdBy->name }}
                &mdash; {{ \Carbon\Carbon::parse($sale->tanggal)->translatedFormat('d F Y') }}
            </p>
        </div>
    </div>

    <div class="form-body">

        {{-- Info Transaksi --}}
        <div class="section-title">Informasi Transaksi</div>
        <div class="info-grid">
            <div class="info-field">
                <span class="info-field-label">Tanggal</span>
                <div class="info-field-value">
                    <i class="fas fa-calendar-day" style="font-size:12px; color:var(--ink-muted); margin-right:8px;"></i>
                    {{ \Carbon\Carbon::parse($sale->tanggal)->translatedFormat('d F Y') }}
                </div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Kasir</span>
                <div class="info-field-value">
                    <i class="fas fa-user" style="font-size:12px; color:var(--ink-muted); margin-right:8px;"></i>
                    {{ $sale->createdBy->name }}
                </div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Jumlah Item</span>
                <div class="info-field-value">
                    <i class="fas fa-boxes-stacked" style="font-size:12px; color:var(--ink-muted); margin-right:8px;"></i>
                    {{ $sale->items->count() }} produk
                </div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Total Bayar</span>
                <div class="info-field-value" style="font-family:'Sora',sans-serif; font-weight:700; font-size:15px;">
                    <i class="fas fa-money-bill" style="font-size:12px; color:var(--ink-muted); margin-right:8px;"></i>
                    Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Daftar Produk --}}
        <div class="section-title">Daftar Produk Terjual</div>

        <div class="items-header d-none d-md-grid">
            <span>Produk</span>
            <span style="text-align:right;">Jumlah</span>
            <span style="text-align:right;">Harga Jual</span>
            <span style="text-align:right;">Subtotal</span>
        </div>

        @foreach($sale->items as $item)
        <div class="item-row">
            <div class="item-product-name">{{ $item->product->nama_produk }}</div>
            <div class="item-cell">
                {{ number_format($item->laku, 0, ',', '.') }}
                <small style="color:var(--ink-muted);">unit</small>
            </div>
            <div class="item-cell mono">
                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
            </div>
            <div class="item-cell mono" style="font-weight:600; color:var(--ink);">
                Rp {{ number_format($item->sub_total, 0, ',', '.') }}
            </div>
        </div>
        @endforeach

        {{-- Grand Total --}}
        <div class="grand-total-bar">
            <div>
                <div class="grand-total-label">Total Bayar</div>
                <div class="grand-total-count">{{ $sale->items->count() }} produk</div>
            </div>
            <div class="grand-total-value">
                Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}
            </div>
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('sales.index') }}" class="btn"
            style="height:38px; font-size:13.5px; border-radius:var(--r-sm); padding:0 16px;
                   border:1px solid var(--border); color:var(--ink-soft);
                   display:inline-flex; align-items:center; gap:7px;">
            <i class="fas fa-arrow-left" style="font-size:12px;"></i> Kembali
        </a>
        <span style="margin-left:auto; font-size:12px; color:var(--ink-muted);">
            ID Transaksi: <strong>#{{ $sale->id }}</strong>
        </span>
    </div>
</div>

@endsection
