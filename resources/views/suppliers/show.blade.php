@extends('layouts.app')
@section('title', 'Detail Supplier')

@push('styles')
<style>
    /* ─── Page Header ─────────────────────────────────── */
    .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .back-btn { width: 34px; height: 34px; border: 1px solid var(--border); border-radius: var(--r-sm); background: var(--surface); color: var(--ink-soft); display: flex; align-items: center; justify-content: center; font-size: 13px; text-decoration: none; transition: all 0.15s; flex-shrink: 0; }
    .back-btn:hover { background: var(--bg); color: var(--ink); }
    .page-header h1 { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px; }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* ─── Form Card (shared) ──────────────────────────── */
    .form-card { max-width: 860px; margin: 0 auto 24px auto; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeSlideUp 0.4s ease both; }
    .form-card-header { padding: 20px 28px 18px; border-bottom: 1px solid var(--border); background: var(--bg); display: flex; align-items: center; gap: 12px; }
    .form-card-header-icon { width: 40px; height: 40px; background: var(--accent-soft); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--accent); flex-shrink: 0; }
    .form-card-header-text h5 { font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 2px; }
    .form-card-header-text p { font-size: 12px; color: var(--ink-muted); margin: 0; }

    /* ─── Form Body / Info ────────────────────────────── */
    .form-body { padding: 26px 28px; }
    .section-title { font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 18px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    @media (max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }
    .info-field { display: flex; flex-direction: column; gap: 6px; }
    .info-field-label { font-size: 11px; font-weight: 700; letter-spacing: 0.7px; text-transform: uppercase; color: var(--ink-muted); }
    .info-field-value { min-height: 44px; padding: 11px 14px; border: 1px solid var(--border); border-radius: var(--r-sm); background: var(--bg); display: flex; align-items: flex-start; gap: 9px; font-size: 13.5px; color: var(--ink); line-height: 1.5; word-break: break-word; }
    .info-field-value i { font-size: 12px; color: var(--ink-muted); margin-top: 2px; flex-shrink: 0; }

    /* ─── Form Footer ─────────────────────────────────── */
    .form-footer { padding: 16px 28px; border-top: 1px solid var(--border); background: var(--bg); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .btn-edit { height: 38px; padding: 0 18px; border-radius: var(--r-sm); border: none; background: var(--accent); color: #fff; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; transition: all 0.15s; }
    .btn-edit:hover { background: #1d4ed8; color: #fff; transform: translateY(-1px); }
    .btn-delete { height: 38px; padding: 0 16px; border-radius: var(--r-sm); border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; cursor: pointer; transition: all 0.15s; }
    .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }

    /* ─── Product List ────────────────────────────────── */
    .product-list { display: flex; flex-direction: column; }
    .product-item { display: flex; align-items: center; justify-content: space-between; padding: 13px 0; border-bottom: 1px solid var(--border); gap: 12px; }
    .product-item:last-child { border-bottom: none; }
    .product-name { display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 500; color: var(--ink); }
    .product-name-icon { width: 30px; height: 30px; border-radius: var(--r-sm); background: var(--accent-soft); display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--accent); flex-shrink: 0; }
    .btn-remove-product { height: 30px; padding: 0 12px; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--ink-muted); font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.15s; white-space: nowrap; }
    .btn-remove-product:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .empty-products { text-align: center; padding: 40px 20px; color: var(--ink-muted); }
    .empty-products-icon { width: 52px; height: 52px; border-radius: var(--r-md); background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--ink-muted); margin: 0 auto 14px; opacity: 0.7; }
    .product-count-chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; background: var(--accent-soft); border-radius: 999px; font-size: 12px; font-weight: 600; color: var(--accent); margin-left: 4px; }

    /* ─── Flash Alert ─────────────────────────────────── */
    .flash-alert { max-width: 860px; margin: 0 auto 20px; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: var(--r-md); font-size: 13px; font-weight: 500; animation: fadeSlideUp 0.3s ease both; }
    .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .flash-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="flash-alert flash-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-alert flash-error"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
@endif

<div class="page-header animate-in">
    <a href="{{ route('suppliers.index') }}" class="back-btn" title="Kembali"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h1>Detail Supplier</h1>
        <p>Informasi lengkap supplier dan produk yang disuplai</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-building"></i></div>
        <div class="form-card-header-text">
            <h5>{{ $supplier->nama_supplier }}</h5>
            <p>Supplier ID #{{ $supplier->id }}</p>
        </div>
    </div>
    <div class="form-body">
        <div class="section-title">Informasi Supplier</div>
        <div class="info-grid">
            <div class="info-field">
                <span class="info-field-label">Nama Supplier</span>
                <div class="info-field-value"><i class="fas fa-building"></i> {{ $supplier->nama_supplier }}</div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Telepon</span>
                <div class="info-field-value"><i class="fas fa-phone"></i> {{ $supplier->telepon ?? '—' }}</div>
            </div>
            <div class="info-field" style="grid-column: 1 / -1;">
                <span class="info-field-label">Alamat</span>
                <div class="info-field-value" style="min-height:60px; align-items:flex-start;"><i class="fas fa-location-dot"></i> {{ $supplier->alamat ?? '—' }}</div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Tanggal Dibuat</span>
                <div class="info-field-value"><i class="fas fa-calendar-plus"></i> {{ $supplier->created_at ? \Carbon\Carbon::parse($supplier->created_at)->translatedFormat('d F Y • H:i') : '—' }}</div>
            </div>
            <div class="info-field">
                <span class="info-field-label">Terakhir Diupdate</span>
                <div class="info-field-value"><i class="fas fa-clock-rotate-left"></i> {{ $supplier->updated_at ? \Carbon\Carbon::parse($supplier->updated_at)->translatedFormat('d F Y • H:i') : '—' }}</div>
            </div>
        </div>
    </div>
    <div class="form-footer">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-edit"><i class="fas fa-pen"></i> Edit Supplier</a>
        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
        </form>
        <span style="margin-left:auto; font-size:12px; color:var(--ink-muted);">Supplier #{{ $supplier->id }}</span>
    </div>
</div>

  @endsection
