@extends('layouts.app')
@section('title', 'Edit Satuan Barang')

@section('content')
<style>
    .fade-in { animation: fadeIn 0.7s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-1"></i> Edit Satuan Barang</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.update', $unit->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Satuan</label>
                            <input type="text" name="name" class="form-control" value="{{ $unit->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ $unit->keterangan }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('units.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update Satuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
