@extends('layouts.app')
@section('title', 'Tambah Supplier')

{{-- Style tetap dipertahankan --}}
@push('styles')
<style>
    /* ─── (Semua CSS kamu tetap sama di sini) ─── */
    .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .back-btn { width: 34px; height: 34px; border: 1px solid var(--border); border-radius: var(--r-sm); background: var(--surface); color: var(--ink-soft); display: flex; align-items: center; justify-content: center; font-size: 13px; text-decoration: none; transition: all 0.15s; flex-shrink: 0; }
    .back-btn:hover { background: var(--bg); color: var(--ink); }
    .page-header h1 { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px; }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }
    .form-card { max-width: 860px; margin: 0 auto 24px auto; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeSlideUp 0.4s ease both; }
    .form-card-header { padding: 20px 28px 18px; border-bottom: 1px solid var(--border); background: var(--bg); display: flex; align-items: center; gap: 12px; }
    .form-card-header-icon { width: 40px; height: 40px; background: var(--accent-soft); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--accent); }
    .form-card-header-text h5 { font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 2px; }
    .form-card-header-text p { font-size: 12px; color: var(--ink-muted); margin: 0; }
    .form-body { padding: 26px 28px; }
    .field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 20px; }
    .field-label { font-size: 12.5px; font-weight: 600; color: var(--ink-soft); display: flex; align-items: center; gap: 4px; }
    .field-label .required { color: #ef4444; }
    .form-control { border: 1px solid var(--border); border-radius: var(--r-sm); font-size: 13.5px; color: var(--ink); padding: 10px 13px; background: var(--surface); transition: border-color 0.15s, box-shadow 0.15s; width: 100%; }
    .invalid-feedback { font-size: 12px; color: #dc2626; margin-top: 4px; }
    .form-footer { padding: 16px 28px; border-top: 1px solid var(--border); background: var(--bg); display: flex; align-items: center; gap: 10px; }
    .btn-save { height: 38px; padding: 0 20px; border: none; border-radius: var(--r-sm); background: var(--accent); color: #fff; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .btn-cancel { height: 38px; padding: 0 16px; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--ink-soft); font-size: 13.5px; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; }
</style>
@endpush

@section('content')

<div class="page-header animate-in">
    <a href="{{ route('suppliers.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h1>Tambah Supplier</h1>
        <p>Masukkan data supplier baru</p>
    </div>
</div>

<form method="POST" action="{{ route('suppliers.store') }}">
    @csrf
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-icon"><i class="fas fa-truck"></i></div>
            <div class="form-card-header-text">
                <h5>Informasi Supplier</h5>
                <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
            </div>
        </div>

        <div class="form-body">
            <div class="field-group">
                <label class="field-label">Nama Supplier <span class="required">*</span></label>
                <input type="text" name="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" required>
                @error('nama_supplier') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Nomor Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
            </div>

            <div class="field-group">
                <label class="field-label">Alamat Supplier</label>
                <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-save"><i class="fas fa-floppy-disk"></i> Simpan Supplier</button>
            <a href="{{ route('suppliers.index') }}" class="btn-cancel"><i class="fas fa-xmark"></i> Batal</a>
        </div>
    </div>
</form>

@endsection
