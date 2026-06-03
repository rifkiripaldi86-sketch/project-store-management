@extends('layouts.app')
@section('title', 'Edit Supplier')

@push('styles')
<style>
    /* Header */
    .page-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .back-btn {
        width: 34px;
        height: 34px;
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        background: var(--surface);
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .back-btn:hover {
        background: var(--bg);
        color: var(--ink);
    }

    .page-header h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 2px;
        letter-spacing: -0.3px;
    }

    .page-header p {
        font-size: 13px;
        color: var(--ink-muted);
        margin: 0;
    }

    /* Form card */
    .form-card {
        max-width: 860px;
        margin: 0 auto;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeSlideUp 0.4s 0.05s ease both;
    }

    .form-card-header {
        padding: 20px 28px 18px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-card-header-icon {
        width: 40px;
        height: 40px;
        background: #fef3c7;
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #d97706;
    }

    .form-card-header-text h5 {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 2px;
    }

    .form-card-header-text p {
        font-size: 12px;
        color: var(--ink-muted);
        margin: 0;
    }

    /* Form body */
    .form-body {
        padding: 26px 28px;
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 20px;
    }

    .field-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .field-label .required {
        color: #ef4444;
    }

    .form-control,
    textarea.form-control {
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        font-size: 13.5px;
        color: var(--ink);
        padding: 10px 13px;
        background: var(--surface);
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    textarea.form-control {
        min-height: 110px;
        resize: vertical;
    }

    .form-control:focus,
    textarea.form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
        outline: none;
    }

    .form-control.is-invalid,
    textarea.form-control.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
    }

    /* Footer */
    .form-footer {
        padding: 16px 28px;
        border-top: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-update {
        height: 38px;
        padding: 0 20px;
        border: none;
        border-radius: var(--r-sm);
        background: #d97706;
        color: #fff;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .15s;
    }

    .btn-update:hover {
        background: #b45309;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(217,119,6,.18);
    }

    .btn-cancel {
        height: 38px;
        padding: 0 16px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: 13.5px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
        transition: all .15s;
    }

    .btn-cancel:hover {
        background: var(--bg);
        color: var(--ink);
    }

    .form-hint {
        margin-left: auto;
        font-size: 12px;
        color: var(--ink-muted);
    }

    /* Info badge */
    .supplier-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: var(--bg);
        border: 1px solid var(--border);
        font-size: 12px;
        color: var(--ink-soft);
        margin-top: 10px;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <a href="{{ route('suppliers.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div>
        <h1>Edit Supplier</h1>
        <p>Perbaharui data supplier</p>
    </div>
</div>

{{-- Form card --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-pen"></i>
        </div>

        <div class="form-card-header-text">
            <h5>Form Edit Supplier</h5>
            <p>Perbarui informasi supplier dengan data terbaru</p>

            <div class="supplier-badge">
                <i class="fas fa-building" style="font-size:10px;"></i>
                {{ $supplier->nama_supplier }}
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')

        <div class="form-body">

            {{-- Informasi Supplier --}}
            <div class="section-title">
                Informasi Supplier
            </div>

            {{-- Nama Supplier --}}
            <div class="field-group">
                <label class="field-label">
                    Nama Supplier
                    <span class="required">*</span>
                </label>

                <input type="text"
                    name="nama_supplier"
                    class="form-control @error('nama_supplier') is-invalid @enderror"
                    value="{{ old('nama_supplier', $supplier->nama_supplier) }}"
                    placeholder="Contoh: PT Sumber Rezeki"
                    required
                    autofocus>

                @error('nama_supplier')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Telepon --}}
            <div class="field-group">
                <label class="field-label">
                    Nomor Telepon
                </label>

                <input type="text"
                    name="telepon"
                    class="form-control @error('telepon') is-invalid @enderror"
                    value="{{ old('telepon', $supplier->telepon) }}"
                    placeholder="08xxxxxxxxxx">

                @error('telepon')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">
                    Alamat Supplier
                </label>

                <textarea
                    name="alamat"
                    class="form-control @error('alamat') is-invalid @enderror"
                    placeholder="Masukkan alamat lengkap supplier...">{{ old('alamat', $supplier->alamat) }}</textarea>

                @error('alamat')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div class="form-footer">
            <button type="submit" class="btn-update">
                <i class="fas fa-floppy-disk" style="font-size:12px;"></i>
                Update Supplier
            </button>

            <a href="{{ route('suppliers.index') }}" class="btn-cancel">
                <i class="fas fa-xmark" style="font-size:12px;"></i>
                Batal
            </a>

            <span class="form-hint">
                Pastikan perubahan data sudah benar sebelum disimpan
            </span>
        </div>
    </form>
</div>

@endsection