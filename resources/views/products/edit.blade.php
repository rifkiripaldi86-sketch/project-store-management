@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div class="card shadow">
    <div class="card-header bg-white">
        <h5><i class="fas fa-edit me-1"></i> Edit Produk</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')

            {{-- Input Nama Produk --}}
            <div class="mb-3">
                <label>Nama Produk <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nama_produk"
                    class="form-control @error('nama_produk') is-invalid @enderror"
                    value="{{ old('nama_produk', $product->nama_produk) }}"
                    required
                >
                @error('nama_produk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Dropdown Supplier Resmi --}}
            <div class="mb-3">
                <label for="supplier_id" class="form-label">
                    Supplier Resmi <span class="text-danger">*</span>
                </label>
                <select
                    name="supplier_id"
                    id="supplier_id"
                    class="form-select @error('supplier_id') is-invalid @enderror"
                    required
                >
                    <option value="">— Pilih Supplier —</option>
                    @foreach($suppliers as $supplier)
                        <option
                            value="{{ $supplier->id }}"
                            {{ (old('supplier_id', $product->supplier_id) == $supplier->id) ? 'selected' : '' }}
                        >
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Stok (Bisa diubah manual/tidak di-disabled lagi) --}}
            <div class="mb-3">
                <label for="current_stock">Stok Saat Ini <span class="text-danger">*</span></label>
                <input
                    type="number"
                    name="current_stock"
                    id="current_stock"
                    class="form-control @error('current_stock') is-invalid @enderror"
                    value="{{ old('current_stock', $product->current_stock) }}"
                    min="0"
                    required
                >
                <small class="text-muted">
                    Ubah angka di atas jika ingin menyesuaikan stok secara manual.
                </small>
                @error('current_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
