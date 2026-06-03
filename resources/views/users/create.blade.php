@extends('layouts.app')
@section('title', 'Tambah User')

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
        background: var(--accent-soft);
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: var(--accent);
        flex-shrink: 0;
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

    /* Body */
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

    /* Form */
    .field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 18px;
    }

    .field-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .required {
        color: #ef4444;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        font-size: 13.5px;
        color: var(--ink);
        padding: 10px 13px;
        height: auto;
        background: var(--surface);
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
        outline: none;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
        display: block;
    }

    /* Password hint */
    .password-hint {
        font-size: 11.5px;
        color: var(--ink-muted);
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

    @media (max-width: 640px) {
        .form-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .form-footer .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">

    <a href="{{ route('users.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div>
        <h1>Tambah User</h1>
        <p>Tambahkan akun pengguna baru ke sistem</p>
    </div>

</div>

<div class="form-card">

    <div class="form-card-header">

        <div class="form-card-header-icon">
            <i class="fas fa-user-plus"></i>
        </div>

        <div class="form-card-header-text">
            <h5>Form Tambah User</h5>
            <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>

    </div>

    <form method="POST" action="{{ route('users.store') }}">

        @csrf

        <div class="form-body">

            <div class="section-title">Informasi User</div>

            {{-- Nama --}}
            <div class="field-group">
                <label class="field-label">
                    Nama Lengkap
                    <span class="required">*</span>
                </label>

                <input type="text"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap"
                    required
                    autofocus>

                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="field-group">
                <label class="field-label">
                    Email
                    <span class="required">*</span>
                </label>

                <input type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="contoh@email.com"
                    required>

                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field-group">
                <label class="field-label">
                    Password
                    <span class="required">*</span>
                </label>

                <input type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password"
                    required>

                <div class="password-hint">
                    Gunakan kombinasi huruf dan angka agar lebih aman
                </div>

                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role --}}
            <div class="field-group" style="max-width:280px;">
                <label class="field-label">
                    Role User
                    <span class="required">*</span>
                </label>

                <select name="role"
                    class="form-select @error('role') is-invalid @enderror"
                    required>

                    <option value="operator"
                        {{ old('role') == 'operator' ? 'selected' : '' }}>
                        Operator
                    </option>

                    <option value="admin"
                        {{ old('role') == 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>

                </select>

                @error('role')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div class="form-footer">

            <button type="submit"
                class="btn btn-primary d-flex align-items-center gap-2"
                style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 20px;">

                <i class="fas fa-floppy-disk" style="font-size:12px;"></i>
                Simpan User
            </button>

            <a href="{{ route('users.index') }}"
                class="btn d-flex align-items-center gap-2"
                style="height:38px; font-size:13.5px; border-radius:var(--r-sm);
                       padding:0 16px; border:1px solid var(--border);
                       color:var(--ink-soft);">

                <i class="fas fa-xmark" style="font-size:12px;"></i>
                Batal
            </a>

        </div>

    </form>

</div>

@endsection