@extends('layouts.app')
@section('title', 'Tambah Produk')

@push('styles')
<style>
    .form-card{ max-width: 760px; margin: 0 auto; }
    .form-section{ padding: 28px; }
    .form-label{ font-size: .74rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: .5rem; }
    .form-control, textarea.form-control, .form-select{ border: 1px solid var(--border); border-radius: var(--r-md); background: var(--surface); min-height: 46px; padding: .72rem 1rem; font-size: .92rem; color: var(--ink); transition: .2s ease; }
    .form-control:focus, textarea.form-control:focus, .form-select:focus{ border-color: var(--accent); box-shadow: 0 0 0 4px rgba(37,99,235,.10); }
    .form-select option { color: #1e293b !important; background-color: #ffffff !important; }
    .form-hint{ font-size: 12px; color: var(--ink-muted); margin-top: 6px; }
    .required-star{ color: #dc2626; }
    .invalid-feedback{ font-size: .76rem; }
    .product-preview{ border: 1px dashed var(--border); background: linear-gradient(to bottom right, #f8fafc, #ffffff); border-radius: var(--r-lg); padding: 22px; display: flex; align-items: center; gap: 18px; }
    .preview-icon{ width: 58px; height: 58px; border-radius: 16px; background: rgba(59,130,246,.10); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .preview-title{ font-weight: 700; color: var(--ink); margin-bottom: 4px; }
    .preview-subtitle{ font-size: 13px; color: var(--ink-muted); }
    .form-actions{ display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; padding-top: 22px; border-top: 1px solid var(--border); }
    .btn-save{ border: none; background: var(--accent); color: white; padding: .72rem 1.4rem; border-radius: var(--r-md); font-weight: 600; font-size: .88rem; transition: .2s ease; display: inline-flex; align-items: center; gap: 8px; }
    .btn-save:hover{ transform: translateY(-1px); background: #1d4ed8; box-shadow: 0 10px 22px rgba(37,99,235,.18); }
    .btn-cancel{ border: 1px solid var(--border); background: white; color: var(--ink-soft); padding: .72rem 1.4rem; border-radius: var(--r-md); font-weight: 600; font-size: .88rem; transition: .2s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-cancel:hover{ background: #f8fafc; color: var(--ink); border-color: #cbd5e1; }
</style>
@endpush

@section('content')

<div class="page-header animate-in no-print">
    <div>
        <h1>Tambah Produk</h1>
        <p>Tambahkan produk baru ke dalam sistem toko</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left" style="font-size:12px;"></i> Kembali
    </a>
</div>

<div class="section-card animate-in form-card" style="animation-delay:.05s;">
    <div class="section-card-header">
        <div class="section-card-header-icon"><i class="fas fa-box-open"></i></div>
        <h6>Form Tambah Produk</h6>
        <div class="ms-auto" style="font-size:12px; color:var(--ink-muted);"><span class="required-star">*</span> wajib diisi</div>
    </div>

    <div class="form-section">
        <div class="product-preview mb-4">
            <div class="preview-icon"><i class="fas fa-cake-candles"></i></div>
            <div>
                <div class="preview-title">Produk Baru</div>
                <div class="preview-subtitle">Pastikan nama produk mudah dikenali agar pencarian dan transaksi lebih cepat.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- 1. NAMA PRODUK --}}
            <div class="mb-4">
                <label class="form-label">Nama Produk <span class="required-star">*</span></label>
                <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Contoh: Kue Black Forest" class="form-control @error('nama_produk') is-invalid @enderror" required autofocus>
                @error('nama_produk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
    <label class="form-label">
        Barcode
        <span class="required-star">*</span>
    </label>

    <input
        type="text"
        name="barcode"
        id="barcode"
        value="{{ old('barcode') }}"
        class="form-control"
        placeholder="Scan barcode menggunakan scanner USB"
        autocomplete="off">

    <div class="form-hint">
        Arahkan scanner ke barcode produk.
    </div>
</div>

{{-- FOTO PRODUK --}}
<div class="mb-4">
    <label class="form-label">
        Gambar Produk
    </label>

    <input
        type="file"
        name="image"
        id="image"
        class="form-control @error('image') is-invalid @enderror"
        accept="image/*">

    <div class="form-hint">
        Format: JPG, PNG, WEBP. Maksimal 2MB.
    </div>

    @error('image')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror

    <div class="mt-3">
        <img id="preview-image"
             src="#"
             style="display:none;max-width:220px;border-radius:12px;border:1px solid #ddd;padding:5px;">
    </div>
</div>



            <div class="row">
                {{-- 2. SUPPLIER --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Supplier Resmi <span class="required-star">*</span></label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="">— Pilih Supplier —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. KATEGORI (Dari form ke-2) --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kategori <span class="required-star">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                {{-- 4. SATUAN --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Satuan Barang <span class="required-star">*</span></label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                        <option value="">— Pilih Satuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 5. HARGA BELI --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Harga Beli (Rp) <span class="required-star">*</span></label>
                    <input type="number" name="harga_beli" value="{{ old('harga_beli') }}" class="form-control @error('harga_beli') is-invalid @enderror" min="0" step="1" placeholder="Contoh: 5000" required>
                </div>

                {{-- 6. HARGA JUAL --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Harga Jual (Rp) <span class="required-star">*</span></label>
                    <input type="number" name="harga_jual" value="{{ old('harga_jual') }}" class="form-control @error('harga_jual') is-invalid @enderror" min="0" step="1" placeholder="Contoh: 7500" required>
                </div>
            </div>

            {{-- 7. STOK --}}
            <div class="mb-4">
                <label class="form-label">Stok Awal <span class="required-star">*</span></label>
                <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" class="form-control @error('current_stock') is-invalid @enderror" min="0" readonly required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan Produk</button>
                <a href="{{ route('products.index') }}" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const barcode = document.getElementById('barcode');

    barcode.focus();

    barcode.addEventListener('keydown', function(e){

        if(e.key === 'Enter'){

            e.preventDefault();

            document.querySelector('[name="nama_produk"]').focus();

        }

    });

});

document.addEventListener('DOMContentLoaded', function () {

    const barcode = document.getElementById('barcode');

    barcode.focus();

    barcode.addEventListener('keydown', function(e){

        if(e.key === 'Enter'){
            e.preventDefault();
            document.querySelector('[name="nama_produk"]').focus();
        }

    });

    // Preview gambar
    const image = document.getElementById('image');
    const preview = document.getElementById('preview-image');

    image.addEventListener('change', function(e){

        const file = e.target.files[0];

        if(file){
            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";
        }else{
            preview.style.display = "none";
        }

    });

});


</script>
@endpush

@endsection
