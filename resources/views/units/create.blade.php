@extends('layouts.app')
@section('title', 'Tambah Satuan Barang')

@section('content')
<style>
    .fade-in { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-plus me-1"></i> Tambah Satuan Barang</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Satuan</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Pcs, Kg, Liter" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('units.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Satuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
