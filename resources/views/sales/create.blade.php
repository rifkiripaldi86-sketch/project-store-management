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
            <div class="field-group" style="max-width:260px; margin-bottom:24px;">
                <label class="field-label" for="tanggal">
                    Tanggal <span class="required">*</span>
                </label>
                <input type="date" name="tanggal" id="tanggal"
                    class="form-control @error('tanggal') is-invalid @enderror"
                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
                {{-- Server‑side rows (old input or initial empty row) --}}
                @php
                    $oldItems = old('items', []);
                    $hasOldItems = count($oldItems) > 0;
                @endphp

                @if($hasOldItems)
                    @foreach($oldItems as $idx => $item)
                        <div class="item-row" data-idx="{{ $idx }}">
                            <div>
                                <select name="items[{{ $idx }}][product_id]" class="form-select product-select" required>
                                    <option value="">— Pilih Produk —</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stok="{{ $product->current_stock }}"
                                            {{ (string)$item['product_id'] === (string)$product->id ? 'selected' : '' }}>
                                            {{ $product->nama_produk }} (Stok: {{ $product->current_stock }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="stok-hint" id="stok-hint-{{ $idx }}"></div>
                            </div>
                            <div>
                                <input type="number" name="items[{{ $idx }}][laku]"
                                    class="form-control qty-input" placeholder="0" min="1" step="1"
                                    value="{{ $item['laku'] ?? '' }}" required>
                            </div>
                            <div>
                                <input type="number" name="items[{{ $idx }}][harga_jual]"
                                    class="form-control price-input" placeholder="0" min="0" step="0.01"
                                    value="{{ $item['harga_jual'] ?? '' }}" required>
                            </div>
                            <div class="subtotal-display empty" id="subtotal-{{ $idx }}">—</div>
                            <button type="button" class="btn-remove-row" title="Hapus baris">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    {{-- One empty row as default --}}
                    <div class="item-row" data-idx="0">
                        <div>
                            <select name="items[0][product_id]" class="form-select product-select" required>
                                <option value="">— Pilih Produk —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-stok="{{ $product->current_stock }}">
                                        {{ $product->nama_produk }} (Stok: {{ $product->current_stock }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="stok-hint" id="stok-hint-0"></div>
                        </div>
                        <div>
                            <input type="number" name="items[0][laku]" class="form-control qty-input"
                                placeholder="0" min="1" step="1" required>
                        </div>
                        <div>
                            <input type="number" name="items[0][harga_jual]" class="form-control price-input"
                                placeholder="0" min="0" step="0.01" required>
                        </div>
                        <div class="subtotal-display empty" id="subtotal-0">—</div>
                        <button type="button" class="btn-remove-row" title="Hapus baris">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
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
        <span style="margin-left:auto; font-size:12px; color:var(--ink-muted); display:flex; align-items:center; gap:5px;">
            <kbd class="kbd">Ctrl</kbd> + <kbd class="kbd">Enter</kbd> untuk simpan
        </span>
    </div>
</div>

{{-- Template for cloning new rows --}}
<template id="item-row-template">
    <div class="item-row" data-idx="__INDEX__">
        <div>
            <select name="items[__INDEX__][product_id]" class="form-select product-select" required>
                <option value="">— Pilih Produk —</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-stok="{{ $product->current_stock }}">
                        {{ $product->nama_produk }} (Stok: {{ $product->current_stock }})
                    </option>
                @endforeach
            </select>
            <div class="stok-hint" id="stok-hint-__INDEX__"></div>
        </div>
        <div>
            <input type="number" name="items[__INDEX__][laku]" class="form-control qty-input"
                placeholder="0" min="1" step="1" required>
        </div>
        <div>
            <input type="number" name="items[__INDEX__][harga_jual]" class="form-control price-input"
                placeholder="0" min="0" step="0.01" required>
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
    // Helper: format Rupiah
    const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));

    // Update subtotal for a single row
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

    // Update grand total and row count
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

    // Setup event listeners for a single row (stock hint & subtotal)
    function bindRowEvents(row, idx) {
        const select     = row.querySelector('.product-select');
        const qtyInput   = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const stokHint   = row.querySelector('.stok-hint');

        // Stock hint on product change
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

        // Subtotal update on quantity/price change
        const recalc = () => {
            updateRowSubtotal(row);
            updateGrandTotalAndCount();
        };
        qtyInput.addEventListener('input', recalc);
        priceInput.addEventListener('input', recalc);

        // Remove row button
        const removeBtn = row.querySelector('.btn-remove-row');
        removeBtn.addEventListener('click', () => {
            row.remove();
            updateGrandTotalAndCount();
        });

        // Trigger initial stock hint if a product is preselected
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        } else {
            recalc(); // just to ensure subtotal reflects any pre-filled values
        }
    }

    // Add a new row by cloning the template
    let nextIndex = {{ count(old('items', [])) ?: 1 }}; // continue from existing rows count

    function addNewRow() {
        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        const row = clone.firstElementChild; // the .item-row div

        // Replace __INDEX__ placeholders
        const currentIdx = nextIndex++;
        row.innerHTML = row.innerHTML.replace(/__INDEX__/g, currentIdx);
        // Re-query elements inside the row after innerHTML replacement
        const newRow = row;
        // Append to wrapper
        document.getElementById('items-wrapper').appendChild(newRow);
        // Bind events
        bindRowEvents(newRow, currentIdx);

        // Animation
        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(8px)';
        requestAnimationFrame(() => {
            newRow.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        });

        newRow.querySelector('select')?.focus();
        updateGrandTotalAndCount();
    }

    // Attach global add button
    document.getElementById('btn-add-row').addEventListener('click', addNewRow);

    // Initialize existing rows: bind events and recalc totals
    document.querySelectorAll('.item-row').forEach((row, idx) => {
        // Ensure the row has a proper data-idx attribute (maybe missing from server-side rows)
        if (!row.dataset.idx) row.dataset.idx = idx;
        bindRowEvents(row, idx);
    });

    // Final grand total and count update after everything is loaded
    updateGrandTotalAndCount();

    // Ctrl+Enter submit
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            document.getElementById('saleForm').submit();
        }
    });

    // Submit guard: at least one complete row
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
            alert('Lengkapi semua kolom produk (Produk, Jumlah, Harga Jual).');
        }
    });
</script>
@endpush