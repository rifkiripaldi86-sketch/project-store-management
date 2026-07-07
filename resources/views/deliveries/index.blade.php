@extends('layouts.app')
@section('title', 'Kiriman Supplier')

@push('styles')
<style>
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 24px;
    }
    .page-header-left h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 700; color: var(--ink);
        margin: 0 0 4px; letter-spacing: -0.3px;
    }
    .page-header-left p { font-size: 13px; color: var(--ink-muted); margin: 0; }

    /* Summary cards */
    .summary-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-lg); padding: 20px;
        display: flex; flex-direction: column; gap: 10px;
        box-shadow: var(--shadow-sm); position: relative; overflow: hidden;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .summary-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .summary-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: var(--r-lg) var(--r-lg) 0 0;
        background: var(--card-accent, var(--accent));
    }
    .summary-card.blue   { --card-accent: #3b82f6; }
    .summary-card.violet { --card-accent: #8b5cf6; }
    .summary-card.amber  { --card-accent: #f59e0b; }
    .summary-top { display: flex; align-items: center; justify-content: space-between; }
    .summary-label {
        font-size: 11px; font-weight: 600; letter-spacing: 0.6px;
        text-transform: uppercase; color: var(--ink-soft);
    }
    .summary-icon {
        width: 34px; height: 34px; border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center; font-size: 14px;
    }
    .summary-card.blue   .summary-icon { background: #eff6ff; color: #3b82f6; }
    .summary-card.violet .summary-icon { background: #f5f3ff; color: #8b5cf6; }
    .summary-card.amber  .summary-icon { background: #fffbeb; color: #f59e0b; }
    .summary-value {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 700; color: var(--ink);
        letter-spacing: -0.5px; line-height: 1;
    }
    .summary-sub { font-size: 12px; color: var(--ink-muted); }

    /* Filter bar */
    .filter-bar {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-lg); padding: 14px 18px;
        display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
        margin-bottom: 16px; box-shadow: var(--shadow-sm);
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
    .filter-bar label { font-size: 12px; font-weight: 600; color: var(--ink-soft); white-space: nowrap; }

    /* Table card */
    .table-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-lg); box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .table-card-header {
        padding: 14px 18px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    .table-card-title { font-size: 14px; font-weight: 600; color: var(--ink); }
    .table-count {
        font-size: 12px; color: var(--ink-muted);
        background: var(--bg); border: 1px solid var(--border);
        padding: 2px 10px; border-radius: 99px;
    }
    .data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .data-table thead th {
        background: var(--bg);
        font-size: 10.5px; font-weight: 700; letter-spacing: 0.8px;
        text-transform: uppercase; color: var(--ink-muted);
        padding: 10px 16px; border-bottom: 1px solid var(--border); white-space: nowrap;
    }
    .data-table tbody td {
        padding: 13px 16px; border-bottom: 1px solid #f1f3f5;
        color: var(--ink); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #fafbfc; }

    /* Supplier chip */
    .supplier-chip {
        display: inline-flex; align-items: center; gap: 7px;
    }
    .supplier-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--accent); flex-shrink: 0;
    }

    /* Item count badge */
    .item-count {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--bg); border: 1px solid var(--border);
        padding: 3px 9px; border-radius: 99px;
        font-size: 12px; font-weight: 600; color: var(--ink-soft);
    }

    /* Action buttons */
    .btn-action {
        width: 30px; height: 30px; border-radius: var(--r-sm);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; border: 1px solid var(--border);
        background: var(--surface); color: var(--ink-soft);
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .btn-action:hover { background: var(--bg); color: var(--ink); }
    .btn-action.info:hover  { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
    .btn-action.danger:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

    /* Empty state */
    .empty-state { text-align: center; padding: 56px 20px; }
    .empty-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: var(--bg); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: var(--ink-muted); margin: 0 auto 16px;
    }
    .empty-state h6 { font-size: 15px; font-weight: 600; color: var(--ink); margin: 0 0 6px; }
    .empty-state p  { font-size: 13px; color: var(--ink-muted); margin: 0 0 18px; }

    /* Pagination */
    .pagination-wrap { padding: 12px 18px; border-top: 1px solid var(--border); }
    .pagination { margin: 0; gap: 4px; }
    .page-link {
        border-radius: var(--r-sm) !important; font-size: 13px;
        color: var(--ink-soft); border-color: var(--border);
        padding: 5px 11px; min-width: 34px; text-align: center;
    }
    .page-link:hover { background: var(--bg); color: var(--ink); border-color: var(--border); }
    .page-item.active .page-link { background: var(--accent); border-color: var(--accent); color: #fff; }
    .page-item.disabled .page-link { color: var(--ink-muted); }

    /* Modal */
    .modal-content { border: none; border-radius: var(--r-xl); box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--border); padding: 18px 22px; }
    .modal-body   { padding: 20px 22px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 14px 22px; gap: 8px; }
    .modal-title  { font-size: 15px; font-weight: 700; }

    /* Stagger */
    .animate-in { animation: fadeSlideUp 0.35s ease both; }
    .animate-in:nth-child(1) { animation-delay: 0.04s; }
    .animate-in:nth-child(2) { animation-delay: 0.10s; }
    .animate-in:nth-child(3) { animation-delay: 0.16s; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <div class="page-header-left">
        <h1><i class="fas fa-truck" style="font-size:18px; color:var(--accent); margin-right:8px;"></i>Kiriman Supplier</h1>
        <p>Pencatatan barang masuk dari supplier</p>
    </div>
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
       style="height:38px; border-radius:var(--r-sm); font-size:13.5px; font-weight:600;">
        <i class="fas fa-plus" style="font-size:11px;"></i> Tambah Kiriman
    </a>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4 animate-in">
        <div class="summary-card blue">
            <div class="summary-top">
                <span class="summary-label">Total Kiriman</span>
                <div class="summary-icon"><i class="fas fa-truck"></i></div>
            </div>
            <div class="summary-value">{{ $totalKiriman }}</div>
            <div class="summary-sub">Kiriman sesuai filter</div>
        </div>
    </div>
    <div class="col-12 col-sm-4 animate-in">
    <div class="summary-card amber">
        <div class="summary-top">
            <span class="summary-label">Stok Masuk</span>
            <div class="summary-icon"><i class="fas fa-arrow-down-wide-short"></i></div>
        </div>
        <div class="summary-value">
            {{ $totalStokMasuk }}
        </div>
        <div class="summary-sub">Total barang diterima</div>
    </div>
</div>
    <div class="col-12 col-sm-4 animate-in">
        <div class="summary-card amber">
            <div class="summary-top">
                <span class="summary-label">Total Nilai</span>
                <div class="summary-icon"><i class="fas fa-receipt"></i></div>
            </div>
            <div class="summary-value" data-count="{{ $totalNilai }}" data-prefix="Rp " data-format="currency">
                Rp {{ number_format($totalNilai, 0, ',', '.') }}
            </div>
            <div class="summary-sub">Nilai barang diterima</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('deliveries.index') }}" class="filter-bar animate-in">
    <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width:160px;">
        <i class="fas fa-magnifying-glass" style="color:var(--ink-muted); font-size:13px;"></i>
        <input type="text" name="search" class="form-control border-0 shadow-none ps-0"
            placeholder="Cari nama supplier…"
            value="{{ request('search') }}" style="height:30px; background:transparent;">
    </div>
    <div style="width:1px; height:24px; background:var(--border);"></div>
    <div class="d-flex align-items-center gap-2">
        <label>Supplier</label>
        <select name="supplier_id" class="form-select" style="width:160px;">
            <option value="">Semua</option>
            @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                    {{ $sup->nama_supplier }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="d-flex align-items-center gap-2">
        <label>Dari</label>
        <input type="date" name="dari" class="form-control" style="width:150px;" value="{{ request('dari') }}">
    </div>
    <div class="d-flex align-items-center gap-2">
        <label>S.d.</label>
        <input type="date" name="sampai" class="form-control" style="width:150px;" value="{{ request('sampai') }}">
    </div>
    <button type="submit" class="btn btn-primary"
        style="height:36px; font-size:13px; padding:0 16px; border-radius:var(--r-sm); flex-shrink:0;">
        <i class="fas fa-filter me-1" style="font-size:11px;"></i> Filter
    </button>
    @if(request()->hasAny(['search','supplier_id','dari','sampai']))
        <a href="{{ route('deliveries.index') }}" class="btn"
            style="height:36px; font-size:13px; padding:0 12px; border:1px solid var(--border); border-radius:var(--r-sm); color:var(--ink-soft); flex-shrink:0;">
            <i class="fas fa-xmark"></i>
        </a>
    @endif
</form>

{{-- Table --}}
<div class="table-card animate-in">
    <div class="table-card-header">
        <span class="table-card-title">Riwayat Kiriman</span>
        <span class="table-count">{{ $deliveries->total() }} kiriman</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:44px;">No. </th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Nama Produk</th>
                    <th>Stok</th>
                    <th style="text-align:center;">Harga Beli</th>
                    <th>Diinput Oleh</th>
                    <th style="text-align:center; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $index => $delivery)
                @php
                    $nilaiKiriman = $delivery->items->sum(fn($i) => $i->jumlah_kirim * $i->harga);
                @endphp
                <tr>
                    <td style="color:var(--ink-muted); font-size:12px;">
                        {{ $index + $deliveries->firstItem() }}
                    </td>
                    <td style="color:var(--ink-soft); white-space:nowrap;">
                        <i class="fas fa-calendar-day" style="color:var(--ink-muted); font-size:11px; margin-right:5px;"></i>
                        {{ \Carbon\Carbon::parse($delivery->tanggal)->format('d M Y') }}
                    </td>
                    <td>
                        <div class="supplier-chip">
                            <span style="font-weight:500;">{{ $delivery->supplier->nama_supplier }}</span>
                        </div>
                    </td>
                    <td>
                        @foreach($delivery->items as $item)
                        <div>{{ $item->product->nama_produk ?? '-' }}</div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($delivery->items as $item)
                        <div>{{ number_format($item->jumlah_kirim) }}</div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($delivery->items as $item)
                        <div>
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </div>
                        @endforeach
</td>
                    <td style="color:var(--ink-soft);">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="width:24px; height:24px; background:var(--accent-soft); border-radius:50%;
                                display:flex; align-items:center; justify-content:center;
                                font-size:10px; font-weight:700; color:var(--accent);">
                                {{ strtoupper(substr($delivery->createdBy->name ?? 'U', 0, 1)) }}
                            </div>
                            {{ $delivery->createdBy->name ?? '—' }}
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:4px; justify-content:center;">
                            <a href="{{ route('deliveries.show', $delivery) }}" class="btn-action info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn-action danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $delivery->id }}"
                                data-info="{{ \Carbon\Carbon::parse($delivery->tanggal)->format('d M Y') }} — {{ $delivery->supplier->nama_supplier }}"
                                title="Hapus">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-truck"></i></div>
                            <h6>Belum ada kiriman</h6>
                            <p>Catat kiriman pertama dari supplier Anda</p>
                            <a href="{{ route('deliveries.create') }}" class="btn btn-primary"
                                style="font-size:13px; border-radius:var(--r-sm);">
                                <i class="fas fa-plus me-1"></i> Tambah Kiriman
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($deliveries->hasPages())
    <div class="pagination-wrap d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span style="font-size:12px; color:var(--ink-muted);">
            Menampilkan {{ $deliveries->firstItem() }}–{{ $deliveries->lastItem() }} dari {{ $deliveries->total() }} kiriman
        </span>
        {{ $deliveries->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span style="width:28px;height:28px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:13px;">
                        <i class="fas fa-trash-can"></i>
                    </span>
                    Hapus Kiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px; color:var(--ink-soft); margin:0 0 10px;">
                    Anda akan menghapus kiriman berikut:
                </p>
                <div style="background:var(--bg); border:1px solid var(--border); border-radius:var(--r-md); padding:12px 14px; font-size:13.5px; font-weight:600; color:var(--ink);"
                    id="delete-info-box">—</div>
                <p style="font-size:12.5px; color:#dc2626; margin:12px 0 0;">
                    <i class="fas fa-circle-exclamation me-1"></i>
                    Stok produk terkait akan <strong>dikurangi kembali</strong>. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn"
                    style="font-size:13px; border:1px solid var(--border); border-radius:var(--r-sm); padding:7px 16px;"
                    data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST" action="">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        style="font-size:13px; border-radius:var(--r-sm); padding:7px 16px;">
                        <i class="fas fa-trash-can me-1"></i> Hapus & Kurangi Stok
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* Counter animation */
const fmt = n => new Intl.NumberFormat('id-ID').format(Math.round(n));
function animateCounter(el) {
    const target = parseFloat(el.dataset.count) || 0;
    const prefix = el.dataset.prefix || '';
    const isCur  = el.dataset.format === 'currency';
    const dur    = 800;
    const start  = performance.now();
    function tick(now) {
        const t = Math.min((now - start) / dur, 1);
        const e = 1 - Math.pow(1 - t, 3);
        el.textContent = prefix + (isCur ? fmt(target * e) : Math.round(target * e));
        if (t < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { animateCounter(e.target); obs.unobserve(e.target); } });
}, { threshold: 0.3 });
document.querySelectorAll('[data-count]').forEach(el => obs.observe(el));

/* Delete modal - set form action */
document.getElementById('deleteModal').addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    document.getElementById('delete-info-box').textContent = btn.dataset.info;
    document.getElementById('delete-form').action = `/deliveries/${btn.dataset.id}`;
});
</script>
@endpush
