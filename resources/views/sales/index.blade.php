@extends('layouts.app')
@section('title', 'Data Penjualan')

@push('styles')
<style>
    /* Header */
    .page-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .page-header-left { display: flex; align-items: center; gap: 12px; }
    .page-header h1 { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 2px; letter-spacing: -0.3px; }
    .page-header p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
    .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 18px 20px; box-shadow: var(--shadow-sm); }
    .stat-label { font-size: 11.5px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: var(--ink-muted); margin-bottom: 6px; }
    .stat-value { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); }
    .stat-sub { font-size: 12px; color: var(--ink-muted); margin-top: 3px; }

    /* Filter */
    .filter-card { background: var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);padding:14px 18px;margin-bottom:18px;box-shadow:var(--shadow-sm); }
    .filter-form { display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px; align-items:center; }
    .search-box { position:relative; }
    .search-box i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--ink-muted); }
    .search-box input { padding-left:40px; }
    .filter-form input,
    .filter-form select { height:40px; border:1px solid var(--border); border-radius:10px; padding:0 12px; width:100%; }

    .filter-btn{
    height:40px;
    padding:0 20px;
    border:none;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    font-weight:600;
}

@media(max-width:992px){
    .filter-form{
        grid-template-columns:1fr;
    }
}

    /* ── Filter bar ── */
    .filter-bar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 14px 18px;
        display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
    }
    .filter-bar .form-control,
    .filter-bar .form-select {
        font-size: 13px; border-color: var(--border); border-radius: var(--r-sm);
        height: 36px; padding: 0 10px; color: var(--ink);
    }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,78,216,0.08);
    }
    .filter-bar label {
        font-size: 12px; font-weight: 600; color: var(--ink-soft); white-space: nowrap;
    }

    /* Table */S]
    .main-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
    .main-card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); background: var(--bg); display: flex; align-items: center; gap: 10px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { font-size: 10.5px; font-weight: 700; letter-spacing: 0.7px; text-transform: uppercase; color: var(--ink-muted); padding: 11px 16px; background: var(--bg); border-bottom: 1px solid var(--border); }
    .data-table tbody td { padding: 13px 16px; border-bottom: 1px solid var(--border); font-size: 13.5px; }
    .kasir-badge { padding: 3px 10px; border-radius: 99px; background: var(--bg); border: 1px solid var(--border); font-size: 12px; }
    .action-btn { width: 32px; height: 32px; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--ink-soft); display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.15s; }
    .action-btn:hover { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }
    .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--border); background: var(--bg); }
</style>
@endpush

@section('content')

<div class="page-header animate-in">
    <div class="page-header-left">
        <div>
            <h1>Data Penjualan</h1>
            <p>Rekap seluruh transaksi penjualan</p>
        </div>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height:38px; font-weight:600; border-radius:var(--r-sm);">
        <i class="fas fa-plus"></i> Tambah Penjualan
    </a>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ $sales->total() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-rata</div>
        <div class="stat-value">Rp {{ $sales->total() > 0 ? number_format(($totalPendapatan ?? 0) / $sales->total(), 0, ',', '.') : '0' }}</div>
    </div>
</div>

<div class="filter-card">

    <form method="GET" action="{{ route('sales.index') }}" class="filter-form">

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari nomor transaksi..."
                value="{{ request('search') }}">
        </div>

        <select name="kasir" class="form-select">
            <option value="">Semua Kasir</option>

            @foreach($kasirs as $kasir)
                <option
                    value="{{ $kasir->id }}"
                    {{ request('kasir')==$kasir->id ? 'selected':'' }}>
                    {{ $kasir->name }}
                </option>
            @endforeach
        </select>

        <input
            type="date"
            name="from"
            value="{{ request('from') }}">

        <input
            type="date"
            name="to"
            value="{{ request('to') }}">

        <button class="filter-btn">
            <i class="fas fa-filter me-1"></i>
            Filter
        </button>

    </form>

</div>

<div class="main-card">
    <div class="main-card-header"><h5>Daftar Penjualan</h5></div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total Bayar</th>
                    <th>Kasir</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $index => $sale)
                <tr>
                    <td>{{ $index + $sales->firstItem() }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>
    @php
        $suppliers = $sale->items
            ->pluck('product.supplier.nama_supplier')
            ->filter()
            ->unique();
    @endphp

    @if($suppliers->count())
        {{ $suppliers->implode(', ') }}
    @else
        -
    @endif
</td>
                    <td style="font-weight:600;">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</td>
                    <td><span class="kasir-badge">{{ $sale->createdBy->name ?? '-' }}</span></td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('sales.show', $sale) }}" class="action-btn" title="Detail"><i class="fas fa-eye"></i></a>
                            <button type="button" class="action-btn" style="color:#dc2626;" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $sale->id }}" title="Hapus">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:30px;">Tidak ada data penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($sales->hasPages())
    <div class="pagination-wrap">{{ $sales->links() }}</div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Hapus Penjualan</h5></div>
            <div class="modal-body">Anda yakin ingin menghapus data ini? Stok akan dikembalikan dan catatan kas akan dihapus.</div>
            <div class="modal-footer">
                <form id="delete-form" method="POST" action="">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
        const id = e.relatedTarget.getAttribute('data-id');
        document.getElementById('delete-form').action = '/sales/' + id;
    });
</script>
@endpush
