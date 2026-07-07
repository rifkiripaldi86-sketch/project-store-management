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
                <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Input Oleh</th>
                <th>Aksi</th></tr>
            </thead>
            <tbody>

@forelse($damaged as $group)

@php
    $first = $group->first();
@endphp

<tr>

    <td>{{ $loop->iteration }}</td>

    <td>
        {{ \Carbon\Carbon::parse($first->tanggal)->format('d/m/Y') }}
    </td>

    <td>
        {{ optional($first->supplier)->nama_supplier ?? '-' }}
    </td>

    <td>
        @foreach($group as $item)
            {{ $item->product->nama_produk }}<br>
        @endforeach
    </td>

    <td>
        @foreach($group as $item)
            {{ $item->jumlah }}<br>
        @endforeach
    </td>

    <td>
        @foreach($group as $item)
            {{ $item->keterangan ?? '-' }}<br>
        @endforeach
    </td>

    <td>
        {{ $first->createdBy->name }}
    </td>

    <td>

<form action="{{ route('damaged.destroyGroup') }}"
      method="POST"
      onsubmit="return confirm('Hapus semua data pada grup ini?')">

    @csrf
    @method('DELETE')

    <input type="hidden"
           name="tanggal"
           value="{{ $first->tanggal }}">

    <input type="hidden"
           name="supplier_id"
           value="{{ $first->supplier_id }}">

    <button class="btn btn-danger btn-sm">
        Hapus
    </button>

</form>

</td>

</tr>

@empty

<tr>
    <td colspan="8" class="text-center">
        Belum ada data
    </td>
</tr>

@endforelse

</tbody>
        </table>
    </div>
</div>
@endsection
