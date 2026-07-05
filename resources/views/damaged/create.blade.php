@extends('layouts.app')
@section('title', 'Tambah Barang Rusak')
@section('content')
<div class="card shadow">
    <div class="card-header bg-white">
        <h5>Form Barang Rusak</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('damaged.store') }}">
            @csrf
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="mb-3">
                <label>Supplier (opsional)</label>
                <select name="supplier_id" id="supplier_id" class="form-select">
                    <option value="">-- Tidak perlu supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Produk</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" data-supplier="{{ $p->supplier_id ?? '' }}">
                        {{ $p->nama_produk }} (Stok: {{ $p->current_stock }})
                    </option>
                    @endforeach
                </select>
                <small class="text-muted" id="product_hint"></small>
            </div>

            <div class="mb-3">
                <label>Jumlah Rusak</label>
                <input type="number" name="jumlah" class="form-control" required min="1">
            </div>
            <div class="mb-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" placeholder="misal: expired, pecah">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('damaged.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const supplierSelect = document.getElementById('supplier_id');
    const productSelect  = document.getElementById('product_id');
    const hint = document.getElementById('product_hint');

    // Simpan semua option asli sekali di awal
    const allOptions = Array.from(productSelect.options);

    function filterProducts() {
        const supplierId = supplierSelect.value;
        const currentValue = productSelect.value;

        // Kosongkan select, isi ulang sesuai filter
        productSelect.innerHTML = '';

        const placeholder = new Option('Pilih Produk', '');
        productSelect.appendChild(placeholder);

        let matchCount = 0;

        allOptions.forEach(opt => {
            if (opt.value === '') return; // skip placeholder lama

            const productSupplierId = opt.dataset.supplier || '';

            // Jika belum pilih supplier -> tampilkan semua produk
            // Jika sudah pilih supplier -> tampilkan hanya produk milik supplier itu
            if (supplierId === '' || productSupplierId === supplierId) {
                productSelect.appendChild(opt.cloneNode(true));
                matchCount++;
            }
        });

        // Kalau produk yang tadinya dipilih masih ada di list baru, pertahankan
        const stillExists = Array.from(productSelect.options).some(o => o.value === currentValue);
        productSelect.value = stillExists ? currentValue : '';

        if (supplierId !== '' && matchCount === 0) {
            hint.textContent = 'Supplier ini belum punya produk terdaftar.';
        } else {
            hint.textContent = '';
        }
    }

    supplierSelect.addEventListener('change', filterProducts);
});
</script>
@endsection
