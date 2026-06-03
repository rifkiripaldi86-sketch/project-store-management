@extends('layouts.app')
@section('title', 'Tambah Kiriman Supplier')

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

    /* ── Product item rows ── */
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
    .item-row .form-select {
        background: var(--surface); font-size: 13px; padding: 7px 10px;
    }

    /* Style untuk form select ketika terkunci (disabled) */
    .item-row .form-select:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
        color: #9ca3af;
    }

    .subtotal-display {
        font-family: 'Sora', sans-serif;
        font-size: 13px; font-weight: 600; color: var(--ink);
        padding: 9px 0; white-space: nowrap;
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
    .grand-total-label {
        font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.7);
    }
    .grand-total-value {
        font-family: 'Sora', sans-serif;
        font-size: 20px; font-weight: 700; color: #fff; letter-spacing: -0.3px;
    }
    .grand-total-count {
        font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px;
    }

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

{{-- Header --}}
<div class="page-header animate-in">
    <a href="{{ route('deliveries.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Tambah Kiriman Supplier</h1>
        <p>Catat barang kiriman baru dari supplier</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-truck-ramp-box"></i></div>
        <div class="form-card-header-text">
            <h5>Form Kiriman Supplier</h5>
            <p>Kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('deliveries.store') }}" id="deliveryForm">
            @csrf

            {{-- Informasi Kiriman --}}
            <div class="section-title">Informasi Kiriman</div>
            <div class="field-row mb-4">
                <div class="field-group">
                    <label class="field-label" for="tanggal">
                        Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="form-control @error('tanggal') is-invalid @enderror"
                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="field-group">
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
            </div>

            {{-- Daftar Produk --}}
            <div class="section-title">Daftar Produk</div>

            <div class="items-header d-none d-md-grid">
                <span>Produk</span>
                <span>Jumlah</span>
                <span>Harga Satuan (Rp)</span>
                <span>Subtotal</span>
                <span></span>
            </div>

            <div id="items-wrapper"></div>

            <button type="button" id="btn-add-row" class="btn-add-row">
                <i class="fas fa-plus" style="font-size:11px;"></i> Tambah Produk
            </button>

            {{-- Grand Total --}}
            <div class="grand-total-bar mt-4">
                <div>
                    <div class="grand-total-label">Grand Total</div>
                    <div class="grand-total-count" id="row-count">0 produk</div>
                </div>
                <div class="grand-total-value" id="grand-total">Rp 0</div>
            </div>
        </form>
    </div>

    <div class="form-footer">
        <button type="submit" form="deliveryForm" id="btn-submit"
            class="btn btn-primary d-flex align-items-center gap-2"
            style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 20px;">
            <i class="fas fa-floppy-disk" style="font-size:13px;"></i> Simpan Kiriman
        </button>
        <a href="{{ route('deliveries.index') }}" class="btn"
            style="height:38px; font-size:13.5px; border-radius:var(--r-sm); padding:0 16px; border:1px solid var(--border); color:var(--ink-soft);">
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
const PRODUCTS = @json($products->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_produk]));

let rowIndex = 0;
let supplierProducts = [];

const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));

/* Build product <options> string */
function getProductOptions() {
    const data = supplierProducts.length ? supplierProducts : PRODUCTS;
    return data.map(product => `
        <option value="${product.id}">
            ${product.nama_produk ?? product.nama}
        </option>
    `).join('');
}

function createRow(idx) {
    const row = document.createElement('div');
    row.className = 'item-row';
    row.dataset.idx = idx;

    // Cek apakah supplier sudah dipilih saat membuat baris baru
    const currentSupplier = document.getElementById('supplier_id').value;
    const isDisabled = currentSupplier ? '' : 'disabled';
    const placeholderText = currentSupplier ? '— Pilih Produk —' : '⚠️ Pilih Supplier Terlebih Dahulu';

    row.innerHTML = `
        <div>
            <select name="items[${idx}][product_id]" class="form-select product-select" ${isDisabled} required>
                <option value="">${placeholderText}</option>
                ${getProductOptions()}
            </select>
        </div>
        <div>
            <input type="number" name="items[${idx}][jumlah_kirim]"
                class="form-control qty-input"
                placeholder="0" min="1" step="1" required>
        </div>
        <div>
            <input type="number" name="items[${idx}][harga]"
                class="form-control price-input"
                placeholder="0" min="0" step="1" required>
        </div>
        <div class="subtotal-display empty" id="subtotal-${idx}">—</div>
        <button type="button" class="btn-remove-row" title="Hapus baris">
            <i class="fas fa-xmark"></i>
        </button>
    `;

    const qtyInput   = row.querySelector('.qty-input');
    const priceInput = row.querySelector('.price-input');
    const subtotalEl = row.querySelector(`#subtotal-${idx}`);

    function updateSubtotal() {
        const qty   = parseFloat(qtyInput.value)   || 0;
        const price = parseFloat(priceInput.value) || 0;
        const sub   = qty * price;
        if (sub > 0) {
            subtotalEl.textContent = fmt(sub);
            subtotalEl.classList.remove('empty');
        } else {
            subtotalEl.textContent = '—';
            subtotalEl.classList.add('empty');
        }
        updateGrandTotal();
    }

    qtyInput.addEventListener('input', updateSubtotal);
    priceInput.addEventListener('input', updateSubtotal);

    row.querySelector('.btn-remove-row').addEventListener('click', () => {
        row.remove();
        updateGrandTotal();
        updateRowCount();
    });

    return row;
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        total += qty * price;
    });
    document.getElementById('grand-total').textContent = fmt(total);
}

function updateRowCount() {
    const count = document.querySelectorAll('.item-row').length;
    document.getElementById('row-count').textContent = count + ' produk';
}

function addRow() {
    const wrapper = document.getElementById('items-wrapper');
    const row = createRow(rowIndex++);
    wrapper.appendChild(row);

    row.style.opacity = '0';
    row.style.transform = 'translateY(8px)';
    requestAnimationFrame(() => {
        row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
    });

    setTimeout(() => row.querySelector('select')?.focus(), 50);
    updateRowCount();
}

document.getElementById('btn-add-row').addEventListener('click', addRow);

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('supplier_id').addEventListener('change', async function () {
        const supplierId = this.value;
        supplierProducts = [];

        // Jika supplier dikosongkan kembali, kunci semua pilihan produk
        if (!supplierId) {
            document.querySelectorAll('.product-select').forEach(select => {
                select.innerHTML = '<option value="">⚠️ Pilih Supplier Terlebih Dahulu</option>';
                select.disabled = true;
            });
            return;
        }

        try {
            const response = await fetch(`/deliveries/get-products/${supplierId}`);
            supplierProducts = await response.json();

            document.querySelectorAll('.product-select').forEach(select => {
                const currentVal = select.value;
                select.innerHTML = '<option value="">— Pilih Produk —</option>';

                supplierProducts.forEach(product => {
                    select.innerHTML += `
                        <option value="${product.id}">
                            ${product.nama_produk}
                        </option>
                    `;
                });
                select.value = currentVal;
                select.disabled = false; // Buka kunci pilihan produk karena supplier sudah valid
            });
        } catch (error) {
            console.error('Gagal mengambil produk supplier:', error);
        }
    });

    const oldItems = @json(old('items', []));

    if (oldItems && Object.keys(oldItems).length > 0) {
        Object.values(oldItems).forEach(item => {
            addRow();
            const rows = document.querySelectorAll('.item-row');
            const last = rows[rows.length - 1];
            if (last) {
                last.querySelector('.product-select').value = item.product_id   || '';
                last.querySelector('.qty-input').value      = item.jumlah_kirim || '';
                last.querySelector('.price-input').value    = item.harga        || '';
                last.querySelector('.qty-input').dispatchEvent(new Event('input'));
            }
        });
    } else {
        addRow();
    }
});

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        document.getElementById('deliveryForm').submit();
    }
});

document.getElementById('deliveryForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 produk sebelum menyimpan.');
        return;
    }
    let valid = true;
    rows.forEach(row => {
        const prod  = row.querySelector('.product-select').value;
        const qty   = row.querySelector('.qty-input').value;
        const price = row.querySelector('.price-input').value;
        if (!prod || !qty || !price) valid = false;
    });
    if (!valid) {
        e.preventDefault();
        alert('Lengkapi semua kolom produk (Produk, Jumlah, Harga).');
    }
});
</script>
@endpush
