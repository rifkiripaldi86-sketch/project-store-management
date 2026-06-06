<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Sari Rezeki — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ────────────────────────────────────────────
           ROOT TOKENS (Ubah sidebar jadi putih asap)
        ──────────────────────────────────────────── */
        :root {
            --sidebar-w: 256px;
            --sidebar-collapsed-w: 68px;
            --speed: 0.28s;

            /* Palette umum */
            --ink:        #1e293b;
            --ink-soft:   #475569;
            --ink-muted:  #94a3b8;
            --border:     #e2e8f0;
            --surface:    #ffffff;
            --bg:         #f8fafc;

            /* Sidebar (PUTIH ASAP) */
            --sb-bg:      #fefefe;      /* putih asap */
            --sb-accent:  #1d4ed8;
            --sb-hover:   #f1f5f9;
            --sb-active:  #eef2ff;
            --sb-text:    #334155;
            --sb-text-dim:#64748b;

            /* Accent */
            --accent:     #1d4ed8;
            --accent-soft:#eff3ff;

            /* Status colors */
            --c-blue:     #3b82f6;
            --c-emerald:  #10b981;
            --c-amber:    #f59e0b;
            --c-violet:   #8b5cf6;

            /* Shadows */
            --shadow-sm:  0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            --shadow-md:  0 4px 12px rgba(0,0,0,0.05);
            --shadow-lg:  0 12px 28px rgba(0,0,0,0.08);

            /* Radius */
            --r-sm: 6px;
            --r-md: 10px;
            --r-lg: 14px;
            --r-xl: 20px;
        }

        /* ────────────────────────────────────────────
           RESET & BASE
        ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--ink);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; }

        /* ────────────────────────────────────────────
           SIDEBAR (PUTIH ASAP)
        ──────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-w);
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            transition: width var(--speed) cubic-bezier(.4,0,.2,1);
            z-index: 1030;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed-w); }

        /* Logo area */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px 16px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            overflow: hidden;
        }

        .sb-brand-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: var(--r-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            color: white;
        }

        .sb-brand-text {
            display: flex; flex-direction: column; overflow: hidden;
        }

        .sb-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 14px; font-weight: 700;
            color: var(--ink); letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .sb-brand-sub {
            font-size: 10px; color: var(--sb-text-dim);
            letter-spacing: 1.2px; text-transform: uppercase;
            white-space: nowrap; margin-top: 1px;
        }

        .sidebar.collapsed .sb-brand-text { display: none; }

        /* Nav */
        .sb-nav { flex: 1; padding: 12px 10px; overflow-y: auto; overflow-x: hidden; }

        .sb-nav::-webkit-scrollbar { width: 4px; }
        .sb-nav::-webkit-scrollbar-track { background: transparent; }
        .sb-nav::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .sb-section-label {
            font-size: 9px; font-weight: 600; letter-spacing: 1.6px;
            text-transform: uppercase; color: var(--ink-muted);
            padding: 14px 10px 6px;
            white-space: nowrap; overflow: hidden;
            transition: opacity var(--speed);
        }

        .sidebar.collapsed .sb-section-label { opacity: 0; }

        /* Nav items */
        .sb-item {
            display: flex; align-items: center;
            padding: 9px 10px;
            margin-bottom: 2px;
            border-radius: var(--r-md);
            color: var(--sb-text);
            font-size: 13.5px; font-weight: 500;
            white-space: nowrap;
            transition: background var(--speed), color 0.15s, transform 0.15s;
            cursor: pointer; border: none; background: none; width: 100%;
            text-align: left;
            position: relative;
        }

        .sb-item:hover {
            background: var(--sb-hover);
            color: var(--ink);
        }

        .sb-item.active {
            background: var(--sb-active);
            color: var(--accent);
        }

        .sb-item.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: var(--accent);
        }

        .sb-icon {
            width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--r-sm);
            font-size: 13px;
            flex-shrink: 0; margin-right: 10px;
            transition: all 0.2s;
            color: var(--sb-text-dim);
        }

        .sb-item:hover .sb-icon, .sb-item.active .sb-icon {
            background: rgba(0,0,0,0.04);
            color: var(--accent);
        }

        .sb-label { transition: opacity var(--speed); overflow: hidden; }
        .sidebar.collapsed .sb-label { opacity: 0; width: 0; }
        .sidebar.collapsed .sb-icon { margin-right: 0; }

        .sb-chevron {
            margin-left: auto; font-size: 10px; color: var(--ink-muted);
            transition: transform 0.25s;
        }
        .sidebar.collapsed .sb-chevron { display: none; }

        [aria-expanded="true"] .sb-chevron { transform: rotate(180deg); }

        /* Sub-nav */
        .sb-sub {
            padding-left: 14px;
        }

        .sb-sub .sb-item {
            font-size: 13px; font-weight: 400;
            color: var(--sb-text-dim);
            padding: 7px 10px;
        }

        .sb-sub .sb-item:hover { color: var(--ink); background: var(--sb-hover); }
        .sb-sub .sb-item.active { color: var(--accent); background: var(--sb-active); }

        .sb-sub .sb-icon {
            width: 24px; height: 24px; font-size: 11px;
        }

        /* Footer sidebar */
        .sb-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
        }

        .sb-user {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: var(--r-md);
            cursor: pointer; overflow: hidden;
            transition: background 0.2s;
        }

        .sb-user:hover { background: var(--sb-hover); }

        .sb-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600; color: #fff;
            flex-shrink: 0;
        }

        .sb-user-info { overflow: hidden; }
        .sb-user-name {
            font-size: 13px; font-weight: 600; color: var(--ink);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sb-user-role {
            font-size: 11px; color: var(--sb-text-dim);
        }

        .sidebar.collapsed .sb-user-info { display: none; }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .sb-item { justify-content: center; }

        /* Logout button di sidebar */
        .sb-footer .sb-item {
            margin-top: 8px;
            color: var(--sb-text-dim);
        }
        .sb-footer .sb-item:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        /* ────────────────────────────────────────────
           MAIN CONTENT (sama seperti sebelumnya)
        ──────────────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left var(--speed) cubic-bezier(.4,0,.2,1);
        }

        .sidebar.collapsed ~ #main { margin-left: var(--sidebar-collapsed-w); }

        /* ────────────────────────────────────────────
           TOPBAR (tidak berubah)
        ──────────────────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(248,249,251,0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 58px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .btn-icon {
            width: 34px; height: 34px;
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--ink-soft); font-size: 13px;
            cursor: pointer; transition: all 0.2s;
        }

        .btn-icon:hover {
            background: var(--bg);
            border-color: #d1d5db;
            color: var(--ink);
        }

        .breadcrumb-nav {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: var(--ink-muted);
        }

        .breadcrumb-nav span:last-child { color: var(--ink); font-weight: 500; }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .topbar-badge {
            width: 34px; height: 34px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--ink-soft); font-size: 13px;
            cursor: pointer; transition: all 0.2s; position: relative;
        }

        .topbar-badge:hover { background: var(--bg); color: var(--ink); }

        .badge-dot {
            width: 7px; height: 7px; background: #ef4444;
            border-radius: 50%; position: absolute; top: 6px; right: 6px;
            border: 1.5px solid var(--bg);
        }

        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px 5px 5px;
            border-radius: var(--r-md);
            cursor: pointer; border: 1px solid var(--border);
            background: var(--surface);
            transition: all 0.2s;
        }

        .topbar-user:hover { background: var(--bg); }

        .topbar-avatar {
            width: 28px; height: 28px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; color: #fff;
        }

        .topbar-user-info { line-height: 1; }
        .topbar-user-name { font-size: 13px; font-weight: 600; color: var(--ink); }
        .topbar-user-role { font-size: 11px; color: var(--ink-muted); }

        /* ────────────────────────────────────────────
           CONTENT AREA
        ──────────────────────────────────────────── */
        .content-area {
            flex: 1;
            padding: 28px 28px 20px;
        }

        /* ────────────────────────────────────────────
           CARDS
        ──────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600; font-size: 14px; color: var(--ink);
        }

        .card-body { padding: 20px; }

        /* ────────────────────────────────────────────
           ALERTS
        ──────────────────────────────────────────── */
        .alert {
            border-radius: var(--r-md);
            border: 1px solid transparent;
            font-size: 13.5px;
        }

        .alert-success {
            background: #f0fdf4; border-color: #bbf7d0; color: #166534;
        }

        .alert-danger {
            background: #fef2f2; border-color: #fecaca; color: #991b1b;
        }

        /* ────────────────────────────────────────────
           TABLE
        ──────────────────────────────────────────── */
        .table { font-size: 13.5px; }
        .table th {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.8px; text-transform: uppercase;
            color: var(--ink-muted); background: var(--bg);
            padding: 10px 14px;
        }
        .table td { padding: 12px 14px; vertical-align: middle; }
        .table-hover tbody tr:hover { background: var(--bg); }

        /* ────────────────────────────────────────────
           BUTTONS
        ──────────────────────────────────────────── */
        .btn { border-radius: var(--r-sm); font-size: 13.5px; font-weight: 500; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }

        /* ────────────────────────────────────────────
           FOOTER
        ──────────────────────────────────────────── */
        .app-footer {
            padding: 14px 28px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            font-size: 12px; color: var(--ink-muted);
            display: flex; align-items: center; justify-content: space-between;
        }

        /* ────────────────────────────────────────────
           PAGE LOAD ANIMATION
        ──────────────────────────────────────────── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeSlideUp 0.4s ease both;
        }

        .animate-in:nth-child(1) { animation-delay: 0.05s; }
        .animate-in:nth-child(2) { animation-delay: 0.1s; }
        .animate-in:nth-child(3) { animation-delay: 0.15s; }
        .animate-in:nth-child(4) { animation-delay: 0.2s; }
        .animate-in:nth-child(5) { animation-delay: 0.25s; }
        .animate-in:nth-child(6) { animation-delay: 0.3s; }

        /* ────────────────────────────────────────────
           PRINT
        ──────────────────────────────────────────── */
        @media print {
            .sidebar, .topbar, .app-footer, .no-print { display: none !important; }
            #main { margin: 0 !important; }
            .content-area { padding: 0 !important; }
            body { background: white; font-size: 12px; }
            .card { box-shadow: none; border: 1px solid #ddd; }
        }

        /* ────────────────────────────────────────────
           RESPONSIVE — MOBILE
        ──────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w) !important;
            }
            .sidebar.mobile-open { transform: translateX(0); }
            #main { margin-left: 0 !important; }

            .sidebar-overlay {
                display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.4); z-index: 1029;
            }
            .sidebar.mobile-open ~ .sidebar-overlay { display: block; }

            .content-area { padding: 16px; }
            .topbar { padding: 0 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SIDEBAR (PUTIH ASAP) -->
    <aside class="sidebar" id="sidebar">

        <!-- Brand -->
        <div class="sb-brand">
            <div class="sb-brand-icon">🍰</div>
            <div class="sb-brand-text">
                <span class="sb-brand-name">Manajemen Toko</span>
                <span class="sb-brand-sub">Toko Kue Sari</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sb-nav">

            <div class="sb-section-label">Main</div>

            <a class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="sb-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sb-label">Dashboard</span>
            </a>

            @auth
                @if(in_array(auth()->user()->role, ['admin', 'operator']))

                    <div class="sb-section-label">Operasional</div>

                    <!-- Transaksi -->
                    <button class="sb-item {{ request()->routeIs('cash.*') || request()->routeIs('deliveries.*') || request()->routeIs('sales.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colTransaksi"
                        aria-expanded="{{ request()->routeIs('cash.*') || request()->routeIs('deliveries.*') || request()->routeIs('sales.*') ? 'true' : 'false' }}">
                        <span class="sb-icon"><i class="fas fa-arrows-rotate"></i></span>
                        <span class="sb-label">Transaksi</span>
                        <i class="fas fa-chevron-down sb-chevron"></i>
                    </button>
                    <div class="collapse sb-sub {{ request()->routeIs('cash.*') || request()->routeIs('deliveries.*') || request()->routeIs('sales.*') ? 'show' : '' }}" id="colTransaksi">
                        <a class="sb-item {{ request()->routeIs('cash.*') ? 'active' : '' }}" href="{{ route('cash.index') }}">
                            <span class="sb-icon"><i class="fas fa-coins"></i></span>
                            <span class="sb-label">Kas Harian</span>
                        </a>
                        <a class="sb-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}" href="{{ route('deliveries.index') }}">
                            <span class="sb-icon"><i class="fas fa-truck"></i></span>
                            <span class="sb-label">Kiriman Supplier</span>
                        </a>
                        <a class="sb-item {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <span class="sb-icon"><i class="fas fa-cart-shopping"></i></span>
                            <span class="sb-label">Penjualan</span>
                        </a>
                    </div>

                    <!-- Master Data -->
                    <button class="sb-item {{ request()->routeIs('suppliers.*') || request()->routeIs('products.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colMaster"
                        aria-expanded="{{ request()->routeIs('suppliers.*') || request()->routeIs('products.*') ? 'true' : 'false' }}">
                        <span class="sb-icon"><i class="fas fa-layer-group"></i></span>
                        <span class="sb-label">Master Data</span>
                        <i class="fas fa-chevron-down sb-chevron"></i>
                    </button>
                    <div class="collapse sb-sub {{ request()->routeIs('suppliers.*') || request()->routeIs('products.*') ? 'show' : '' }}" id="colMaster">
                        <a class="sb-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                            <span class="sb-icon"><i class="fas fa-building"></i></span>
                            <span class="sb-label">Supplier</span>
                        </a>
                        <a class="sb-item {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <span class="sb-icon"><i class="fas fa-box-open"></i></span>
                            <span class="sb-label">Produk</span>
                        </a>
                    </div>

                    <a class="sb-item {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.create') }}">
                        <span class="sb-icon"><i class="fas fa-money-bill-transfer"></i></span>
                        <span class="sb-label">Bayar Supplier</span>
                    </a>

                    <a class="sb-item {{ request()->routeIs('damaged.*') ? 'active' : '' }}" href="{{ route('damaged.index') }}">
                        <span class="sb-icon"><i class="fas fa-triangle-exclamation"></i></span>
                        <span class="sb-label">Barang Rusak</span>
                    </a>

                    <a class="sb-item {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                       <span class="sb-icon"><i class="fas fa-scale-balanced"></i></span>
                        <span class="sb-label">Satuan Barang</span>
                    </a>

                    <a class="sb-item {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <span class="sb-icon"><i class="fas fa-tags"></i></span>
                        <span class="sb-label">Kategori Produk</span>
                    </a>

                    <div class="sb-section-label">Analitik</div>

                    <!-- Laporan -->
                    <button class="sb-item {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#colLaporan"
                        aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                        <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="sb-label">Laporan</span>
                        <i class="fas fa-chevron-down sb-chevron"></i>
                    </button>
                    <div class="collapse sb-sub {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="colLaporan">
                        <a class="sb-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}" href="{{ route('reports.daily') }}">
                            <span class="sb-icon"><i class="fas fa-calendar-day"></i></span>
                            <span class="sb-label">Harian</span>
                        </a>
                        <a class="sb-item {{ request()->routeIs('reports.monthly') ? 'active' : '' }}" href="{{ route('reports.monthly') }}">
                            <span class="sb-icon"><i class="fas fa-calendar-week"></i></span>
                            <span class="sb-label">Bulanan</span>
                        </a>
                        <a class="sb-item {{ request()->routeIs('reports.yearly') ? 'active' : '' }}" href="{{ route('reports.yearly') }}">
                            <span class="sb-icon"><i class="fas fa-calendar-alt"></i></span>
                            <span class="sb-label">Tahunan</span>
                        </a>
                    </div>

                @endif

                @if(auth()->user()->role == 'admin')
                    <div class="sb-section-label">Admin</div>

                    <a class="sb-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <span class="sb-icon"><i class="fas fa-users-gear"></i></span>
                        <span class="sb-label">User Management</span>
                    </a>

                    {{-- <a class="sb-item {{ request()->routeIs('backup.*') ? 'active' : '' }}" href="{{ route('backup.index') }}">
                        <span class="sb-icon"><i class="fas fa-database"></i></span>
                        <span class="sb-label">Backup Database</span>
                    </a> --}}
                @endif
            @endauth

        </nav>

        <!-- User & Logout -->
        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                <div class="sb-user-info">
                    <div class="sb-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="sb-user-role">{{ ucfirst(Auth::user()->role ?? '') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="sb-item" style="color: #64748b;">
                    <span class="sb-icon"><i class="fas fa-arrow-right-from-bracket"></i></span>
                    <span class="sb-label">Logout</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- Mobile overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- MAIN -->
    <div id="main">

        <!-- Topbar -->
        <header class="topbar no-print">
            <div class="topbar-left">
                <!-- Desktop toggle -->
                <div class="btn-icon d-none d-md-flex" id="sb-toggle" title="Toggle sidebar">
                    <i class="fas fa-bars-staggered" id="sb-toggle-icon"></i>
                </div>
                <!-- Mobile toggle -->
                <div class="btn-icon d-flex d-md-none" id="sb-mobile-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="breadcrumb-nav d-none d-sm-flex">
                    <i class="fas fa-house" style="font-size:11px;"></i>
                    <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                    <span>@yield('title', 'Dashboard')</span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-badge no-print">
                    <i class="fas fa-bell" style="font-size:13px;"></i>
                    <span class="badge-dot"></span>
                </div>
                <div class="topbar-user dropdown" data-bs-toggle="dropdown">
                    <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="topbar-user-info d-none d-sm-block">
                        <div class="topbar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="topbar-user-role">{{ ucfirst(Auth::user()->role ?? '') }}</div>
                    </div>
                    <i class="fas fa-chevron-down ms-2 d-none d-sm-block" style="font-size:10px; color: var(--ink-muted);"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px; border-radius: var(--r-md); box-shadow: var(--shadow-lg); border-color: var(--border);">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item d-flex align-items-center gap-2" type="submit">
                                <i class="fas fa-arrow-right-from-bracket" style="color: #ef4444; width:14px;"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="content-area pb-0" id="flash-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-in" role="alert">
                    <i class="fas fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-in" role="alert">
                    <i class="fas fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="content-area">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="app-footer no-print">
            <span>© {{ date('Y') }} Toko Kue Sari Rezeki</span>
            <span>Jl. Cipageran No.136 Cimahi &nbsp;·&nbsp; 089655763820</span>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar: desktop collapse ──
        const sidebar  = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sb-toggle');
        const COLLAPSED_KEY = 'sb_collapsed';

        function applySidebarState() {
            if (localStorage.getItem(COLLAPSED_KEY) === 'true') {
                sidebar.classList.add('collapsed');
            }
        }
        applySidebarState();

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem(COLLAPSED_KEY, sidebar.classList.contains('collapsed'));
            });
        }

        // ── Sidebar: mobile ──
        const mobileToggle  = document.getElementById('sb-mobile-toggle');
        const overlay       = document.getElementById('sidebar-overlay');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
            });
        }

        // ── Auto-dismiss flash after 4s ──
        setTimeout(() => {
            document.querySelectorAll('#flash-area .alert').forEach(el => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            });
        }, 4000);
    </script>

    @stack('scripts')
</body>
</html>
