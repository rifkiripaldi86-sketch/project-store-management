@extends('layouts.app')
@section('title', 'Satuan Barang')

@section('content')
<style>
    /* Animasi Muncul */
    .fade-in { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* Animasi Menghilang (Untuk Notifikasi) */
    .fade-out { animation: fadeOut 0.5s ease-in-out forwards; }
    @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
</style>

<div class="fade-in">
    @if(session('success'))
        <div id="alert-box" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h4>Satuan Barang</h4>
        <a href="{{ route('units.create') }}" class="btn btn-primary">+ Tambah Satuan</a>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-balance-scale me-1"></i> Daftar Satuan Barang</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>NAMA SATUAN</th>
                        <th>KETERANGAN</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $index => $unit)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Script otomatis menghilangkan notifikasi setelah 3 detik
    setTimeout(function() {
        var alert = document.getElementById('alert-box');
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);
</script>
@endsection
