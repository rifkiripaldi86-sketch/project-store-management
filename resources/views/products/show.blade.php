@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
<div class="card shadow">
    <div class="card-header bg-info text-white">Detail Produk</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Nama Produk</th><td>{{ $product->nama_produk }}</td></tr>
            <tr><th>Stok Saat Ini</th><td>{{ $product->current_stock }}</td></tr>
            <tr><th>Dibuat</th><td>{{ $product->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection