@extends('layouts.app')
@section('title', 'Tambah Supplier')

@push('styles')
<style>
    /* ─── Page Header ─────────────────────────────────── */
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

    /* ─── Form Card ───────────────────────────────────── */
    .form-card {
        max-width: 860px;
        margin: 0 auto 24px auto;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeSlideUp 0.4s ease both;
    }

    .form-card:nth-of-type(1) { animation-delay: 0.05s; }
    .form-card:nth-of-type(2) { animation-delay: 0.15s; }

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

    /* ─── Form Body ───────────────────────────────────── */
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

    .field-group:last-child {
        margin-bottom: 0;
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
        width: 100%;
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

    /* ─── Form Footer ─────────────────────────────────── */
    .form-footer {
        padding: 16px 28px;
        border-top: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-save {
        height: 38px;
        padding: 0 20px;
        border: none;
        border-radius: var(--r-sm);
        background: var(--accent);
        color: #fff;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .15s;
        cursor: pointer;
    }

    .btn-save:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(29,78,216,.18);
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

    /* ─── Product Rows ────────────────────────────────── */
    .product-rows {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 14px;
    }

    .product-row {
        display: flex;
        align-items: center;
        gap: 8px;
        animation: fadeSlideUp 0.2s ease both;
    }

    .product-row-num {
        width: 26px;
        height: 26px;
        border-radius: var(--r-sm);
        background: var(--accent-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--accent);
        flex-shrink: 0;
    }

    .product-row input {
        flex: 1;
    }

    .btn-remove-row {
        width: 34px;
        height: 38px;
        border-radius: var(--r-sm);
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .btn-remove-row:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    .btn-add-row {
        height: 36px;
        padding: 0 14px;
        border-radius: var(--r-sm);
        border: 1.5px dashed var(--border);
        background: transparent;
        color: var(--ink-muted);
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.15s;
        width: 100%;
        justify-content: center;
    }

    .btn-add-row:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-soft);
    }

    /* ─── Empty Products State ────────────────────────── */
    .products-empty-hint {
        text-align: center;
        padding: 24px 20px;
        border: 1.5px dashed var(--border);
        border-radius: var(--r-md);
        color: var(--ink-muted);
        font-size: 13px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .products-empty-hint:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-soft);
    }

    .products-empty-hint i {
        display: block;
        font-size: 20px;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    /* ─── Product Summary Badge ───────────────────────── */
    .product-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        background: var(--accent-soft);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
        margin-left: 4px;
        transition: all 0.2s;
    }

    /* ─── Validation errors in product rows ───────────── */
    .product-row-error {
        font-size: 11.5px;
        color: #dc2626;
        margin-top: 2px;
        padding-left: 34px;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header animate-in">
    <a href="{{ route('suppliers.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Tambah Supplier</h1>
        <p>Masukkan data supplier dan produk yang disuplai</p>
    </div>
</div>

<form method="POST" action="{{ route('suppliers.store') }}">
    @csrf

    {{-- ── Card 1: Informasi Supplier ──────────────────── --}}
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="form-card-header-text">
                <h5>Informasi Supplier</h5>
                <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
            </div>
        </div>

        <div class="form-body">

            {{-- Nama Supplier --}}
            <div class="field-group">
                <label class="field-label">
                    Nama Supplier <span class="required">*</span>
                </label>
                <input type="text"
                    name="nama_supplier"
                    class="form-control @error('nama_supplier') is-invalid @enderror"
                    value="{{ old('nama_supplier') }}"
                    placeholder="Contoh: PT Sumber Rezeki"
                    required
                    autofocus>
                @error('nama_supplier')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Telepon --}}
            <div class="field-group">
                <label class="field-label">Nomor Telepon</label>
                <input type="text"
                    name="telepon"
                    class="form-control @error('telepon') is-invalid @enderror"
                    value="{{ old('telepon') }}"
                    placeholder="08xxxxxxxxxx">
                @error('telepon')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="field-group">
                <label class="field-label">Alamat Supplier</label>
                <textarea
                    name="alamat"
                    class="form-control @error('alamat') is-invalid @enderror"
                    placeholder="Masukkan alamat lengkap supplier...">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── Card 2: Produk yang Disuplai ────────────────── --}}
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div class="form-card-header-text">
                <h5>
                    Produk yang Disuplai
                    <span class="product-count-chip" id="productCountChip" style="display:none;">
                        <i class="fas fa-box" style="font-size:9px;"></i>
                        <span id="productCountNum">0</span>
                    </span>
                </h5>
                <p>Opsional — bisa ditambahkan nanti di halaman detail</p>
            </div>
        </div>

        <div class="form-body">

            {{-- Error global untuk products[] --}}
            @if($errors->has('products.*'))
                <div style="margin-bottom:14px; padding:10px 14px; background:#fef2f2; border:1px solid #fecaca; border-radius:var(--r-sm); font-size:12.5px; color:#dc2626;">
                    <i class="fas fa-circle-xmark me-1"></i>
                    Terdapat nama produk yang tidak valid atau duplikat. Periksa kembali daftar di bawah.
                </div>
            @endif

            {{-- Container baris produk --}}
            <div class="product-rows" id="productRows">

                {{-- Re-populate old input jika ada validasi error --}}
                @if(old('products'))
                    @foreach(old('products') as $i => $oldProduct)
                        <div class="product-row" data-row="{{ $i }}">
                            <div class="product-row-num">{{ $i + 1 }}</div>
                            <input type="text"
                                name="products[]"
                                class="form-control @error('products.' . $i) is-invalid @enderror"
                                value="{{ $oldProduct }}"
                                placeholder="Nama produk, mis: Tepung Terigu">
                            <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Hapus baris">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @error('products.' . $i)
                            <div class="product-row-error">
                                <i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    @endforeach
                @endif

            </div>

            {{-- Hint saat kosong --}}
            <div id="emptyHint" class="products-empty-hint" onclick="addRow()" style="{{ old('products') ? 'display:none;' : '' }}">
                <i class="fas fa-box-open"></i>
                Belum ada produk. Klik untuk menambahkan produk pertama.
            </div>

            {{-- Tombol tambah baris --}}
            <button type="button"
                class="btn-add-row"
                id="btnAddRow"
                onclick="addRow()"
                style="{{ old('products') ? '' : 'display:none;' }}">
                <i class="fas fa-plus" style="font-size:10px;"></i>
                Tambah Produk Lagi
            </button>

        </div>

        {{-- Footer form (di dalam card produk, mencakup seluruh form) --}}
        <div class="form-footer">
            <button type="submit" class="btn-save">
                <i class="fas fa-floppy-disk" style="font-size:12px;"></i>
                Simpan Supplier
            </button>

            <a href="{{ route('suppliers.index') }}" class="btn-cancel">
                <i class="fas fa-xmark" style="font-size:12px;"></i>
                Batal
            </a>

            <span class="form-hint">
                Pastikan data sudah benar sebelum disimpan
            </span>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
    let rowCount = {{ old('products') ? count(old('products')) : 0 }};

    function updateUI() {
        const rows   = document.querySelectorAll('.product-row');
        const count  = rows.length;
        const hint   = document.getElementById('emptyHint');
        const addBtn = document.getElementById('btnAddRow');
        const chip   = document.getElementById('productCountChip');
        const num    = document.getElementById('productCountNum');

        hint.style.display   = count === 0 ? '' : 'none';
        addBtn.style.display = count === 0 ? 'none' : '';
        chip.style.display   = count === 0 ? 'none' : '';
        num.textContent      = count;

        // Re-number badges
        rows.forEach((row, i) => {
            row.querySelector('.product-row-num').textContent = i + 1;
            row.dataset.row = i;
        });
    }

    function addRow() {
        const container = document.getElementById('productRows');

        const row = document.createElement('div');
        row.className = 'product-row';
        row.dataset.row = rowCount;
        row.innerHTML = `
            <div class="product-row-num">${container.children.length + 1}</div>
            <input type="text"
                name="products[]"
                class="form-control"
                placeholder="Nama produk, mis: Tepung Terigu"
                autofocus>
            <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Hapus baris">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;

        container.appendChild(row);
        row.querySelector('input').focus();
        rowCount++;
        updateUI();
    }

    function removeRow(btn) {
        const row = btn.closest('.product-row');
        // Hapus juga error hint di bawahnya jika ada
        const next = row.nextElementSibling;
        if (next && next.classList.contains('product-row-error')) {
            next.remove();
        }
        row.remove();
        updateUI();
    }

    // Init
    updateUI();
</script>
@endpush
