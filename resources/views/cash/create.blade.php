@extends('layouts.app')
@section('title', 'Catat Kas')

@push('styles')
<style>
    /* ── Page layout ── */
    .page-header {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 24px;
    }
    .back-btn {
        width: 34px; height: 34px;
        border: 1px solid var(--border); border-radius: var(--r-sm);
        background: var(--surface); color: var(--ink-soft);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; text-decoration: none;
        transition: all 0.15s;
    }
    .back-btn:hover { background: var(--bg); color: var(--ink); }
    .page-header h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 700; color: var(--ink);
        margin: 0 0 2px; letter-spacing: -0.3px;
    }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* ── Form card ── */
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        max-width: 680px;
    }

    .form-card-header {
        padding: 20px 28px 18px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        display: flex; align-items: center; gap: 12px;
    }
    .form-card-header-icon {
        width: 40px; height: 40px;
        background: var(--accent-soft);
        border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: var(--accent);
    }
    .form-card-header-text h5 {
        font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 2px;
    }
    .form-card-header-text p {
        font-size: 12px; color: var(--ink-muted); margin: 0;
    }

    /* ── Form body ── */
    .form-body { padding: 26px 28px; }

    /* Field groups */
    .field-group {
        display: flex; flex-direction: column; gap: 5px;
        margin-bottom: 20px;
    }
    .field-label {
        font-size: 12.5px; font-weight: 600; color: var(--ink-soft);
        display: flex; align-items: center; gap: 4px;
    }
    .field-label .required { color: #ef4444; }

    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        font-size: 13.5px; color: var(--ink);
        padding: 9px 13px; height: auto;
        background: var(--surface);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
        outline: none;
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
    .invalid-feedback { font-size: 12px; color: #dc2626; margin-top: 4px; }
    .field-hint { font-size: 11.5px; color: var(--ink-muted); }

    /* ── Tipe selector ── */
    .tipe-selector { display: flex; gap: 10px; }
    .tipe-option {
        flex: 1; position: relative;
    }
    .tipe-option input[type="radio"] {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
    .tipe-label {
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; padding: 14px 16px;
        border: 1.5px solid var(--border); border-radius: var(--r-md);
        cursor: pointer; transition: all 0.2s;
        background: var(--surface);
    }
    .tipe-label:hover { border-color: #d1d5db; background: var(--bg); }
    .tipe-icon {
        width: 38px; height: 38px; border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; transition: all 0.2s;
    }
    .tipe-text { font-size: 13px; font-weight: 600; color: var(--ink-soft); }

    /* Masuk selected */
    .tipe-option input:checked + .tipe-label.masuk {
        border-color: #10b981; background: #f0fdf4;
    }
    .tipe-option input:checked + .tipe-label.masuk .tipe-icon {
        background: #10b981; color: #fff;
    }
    .tipe-option input:checked + .tipe-label.masuk .tipe-text { color: #16a34a; }

    /* Keluar selected */
    .tipe-option input:checked + .tipe-label.keluar {
        border-color: #ef4444; background: #fef2f2;
    }
    .tipe-option input:checked + .tipe-label.keluar .tipe-icon {
        background: #ef4444; color: #fff;
    }
    .tipe-option input:checked + .tipe-label.keluar .tipe-text { color: #dc2626; }

    /* Unselected icons */
    .tipe-label.masuk  .tipe-icon { background: #f0fdf4; color: #10b981; }
    .tipe-label.keluar .tipe-icon { background: #fef2f2; color: #ef4444; }

    /* ── Amount input ── */
    .amount-wrap {
        position: relative;
    }
    .amount-prefix {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        font-size: 13.5px; font-weight: 600; color: var(--ink-soft);
        pointer-events: none; user-select: none;
    }
    .amount-input {
        padding-left: 42px !important;
        font-family: 'Sora', sans-serif;
        font-size: 18px !important; font-weight: 700;
        letter-spacing: -0.3px;
        height: 52px !important;
    }
    .amount-display {
        font-size: 12px; color: var(--ink-muted); margin-top: 5px;
        min-height: 18px; transition: color 0.2s;
    }

    /* ── Two-column row ── */
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 520px) { .field-row { grid-template-columns: 1fr; } }

    /* ── Divider ── */
    .form-divider {
        border: none; border-top: 1px solid var(--border);
        margin: 22px 0;
    }

    /* ── Footer ── */
    .form-footer {
        padding: 16px 28px;
        border-top: 1px solid var(--border);
        background: var(--bg);
        display: flex; align-items: center; gap: 10px;
        flex-wrap: wrap;
    }

    /* Keyboard shortcut hint */
    .kbd {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 2px 6px; border: 1px solid #d1d5db;
        border-radius: 4px; font-size: 10.5px; font-weight: 600;
        color: var(--ink-soft); background: #fff; letter-spacing: 0.3px;
        font-family: monospace;
    }

    /* Animate */
    .form-card { animation: fadeSlideUp 0.4s 0.05s ease both; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <a href="{{ route('cash.index') }}" class="back-btn" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1>Catat Kas</h1>
        <p>Tambah entri kas masuk atau keluar</p>
    </div>
</div>

<div class="form-card">
    {{-- Card Header --}}
    <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-pen-to-square"></i></div>
        <div class="form-card-header-text">
            <h5>Form Kas Harian</h5>
            <p>Semua kolom bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>
    </div>

    {{-- Form Body --}}
    <div class="form-body">
        <form method="POST" action="{{ route('cash.store') }}" id="kasForm">
            @csrf

            {{-- Tipe Kas --}}
            <div class="field-group">
                <label class="field-label">
                    Tipe Kas <span class="required">*</span>
                </label>
                <div class="tipe-selector">
                    <div class="tipe-option">
                        <input type="radio" name="tipe" id="tipe-masuk" value="masuk"
                            {{ old('tipe', 'masuk') == 'masuk' ? 'checked' : '' }} required>
                        <label for="tipe-masuk" class="tipe-label masuk">
                            <div class="tipe-icon"><i class="fas fa-arrow-trend-up"></i></div>
                            <span class="tipe-text">Kas Masuk</span>
                        </label>
                    </div>
                    <div class="tipe-option">
                        <input type="radio" name="tipe" id="tipe-keluar" value="keluar"
                            {{ old('tipe') == 'keluar' ? 'checked' : '' }}>
                        <label for="tipe-keluar" class="tipe-label keluar">
                            <div class="tipe-icon"><i class="fas fa-arrow-trend-down"></i></div>
                            <span class="tipe-text">Kas Keluar</span>
                        </label>
                    </div>
                </div>
                @error('tipe')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <hr class="form-divider">

            {{-- Tanggal & Kategori --}}
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label" for="tanggal">
                        Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="form-control @error('tanggal') is-invalid @enderror"
                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="kategori">
                        Kategori <span class="required">*</span>
                    </label>
                    <select name="kategori" id="kategori"
                        class="form-select @error('kategori') is-invalid @enderror" required>
                        <option value="">— Pilih kategori —</option>
                        <option value="penjualan"      {{ old('kategori') == 'penjualan'      ? 'selected' : '' }}>Penjualan</option>
                        <option value="bayar_supplier" {{ old('kategori') == 'bayar_supplier' ? 'selected' : '' }}>Bayar Supplier</option>
                        <option value="operasional"    {{ old('kategori') == 'operasional'    ? 'selected' : '' }}>Operasional</option>
                        <option value="lainnya"        {{ old('kategori') == 'lainnya'        ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Jumlah --}}
            <div class="field-group">
                <label class="field-label" for="jumlah">
                    Jumlah <span class="required">*</span>
                </label>
                <div class="amount-wrap">
                    <span class="amount-prefix">Rp</span>
                    <input type="number" name="jumlah" id="jumlah"
                        class="form-control amount-input @error('jumlah') is-invalid @enderror"
                        value="{{ old('jumlah') }}"
                        min="1" step="1" placeholder="0"
                        autocomplete="off" required>
                </div>
                <div class="amount-display" id="amount-terbilang">—</div>
                @error('jumlah')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="field-group">
                <label class="field-label" for="keterangan">
                    Keterangan
                    <span style="margin-left:4px; font-size:11px; font-weight:400; color:var(--ink-muted);">(opsional)</span>
                </label>
                <input type="text" name="keterangan" id="keterangan"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    value="{{ old('keterangan') }}"
                    placeholder="Contoh: Setoran penjualan siang, Bayar listrik…"
                    maxlength="255">
                <span class="field-hint">Maks. 255 karakter</span>
                @error('keterangan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

        </form>
    </div>

    {{-- Footer --}}
    <div class="form-footer">
        <button type="submit" form="kasForm" class="btn btn-primary d-flex align-items-center gap-2"
            style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 20px;">
            <i class="fas fa-floppy-disk" style="font-size:13px;"></i>
            Simpan
        </button>
        <a href="{{ route('cash.index') }}" class="btn"
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
/* ── Amount formatter display ── */
const jumlahInput = document.getElementById('jumlah');
const amountDisplay = document.getElementById('amount-terbilang');

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
}

function updateDisplay() {
    const val = parseFloat(jumlahInput.value);
    if (!val || isNaN(val) || val <= 0) {
        amountDisplay.textContent = '—';
        amountDisplay.style.color = '';
        return;
    }
    amountDisplay.textContent = formatRupiah(val);
    amountDisplay.style.color = 'var(--accent)';
    amountDisplay.style.fontWeight = '600';
}

jumlahInput.addEventListener('input', updateDisplay);
updateDisplay(); // init if old value

/* ── Ctrl+Enter submit ── */
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        document.getElementById('kasForm').submit();
    }
});

/* ── Category auto-select based on tipe ── */
const tipeInputs   = document.querySelectorAll('input[name="tipe"]');
const kategoriSel  = document.getElementById('kategori');

const defaults = { masuk: 'penjualan', keluar: 'operasional' };

tipeInputs.forEach(input => {
    input.addEventListener('change', () => {
        // Only auto-switch if user hasn't manually picked a category
        if (!kategoriSel.dataset.userPicked) {
            kategoriSel.value = defaults[input.value] || '';
        }
    });
});

kategoriSel.addEventListener('change', () => {
    kategoriSel.dataset.userPicked = '1';
});
</script>
@endpush