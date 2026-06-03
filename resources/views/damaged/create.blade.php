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
                <label>Produk</label>
                <select name="product_id" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_produk }} (Stok: {{ $p->current_stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Supplier (opsional)</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- Tidak perlu supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
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
@endsection