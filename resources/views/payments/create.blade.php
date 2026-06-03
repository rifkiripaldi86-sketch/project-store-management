@extends('layouts.app')
@section('title', 'Pembayaran Supplier')

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

    /* Form card */
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

    /* Form fields */
    .form-body { padding: 26px 28px; }
    .section-title {
        font-size: 12px; font-weight: 700; letter-spacing: 0.8px;
        text-transform: uppercase; color: var(--ink-soft);
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }
    .field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 18px; }
    .field-label {
        font-size: 12.5px; font-weight: 600; color: var(--ink-soft);
        display: flex; align-items: center; gap: 4px;
    }
    .field-label .required { color: #ef4444; }
    .form-control, .form-select {
        border: 1px solid var(--border); border-radius: var(--r-sm);
        font-size: 13.5px; color: var(--ink); padding: 9px 13px; height: auto;
        background: var(--surface); transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,78,216,0.08); outline: none;
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
    .invalid-feedback { font-size: 12px; color: #dc2626; margin-top: 4px; display: block; }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 520px) { .field-row { grid-template-columns: 1fr; } }

    /* Info box */
    .info-box {
        background: var(--accent-soft); border: 1px solid rgba(29,78,216,0.15);
        border-radius: var(--r-md); padding: 14px 16px;
        display: flex; align-items: flex-start; gap: 10px; margin-top: 4px;
    }
    .info-box i { color: var(--accent); margin-top: 1px; font-size: 13px; flex-shrink: 0; }
    .info-box p { font-size: 12.5px; color: var(--ink-soft); margin: 0; line-height: 1.6; }

    /* Footer */
    .form-footer {
        padding: 16px 28px; border-top: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .kbd {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 2px 6px; border: 1px solid #d1d5db; border-radius: 4px;
        font-size: 10.5px; font-weight: 600; color: var(--ink-soft);
        background: #fff; font-family: monospace;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <a href="{{ route('dashboard') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Pembayaran Supplier</h1>
        <p>Hitung & catat pembayaran mingguan ke supplier</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="form-card-header-text">
            <h5>Form Pembayaran Supplier</h5>
            <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
            @csrf

            {{-- Supplier --}}
            <div class="section-title">Informasi Supplier</div>
            <div class="field-group" style="margin-bottom: 24px;">
                <label class="field-label" for="supplier_id">
                    Supplier <span class="required">*</span>
                </label>
                <select name="supplier_id" id="supplier_id"
                    class="form-select @error('supplier_id') is-invalid @enderror" required>
                    <option value="">— Pilih Supplier —</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            {{-- Periode --}}
            <div class="section-title">Periode Pembayaran</div>
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label" for="periode_awal">
                        Dari Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="periode_awal" id="periode_awal"
                        class="form-control @error('periode_awal') is-invalid @enderror"
                        value="{{ old('periode_awal') }}" required>
                    @error('periode_awal')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="field-group">
                    <label class="field-label" for="periode_akhir">
                        Sampai Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="periode_akhir" id="periode_akhir"
                        class="form-control @error('periode_akhir') is-invalid @enderror"
                        value="{{ old('periode_akhir') }}" required>
                    @error('periode_akhir')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="info-box">
                <i class="fas fa-circle-info"></i>
                <p>
                    Sistem akan menghitung total bayar supplier berdasarkan data kiriman dan penjualan
                    pada periode yang dipilih. Nota pembayaran akan otomatis dicetak setelah disimpan.
                </p>
            </div>

            @if(session('error'))
            <div style="margin-top:14px; padding:12px 14px; background:#fef2f2; border:1px solid #fecaca;
                        border-radius:var(--r-md); font-size:13px; color:#dc2626; display:flex; gap:8px; align-items:center;">
                <i class="fas fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
            @endif
        </form>
    </div>

    <div class="form-footer">
        <button type="submit" form="paymentForm"
            class="btn btn-primary d-flex align-items-center gap-2"
            style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 20px;">
            <i class="fas fa-calculator" style="font-size:13px;"></i> Hitung &amp; Simpan
        </button>
        <a href="{{ route('dashboard') }}" class="btn"
            style="height:38px; font-size:13.5px; border-radius:var(--r-sm); padding:0 16px;
                   border:1px solid var(--border); color:var(--ink-soft);">
            Batal
        </a>
        <span style="margin-left:auto; font-size:12px; color:var(--ink-muted); display:flex; align-items:center; gap:5px;">
            <kbd class="kbd">Ctrl</kbd> + <kbd class="kbd">Enter</kbd> untuk simpan
        </span>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        document.getElementById('paymentForm').submit();
    }
});
</script>
@endpush