@extends('layouts.app')
@section('title', 'Barang Rusak')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fas fa-exclamation-triangle me-2"></i> Barang Rusak/Reject/Basi</h3>
    <a href="{{ route('damaged.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Rusak</a>
</div>
<form method="GET" class="mb-3">
    <div class="row g-2 align-items-center">

        {{-- Search --}}
        <div class="col-md-4">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Cari produk / supplier...">
        </div>

        {{-- Supplier Filter --}}
        <div class="col-md-3">
            <select name="supplier" class="form-control">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}"
                        {{ request('supplier') == $s->id ? 'selected' : '' }}>
                        {{ $s->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date From --}}
        <div class="col-md-2">
            <input type="date"
                   name="from"
                   value="{{ request('from') }}"
                   class="form-control">
        </div>

        {{-- Date To --}}
        <div class="col-md-2">
            <input type="date"
                   name="to"
                   value="{{ request('to') }}"
                   class="form-control">
        </div>

        {{-- Button --}}
        <div class="col-md-1 d-grid">
            <button class="btn btn-primary">
                <i class="fas fa-filter"></i>
            </button>
        </div>

    </div>
</form>
<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr><th>No</th><th>Tanggal</th><th>Produk</th><th>Supplier</th><th>Jumlah</th><th>Keterangan</th><th>Input Oleh</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($damaged as $index => $d)
                <tr>
                    <td>{{ $index + $damaged->firstItem() }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $d->product->nama_produk }}</td>
                    <td>{{ $d->supplier ? $d->supplier->nama_supplier : '-' }}</td>
                    <td>{{ $d->jumlah }}</td>
                    <td>{{ $d->keterangan ?? '-' }}</td>
                    <td>{{ $d->createdBy->name }}</td>
                    <td>
                        <form action="{{ route('damaged.destroy', $d) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">Belum ada data rusak</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $damaged->links() }}
    </div>
</div>
@endsection
