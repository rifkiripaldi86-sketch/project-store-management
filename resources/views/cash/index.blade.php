@extends('layouts.app')
@section('title', 'Kas Harian')

@push('styles')
<style>
    /* ── Page header ── */
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

    /* ── Summary cards ── */
    .summary-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 20px;
        display: flex; flex-direction: column; gap: 10px;
        box-shadow: var(--shadow-sm);
        position: relative; overflow: hidden;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .summary-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .summary-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: var(--r-lg) var(--r-lg) 0 0;
        background: var(--card-accent, var(--accent));
    }
    .summary-card.green  { --card-accent: #10b981; }
    .summary-card.red    { --card-accent: #ef4444; }
    .summary-card.blue   { --card-accent: #3b82f6; }
    .summary-card-top {
        display: flex; align-items: center; justify-content: space-between;
    }
    .summary-label {
        font-size: 11px; font-weight: 600; letter-spacing: 0.6px;
        text-transform: uppercase; color: var(--ink-soft);
    }
    .summary-icon {
        width: 34px; height: 34px; border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center; font-size: 14px;
    }
    .summary-card.green  .summary-icon { background: #f0fdf4; color: #10b981; }
    .summary-card.red    .summary-icon { background: #fef2f2; color: #ef4444; }
    .summary-card.blue   .summary-icon { background: #eff6ff; color: #3b82f6; }
    .summary-value {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 700; color: var(--ink);
        letter-spacing: -0.5px; line-height: 1;
    }
    .summary-sub { font-size: 12px; color: var(--ink-muted); }

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

    /* ── Table card ── */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .table-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    .table-card-title {
        font-size: 14px; font-weight: 600; color: var(--ink);
    }
    .table-count {
        font-size: 12px; color: var(--ink-muted);
        background: var(--bg); border: 1px solid var(--border);
        padding: 2px 10px; border-radius: 99px;
    }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .data-table thead th {
        background: var(--bg);
        font-size: 10.5px; font-weight: 700;
        letter-spacing: 0.8px; text-transform: uppercase;
        color: var(--ink-muted); padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .data-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid #f1f3f5;
        color: var(--ink); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #fafbfc; }

    /* Badges */
    .tipe-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 99px;
        font-size: 11.5px; font-weight: 600;
    }
    .tipe-badge.masuk  { background: #f0fdf4; color: #16a34a; }
    .tipe-badge.keluar { background: #fef2f2; color: #dc2626; }
    .tipe-badge .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: currentColor;
    }

    .kat-badge {
        display: inline-block; padding: 2px 8px; border-radius: var(--r-sm);
        font-size: 11px; font-weight: 500;
        background: var(--bg); color: var(--ink-soft);
        border: 1px solid var(--border);
    }

    /* Amount */
    .amount-masuk  { font-weight: 600; color: #16a34a; }
    .amount-keluar { font-weight: 600; color: #dc2626; }

    /* Actions */
    .btn-action {
        width: 30px; height: 30px; border-radius: var(--r-sm);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; border: 1px solid var(--border);
        background: var(--surface); color: var(--ink-soft);
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .btn-action:hover { background: var(--bg); color: var(--ink); }
    .btn-action.danger:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 56px 20px;
    }
    .empty-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: var(--bg); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: var(--ink-muted);
        margin: 0 auto 16px;
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
    .page-item.active .page-link {
        background: var(--accent); border-color: var(--accent); color: #fff;
    }
    .page-item.disabled .page-link { color: var(--ink-muted); }

    /* Delete modal */
    .modal-content {
        border: none; border-radius: var(--r-xl);
        box-shadow: var(--shadow-lg);
    }
    .modal-header { border-bottom: 1px solid var(--border); padding: 18px 22px; }
    .modal-body   { padding: 20px 22px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 14px 22px; gap: 8px; }
    .modal-title  { font-size: 15px; font-weight: 700; }

    /* Stagger */
    .animate-in { animation: fadeSlideUp 0.35s ease both; }
    .animate-in:nth-child(1) { animation-delay: 0.04s; }
    .animate-in:nth-child(2) { animation-delay: 0.10s; }
    .animate-in:nth-child(3) { animation-delay: 0.16s; }
    .animate-in:nth-child(4) { animation-delay: 0.22s; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <div class="page-header-left">
        <h1><i class="fas fa-coins" style="font-size:18px; color: var(--accent); margin-right:8px;"></i>Kas Harian</h1>
        <p>Pencatatan arus kas masuk dan keluar harian</p>
    </div>
    <a href="{{ route('cash.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height:38px; border-radius:var(--r-sm); font-size:13.5px; font-weight:600;">
        <i class="fas fa-plus" style="font-size:11px;"></i> Catat Kas
    </a>
</div>

@php
    $saldo = $saldoBersih;
@endphp

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4 animate-in">
        <div class="summary-card green">
            <div class="summary-card-top">
                <span class="summary-label">Kas Masuk</span>
                <div class="summary-icon"><i class="fas fa-arrow-down-to-bracket"></i></div>
            </div>
            <div class="summary-value" data-count="{{ $totalMasuk }}" data-prefix="Rp " data-format="currency">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <div class="summary-sub">Total pemasukan periode ini</div>
        </div>
    </div>
    <div class="col-12 col-sm-4 animate-in">
        <div class="summary-card red">
            <div class="summary-card-top">
                <span class="summary-label">Kas Keluar</span>
                <div class="summary-icon"><i class="fas fa-arrow-up-from-bracket"></i></div>
            </div>
            <div class="summary-value" data-count="{{ $totalKeluar }}" data-prefix="Rp " data-format="currency">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <div class="summary-sub">Total pengeluaran periode ini</div>
        </div>
    </div>
    <div class="col-12 col-sm-4 animate-in">
        <div class="summary-card blue">
            <div class="summary-card-top">
                <span class="summary-label">Saldo Bersih</span>
                <div class="summary-icon"><i class="fas fa-scale-balanced"></i></div>
            </div>
            <div class="summary-value" style="{{ $saldo < 0 ? 'color:#dc2626' : '' }}"
                data-count="{{ abs($saldo) }}" data-prefix="{{ $saldo < 0 ? '−Rp ' : 'Rp ' }}" data-format="currency">
                {{ $saldo < 0 ? '−' : '' }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
            </div>
            <div class="summary-sub">Masuk dikurangi keluar</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('cash.index') }}" class="filter-bar animate-in">
    <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width:160px;">
        <i class="fas fa-magnifying-glass" style="color: var(--ink-muted); font-size:13px;"></i>
        <input type="text" name="search" class="form-control border-0 shadow-none ps-0"
            placeholder="Cari keterangan / kategori…"
            value="{{ request('search') }}" style="height:30px; background:transparent;">
    </div>
    <div style="width:1px; height:24px; background:var(--border);"></div>
    <div class="d-flex align-items-center gap-2">
        <label>Tipe</label>
        <select name="tipe" class="form-select" style="width:130px;">
            <option value="">Semua</option>
            <option value="masuk"  {{ request('tipe') == 'masuk'  ? 'selected' : '' }}>Kas Masuk</option>
            <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
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
    <button type="submit" class="btn btn-primary" style="height:36px; font-size:13px; padding:0 16px; border-radius:var(--r-sm); flex-shrink:0;">
        <i class="fas fa-filter me-1" style="font-size:11px;"></i> Filter
    </button>
    @if(request()->hasAny(['search','tipe','dari','sampai']))
        <a href="{{ route('cash.index') }}" class="btn" style="height:36px; font-size:13px; padding:0 12px; border:1px solid var(--border); border-radius:var(--r-sm); color:var(--ink-soft); flex-shrink:0;">
            <i class="fas fa-xmark"></i>
        </a>
    @endif
</form>

{{-- Table --}}
<div class="table-card animate-in">
    <div class="table-card-header">
        <span class="table-card-title">Riwayat Kas</span>
        <span class="table-count">{{ $cashFlows->total() }} entri</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Jumlah</th>
                    <th>Dicatat Oleh</th>
                    <th style="text-align:center; width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashFlows as $cash)
                <tr>
                    <td style="color:var(--ink-soft); white-space:nowrap;">
                        <i class="fas fa-calendar-day" style="color:var(--ink-muted); font-size:11px; margin-right:5px;"></i>
                        {{ \Carbon\Carbon::parse($cash->tanggal)->format('d M Y') }}
                    </td>
                    <td>
                        <span class="tipe-badge {{ $cash->tipe }}">
                            <span class="dot"></span>
                            {{ ucfirst($cash->tipe) }}
                        </span>
                    </td>
                    <td>
                        <span class="kat-badge">{{ ucwords(str_replace('_',' ', $cash->kategori)) }}</span>
                    </td>
                    <td style="max-width:220px; color:var(--ink-soft);">
                        {{ $cash->keterangan ?? '—' }}
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <span class="amount-{{ $cash->tipe }}">
                            {{ $cash->tipe == 'keluar' ? '−' : '+' }} Rp {{ number_format($cash->jumlah, 0, ',', '.') }}
                        </span>
                    </td>
                    <td style="color:var(--ink-soft);">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="width:24px; height:24px; background:var(--accent-soft); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:var(--accent);">
                                {{ strtoupper(substr($cash->createdBy->name ?? 'U', 0, 1)) }}
                            </div>
                            {{ $cash->createdBy->name ?? '—' }}
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn-action danger"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-id="{{ $cash->id }}"
                            data-info="{{ \Carbon\Carbon::parse($cash->tanggal)->format('d M Y') }} — {{ ucfirst($cash->tipe) }} Rp {{ number_format($cash->jumlah,0,',','.') }}"
                            title="Hapus">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-coins"></i></div>
                            <h6>Belum ada data kas</h6>
                            <p>Mulai catat kas masuk atau keluar hari ini</p>
                            <a href="{{ route('cash.create') }}" class="btn btn-primary" style="font-size:13px; border-radius:var(--r-sm);">
                                <i class="fas fa-plus me-1"></i> Catat Kas Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cashFlows->hasPages())
    <div class="pagination-wrap d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span style="font-size:12px; color:var(--ink-muted);">
            Menampilkan {{ $cashFlows->firstItem() }}–{{ $cashFlows->lastItem() }} dari {{ $cashFlows->total() }} entri
        </span>
        {{ $cashFlows->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span style="width:28px;height:28px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:13px;">
                        <i class="fas fa-trash-can"></i>
                    </span>
                    Hapus Data Kas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px; color:var(--ink-soft); margin:0 0 10px;">
                    Anda akan menghapus entri kas berikut secara permanen:
                </p>
                <div style="background:var(--bg); border:1px solid var(--border); border-radius:var(--r-md); padding:12px 14px; font-size:13.5px; font-weight:600; color:var(--ink);" id="delete-info-box">
                    —
                </div>
                <p style="font-size:12.5px; color:#dc2626; margin:12px 0 0;">
                    <i class="fas fa-circle-exclamation me-1"></i> Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="font-size:13px; border:1px solid var(--border); border-radius:var(--r-sm); padding:7px 16px;" data-bs-dismiss="modal">
                    Batal
                </button>
                <form id="delete-form" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="font-size:13px; border-radius:var(--r-sm); padding:7px 16px;">
                        <i class="fas fa-trash-can me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Counter animation ── */
const fmt = n => new Intl.NumberFormat('id-ID').format(Math.round(n));
function animateCounter(el) {
    const target   = parseFloat(el.dataset.count) || 0;
    const prefix   = el.dataset.prefix || '';
    const suffix   = el.dataset.suffix || '';
    const isCur    = el.dataset.format === 'currency';
    const duration = 800;
    const start    = performance.now();
    function tick(now) {
        const t = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - t, 3);
        el.textContent = prefix + (isCur ? fmt(target * ease) : Math.round(target * ease)) + suffix;
        if (t < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { animateCounter(e.target); obs.unobserve(e.target); } });
}, { threshold: 0.3 });
document.querySelectorAll('[data-count]').forEach(el => obs.observe(el));

/* ── Delete modal ── */
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', e => {
    const btn  = e.relatedTarget;
    const id   = btn.dataset.id;
    const info = btn.dataset.info;
    document.getElementById('delete-info-box').textContent = info;
    document.getElementById('delete-form').action = `/cash/${id}`;
});
</script>
@endpush
