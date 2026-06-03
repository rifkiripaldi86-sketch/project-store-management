@extends('layouts.app')
@section('title', 'Manajemen User')

@push('styles')
<style>
    /* Header */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .page-header h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 2px;
        letter-spacing: -0.3px;
    }

    .page-header p {
        font-size: 13px;
        color: var(--ink-muted);
        margin: 0;
    }

    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
        animation: fadeSlideUp 0.4s 0.05s ease both;
    }

    @media (max-width: 640px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .7px;
        text-transform: uppercase;
        color: var(--ink-muted);
        margin-bottom: 6px;
    }

    .stat-value {
        font-family: 'Sora', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -0.4px;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--ink-muted);
        margin-top: 4px;
    }

    /* Main card */
    .main-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeSlideUp 0.4s 0.1s ease both;
    }

    .main-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .main-card-header-icon {
        width: 36px;
        height: 36px;
        background: var(--accent-soft);
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: var(--accent);
    }

    .main-card-header h5 {
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        color: var(--ink-muted);
        padding: 12px 16px;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
        color: var(--ink);
        vertical-align: middle;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr {
        transition: background 0.15s;
    }

    .data-table tbody tr:hover {
        background: var(--bg);
    }

    /* Role badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .role-admin {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .role-petugas {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    /* User identity */
    .user-name {
        font-weight: 600;
        color: var(--ink);
    }

    .user-email {
        font-size: 12px;
        color: var(--ink-muted);
        margin-top: 2px;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .action-btn-edit {
        background: #fef3c7;
        color: #d97706;
        border-color: #fde68a;
    }

    .action-btn-edit:hover {
        background: #fde68a;
        color: #b45309;
    }

    .action-btn-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .action-btn-delete:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 52px 24px;
    }

    .empty-state-icon {
        width: 58px;
        height: 58px;
        border-radius: var(--r-xl);
        background: var(--bg);
        border: 1px solid var(--border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--ink-muted);
        margin-bottom: 14px;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--ink-muted);
        margin: 0;
    }

    /* Pagination */
    .pagination-wrap {
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        background: var(--bg);
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header animate-in">
    <div>
        <h1>Manajemen User</h1>
        <p>Kelola akun pengguna sistem</p>
    </div>

    <a href="{{ route('users.create') }}"
        class="btn btn-primary d-flex align-items-center gap-2"
        style="height:38px; font-size:13.5px; font-weight:600; border-radius:var(--r-sm); padding:0 18px;">
        <i class="fas fa-user-plus" style="font-size:11px;"></i>
        Tambah User
    </a>
</div>

{{-- Stats --}}
<div class="stats-row">

    <div class="stat-card">
        <div class="stat-label">Total User</div>
        <div class="stat-value">{{ $users->total() }}</div>
        <div class="stat-sub">Semua pengguna sistem</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Administrator</div>
        <div class="stat-value">
            {{ $users->where('role', 'admin')->count() }}
        </div>
        <div class="stat-sub">Memiliki akses penuh</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Petugas</div>
        <div class="stat-value">
            {{ $users->where('role', 'operator')->count() }}
        </div>
        <div class="stat-sub">Pengguna operasional</div>
    </div>

</div>

{{-- Table --}}
<div class="main-card">

    <div class="main-card-header">
        <div class="main-card-header-icon">
            <i class="fas fa-users"></i>
        </div>

        <h5>Daftar User</h5>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th style="width:90px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $index => $user)
                <tr>

                    <td style="color:var(--ink-muted); font-size:12px;">
                        {{ $index + $users->firstItem() }}
                    </td>

                    <td>
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </td>

                    <td>
                        <span class="role-badge {{ $user->role == 'admin' ? 'role-admin' : 'role-petugas' }}">
                            <i class="fas {{ $user->role == 'admin' ? 'fa-shield-halved' : 'fa-user' }}"
                                style="font-size:10px;"></i>

                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    <td>
                        <div class="action-buttons">

                            <a href="{{ route('users.edit', $user) }}"
                                class="action-btn action-btn-edit"
                                title="Edit User">

                                <i class="fas fa-pen"></i>
                            </a>

                            <form action="{{ route('users.destroy', $user) }}"
                                method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="action-btn action-btn-delete"
                                    title="Hapus User"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')">

                                    <i class="fas fa-trash"></i>
                                </button>

                            </form>

                        </div>
                    </td>

                </tr>
                @empty

                <tr>
                    <td colspan="4" style="padding:0; border:none;">
                        <div class="empty-state">

                            <div class="empty-state-icon">
                                <i class="fas fa-users"></i>
                            </div>

                            <p>Belum ada data user</p>

                        </div>
                    </td>
                </tr>

                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrap">
        {{ $users->links() }}
    </div>
    @endif

</div>

@endsection