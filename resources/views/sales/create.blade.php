@extends('layouts.app')
@section('title', 'Tambah Penjualan')

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

    /* Product item rows */
    .items-header {
        display: grid;
        grid-template-columns: 1fr 110px 140px 120px 36px;
        gap: 10px; padding: 0 0 8px;
        border-bottom: 1px solid var(--border); margin-bottom: 10px;
    }
    .items-header span {
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.7px;
        text-transform: uppercase; color: var(--ink-muted);
    }
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .product-select:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
        color: #9ca3af;
    }
    @media (max-width: 640px) {
        .field-row {
            grid-template-columns: 1fr;
        }
    }
    .item-row {
        display: grid;
        grid-template-columns: 1fr 110px 140px 120px 36px;
        gap: 10px; align-items: start;
        padding: 12px 14px;
        background: var(--bg); border: 1px solid var(--border);
        border-radius: var(--r-md); margin-bottom: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .item-row:hover { border-color: #d1d5db; box-shadow: var(--shadow-sm); }
    .item-row:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,78,216,0.06); }
    .item-row .form-control,
    .item-row .form-select { background: var(--surface); font-size: 13px; padding: 7px 10px; }

    /* Stock badge on select */
    .stok-hint {
        font-size: 11.5px; color: var(--ink-muted); margin-top: 4px;
        display: none;
    }
    .stok-hint.show { display: block; }
    .stok-hint.low  { color: #dc2626; }

    .subtotal-display {
        font-family: 'Sora', sans-serif; font-size: 13px;
        font-weight: 600; color: var(--ink); padding: 9px 0; white-space: nowrap;
    }
    .subtotal-display.empty { color: var(--ink-muted); font-weight: 400; font-family: inherit; }

    .btn-remove-row {
        width: 34px; height: 34px; border-radius: var(--r-sm);
        border: 1px solid #fecaca; background: #fef2f2;
        color: #dc2626; font-size: 12px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
    }
    .btn-remove-row:hover { background: #fee2e2; border-color: #fca5a5; }

    /* Add row button */
    .btn-add-row {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 14px; border-radius: var(--r-sm);
        border: 1.5px dashed var(--border); background: transparent;
        color: var(--ink-soft); font-size: 13px; font-weight: 500;
        cursor: pointer; transition: all 0.15s; margin-top: 4px;
    }
    .btn-add-row:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }

    /* Grand total bar */
    .grand-total-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--ink); border-radius: var(--r-md);
        padding: 14px 18px; margin-top: 16px;
    }
    .grand-total-label  { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.7); }
    .grand-total-value  { font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 700; color: #fff; letter-spacing: -0.3px; }
    .grand-total-count  { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }

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

    @media (max-width: 640px) {
        .items-header { display: none; }
        .item-row {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto auto;
        }
        .item-row > *:first-child { grid-column: 1 / -1; }
        .subtotal-display { grid-column: 1 / 2; }
        .btn-remove-row   { justify-self: end; }
    }
</style>
@endpush

@section('content')
<div class="page-header animate-in">
    <a href="{{ route('sales.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Tambah Penjualan</h1>
        <p>Catat transaksi penjualan baru</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-cash-register"></i></div>
        <div class="form-card-header-text">
            <h5>Form Penjualan</h5>
            <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf

            {{-- Informasi Transaksi --}}
            <div class="section-title">Informasi Transaksi</div>
            <div class="field-row mb-4">
                {{-- Kolom Tanggal --}}
                <div class="field-group">
                    <label class="field-label" for="tanggal">
                        Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="form-control @error('tanggal') is-invalid @enderror"
                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Kolom Supplier (onchange reload dihapus total biar gak relog) --}}
                <div class="field-group">
                    <label class="field-label" for="supplier_id">
                        Supplier <span class="required">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        <option value="">— Pilih Supplier —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Daftar Produk --}}
            <div class="section-title">Daftar Produk Terjual</div>

            <div class="items-header d-none d-md-grid">
                <span>Produk</span>
                <span>Jumlah Terjual</span>
                <span>Harga Jual (Rp)</span>
                <span>Subtotal</span>
                <span></span>
            </div>

            <div id="items-wrapper">
                {{-- Baris pertama di-block default sampai supplier dipilih --}}
                <div class="item-row" data-idx="0">
                    <div>
                        <select name="items[0][product_id]" class="form-select product-select" disabled required>
                            <option value="">⚠️ Pilih Supplier Terlebih Dahulu</option>
                        </select>
                        <div class="stok-hint" id="stok-hint-0"></div>
                    </div>
                    <div>
                        <input type="number" name="items[0][laku]" class="form-control qty-input"
                            placeholder="0" min="1" step="1" required>
                    </div>
                    <div>
                        <input type="number" name="items[0][harga_jual]" class="form-control price-input"
                            placeholder="0" min="0" step="1" required>
                    </div>
                    <div class="subtotal-display empty" id="subtotal-0">—</div>
                    <button type="button" class="btn-remove-row" title="Hapus baris">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            <button type="button" id="btn-add-row" class="btn-add-row">
                <i class="fas fa-plus" style="font-size:11px;"></i> Tambah Produk
            </button>

            {{-- Grand Total --}}
            <div class="grand-total-bar mt-4">
                <div>
                    <div class="grand-total-label">Total Bayar</div>
                    <div class="grand-total-count" id="row-count">0 produk</div>
                </div>
                <div class="grand-total-value" id="grand-total">Rp 0</div>
            </div>
        </form>
    </div>

    <div class="form-footer">
        <button type="submit" form="saleForm"
            class="btn btn-primary d-flex align-items-center gap-2"
            style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 20px;">
            <i class="fas fa-floppy-disk" style="font-size:13px;"></i> Simpan Penjualan
        </button>
        <a href="{{ route('sales.index') }}" class="btn"
            style="height:38px; font-size:13.5px; border-radius:var(--r-sm); padding:0 16px;
                   border:1px solid var(--border); color:var(--ink-soft);">
            Batal
        </a>
    </div>
</div>

{{-- Template Kloning Baris Baru --}}
<template id="item-row-template">
    <div class="item-row" data-idx="__INDEX__">
        <div>
            <select name="items[__INDEX__][product_id]" class="form-select product-select" disabled required>
                <option value="">⚠️ Pilih Supplier Terlebih Dahulu</option>
            </select>
            <div class="stok-hint" id="stok-hint-__INDEX__"></div>
        </div>
        <div>
            <input type="number" name="items[__INDEX__][laku]" class="form-control qty-input"
                placeholder="0" min="1" step="1" required>
        </div>
        <div>
            <input type="number" name="items[__INDEX__][harga_jual]" class="form-control price-input"
                placeholder="0" min="0" step="1" required>
        </div>
        <div class="subtotal-display empty" id="subtotal-__INDEX__">—</div>
        <button type="button" class="btn-remove-row" title="Hapus baris">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script>
    // Ambil data produk yang dikirim dari controller
    const PRODUCTS = @json($products_json ?? []);

    // Format mata uang Rupiah
    const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));

    // Update subtotal per baris produk
    function updateRowSubtotal(row) {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const sub   = qty * price;
        const subtotalEl = row.querySelector('[class*="subtotal-display"]');
        if (sub > 0) {
            subtotalEl.textContent = fmt(sub);
            subtotalEl.classList.remove('empty');
        } else {
            subtotalEl.textContent = '—';
            subtotalEl.classList.add('empty');
        }
    }

    // Update grand total dan jumlah produk terpilih
    function updateGrandTotalAndCount() {
        let total = 0;
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += qty * price;
        });
        document.getElementById('grand-total').textContent = fmt(total);
        document.getElementById('row-count').textContent = rows.length + ' produk';
    }

    // Fungsi pasang Event Listener ke baris produk
    function bindRowEvents(row, idx) {
        const select     = row.querySelector('.product-select');
        const qtyInput   = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const stokHint   = row.querySelector('.stok-hint');

        select.addEventListener('change', () => {
            const opt  = select.selectedOptions[0];
            const stok = parseInt(opt?.dataset?.stok ?? 0);
            if (select.value && !isNaN(stok)) {
                stokHint.textContent = `Stok tersedia: ${stok} unit`;
                stokHint.className   = 'stok-hint show' + (stok < 5 ? ' low' : '');
            } else {
                stokHint.className = 'stok-hint';
            }
            updateRowSubtotal(row);
            updateGrandTotalAndCount();
        });

        const recalc = () => {
            updateRowSubtotal(row);
            updateGrandTotalAndCount();
        };
        qtyInput.addEventListener('input', recalc);
        priceInput.addEventListener('input', recalc);

        const removeBtn = row.querySelector('.btn-remove-row');
        removeBtn.addEventListener('click', () => {
            row.remove();
            updateGrandTotalAndCount();
        });
    }

    let nextIndex = 1;

    // Fungsi tambah baris produk baru secara dinamis
    function addNewRow() {
        const supplierId = document.getElementById('supplier_id').value;

        // Proteksi: Jika supplier belum dipilih, blok aksi tambah produk!
        if (!supplierId) {
            alert('Pilih Supplier terlebih dahulu sebelum menambah produk!');
            document.getElementById('supplier_id').focus();
            return;
        }

        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        const row = clone.firstElementChild;

        const currentIdx = nextIndex++;
        row.innerHTML = row.innerHTML.replace(/__INDEX__/g, currentIdx);
        const newRow = row;

        document.getElementById('items-wrapper').appendChild(newRow);
        bindRowEvents(newRow, currentIdx);

        // Isi otomatis opsi produk sesuai supplier yang aktif pada baris baru ini
        const select = newRow.querySelector('.product-select');
        populateProductOptions(select, supplierId);
        PRODUCTS.forEach(product => {
                if (product.supplier_id == supplierId) {
                    // Tambahkan data-harga di sini
                    options += `<option value="${product.id}" data-stok="${product.stok}" data-harga="${product.harga_jual}">
                        ${product.nama_produk} (Stok: ${product.stok})
                    </option>`;
                    hasProducts = true;
                }
            });
        updateGrandTotalAndCount();
    }

    // Fungsi pembantu untuk mengisi opsi produk berdasarkan supplier id secara real-time
    function populateProductOptions(selectElement, supplierId) {
        if (!supplierId) {
            selectElement.innerHTML = '<option value="">⚠️ Pilih Supplier Terlebih Dahulu</option>';
            selectElement.disabled = true;
            return;
        }

        let options = '<option value="">— Pilih Produk —</option>';
        let hasProducts = false;

        PRODUCTS.forEach(product => {
            if (product.supplier_id == supplierId) {
                options += `<option value="${product.id}" data-stok="${product.stok}">${product.nama_produk} (Stok: ${product.stok})</option>`;
                hasProducts = true;
            }
        });

        if (!hasProducts) {
            options = '<option value="">❌ Tidak ada produk untuk supplier ini</option>';
        }

        selectElement.innerHTML = options;
        selectElement.disabled = false;
    }

    // LOGIKA UTAMA: Ketika Supplier dipilih (tanpa relog)
    document.getElementById('supplier_id').addEventListener('change', function () {
        const supplierId = this.value;

        // Update semua baris produk yang sudah ada di layar saat ini secara instan
        document.querySelectorAll('.product-select').forEach(select => {
            populateProductOptions(select, supplierId);
        });

        updateGrandTotalAndCount();
    });

    // Inisialisasi awal tombo tambah baris
    document.getElementById('btn-add-row').addEventListener('click', addNewRow);

    // Jalankan binding untuk baris default pertama kali load
    document.querySelectorAll('.item-row').forEach((row, idx) => {
        bindRowEvents(row, idx);
        const select = row.querySelector('.product-select');
        const priceInput = row.querySelector('.price-input');
        select.addEventListener('change', () => {
    const opt = select.selectedOptions[0];
    const harga = opt?.dataset?.harga || 0;

    // Isi otomatis input harga agar tidak kosong saat disubmit
    priceInput.value = harga;

    // ... (update stok hint & subtotal)
    updateRowSubtotal(row);
    updateGrandTotalAndCount();
});
    });

    updateGrandTotalAndCount();

    // Proteksi saat form mau dikirim ke backend Laravel
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 produk sebelum menyimpan.');
            return;
        }
        let valid = true;
        rows.forEach(row => {
            if (!row.querySelector('.product-select').value ||
                !row.querySelector('.qty-input').value ||
                !row.querySelector('.price-input').value) {
                valid = false;
            }
        });
        if (!valid) {
            e.preventDefault();
            alert('Lengkapi semua data kolom produk terlebih dahulu!');
        }
    });
</script>
@endpush
