@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    /* ── Page header ── */
    .page-header {
        margin-bottom: 24px;
    }
    .page-header h1 {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 700;
        color: var(--ink); margin: 0 0 4px;
        letter-spacing: -0.3px;
    }
    .page-header p {
        font-size: 13px; color: var(--ink-muted); margin: 0;
    }

    /* ── Stat cards ── */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 20px;
        display: flex; flex-direction: column;
        gap: 12px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        position: relative; overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    /* Colored top bar */
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--card-accent, var(--accent));
        border-radius: var(--r-lg) var(--r-lg) 0 0;
    }

    .stat-card.blue   { --card-accent: #3b82f6; }
    .stat-card.green  { --card-accent: #10b981; }
    .stat-card.amber  { --card-accent: #f59e0b; }
    .stat-card.violet { --card-accent: #8b5cf6; }

    .stat-top {
        display: flex; align-items: center; justify-content: space-between;
    }

    .stat-label {
        font-size: 11.5px; font-weight: 600;
        letter-spacing: 0.5px; color: var(--ink-soft);
        text-transform: uppercase;
    }

    .stat-icon {
        width: 36px; height: 36px; border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }

    .stat-card.blue   .stat-icon { background: #eff6ff; color: #3b82f6; }
    .stat-card.green  .stat-icon { background: #f0fdf4; color: #10b981; }
    .stat-card.amber  .stat-icon { background: #fffbeb; color: #f59e0b; }
    .stat-card.violet .stat-icon { background: #f5f3ff; color: #8b5cf6; }

    .stat-value {
        font-family: 'Sora', sans-serif;
        font-size: 26px; font-weight: 700;
        color: var(--ink); letter-spacing: -0.5px;
        line-height: 1;
    }

    .stat-sub {
        font-size: 12px; color: var(--ink-muted);
        display: flex; align-items: center; gap: 6px;
    }

    .stat-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 7px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
    }

    .stat-badge.up   { background: #f0fdf4; color: #16a34a; }
    .stat-badge.down { background: #fef2f2; color: #dc2626; }

    /* ── Chart cards ── */
    .chart-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s;
        overflow: hidden;
    }

    .chart-card:hover { box-shadow: var(--shadow-md); }

    .chart-header {
        padding: 18px 20px 14px;
        display: flex; align-items: flex-start; justify-content: space-between;
        border-bottom: 1px solid var(--border);
    }

    .chart-title {
        font-size: 14px; font-weight: 600; color: var(--ink);
        margin: 0 0 3px;
    }

    .chart-subtitle {
        font-size: 12px; color: var(--ink-muted); margin: 0;
    }

    .chart-meta {
        font-family: 'Sora', sans-serif;
        text-align: right;
    }

    .chart-total {
        font-size: 20px; font-weight: 700; color: var(--ink); line-height: 1;
    }

    .chart-total-label {
        font-size: 11px; color: var(--ink-muted); margin-top: 2px;
        font-family: 'DM Sans', sans-serif;
    }

    .chart-body {
        padding: 18px 20px 20px;
        position: relative;
    }

    /* ── Number counter animation ── */
    @keyframes countUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-value { animation: countUp 0.5s ease both; }

    /* ── Stagger for cards ── */
    .stat-row > div:nth-child(1) .stat-card { animation-delay: 0.0s; }
    .stat-row > div:nth-child(2) .stat-card { animation-delay: 0.08s; }
    .stat-row > div:nth-child(3) .stat-card { animation-delay: 0.16s; }
    .stat-row > div:nth-child(4) .stat-card { animation-delay: 0.24s; }
    .stat-card { animation: fadeSlideUp 0.4s ease both; }

    .chart-row > div:nth-child(1) { animation: fadeSlideUp 0.4s 0.3s ease both; }
    .chart-row > div:nth-child(2) { animation: fadeSlideUp 0.4s 0.38s ease both; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header animate-in">
    <h1>Dashboard</h1>
    <p>Selamat datang, {{ Auth::user()->name }} — <span id="live-date"></span></p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 stat-row mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card blue">
            <div class="stat-top">
                <span class="stat-label">Penjualan Hari Ini</span>
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
            </div>
            <div class="stat-value" data-count="{{ $totalPenjualanHariIni }}" data-prefix="Rp " data-format="currency">
                Rp 0
            </div>
            <div class="stat-sub">
                <span class="stat-badge up"><i class="fas fa-arrow-up"></i> +2.4%</span>
                vs kemarin
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">Barang Terjual</span>
                <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="stat-value" data-count="{{ $totalBarangTerjual }}" data-suffix=" pcs">
                0 pcs
            </div>
            <div class="stat-sub">
                <span class="stat-badge up"><i class="fas fa-arrow-up"></i> +8</span>
                dari kemarin
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card amber">
            <div class="stat-top">
                <span class="stat-label">Kas Masuk</span>
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="stat-value" data-count="{{ $kasMasuk }}" data-prefix="Rp " data-format="currency">
                Rp 0
            </div>
            <div class="stat-sub">
                <i class="fas fa-circle-info" style="color: var(--ink-muted);"></i>
                Total kas masuk hari ini
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card violet">
            <div class="stat-top">
                <span class="stat-label">Laba Hari Ini</span>
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
            </div>
            <div class="stat-value" data-count="{{ $labaHariIni }}" data-prefix="Rp " data-format="currency">
                Rp 0
            </div>
            <div class="stat-sub">
                <span class="stat-badge up"><i class="fas fa-arrow-up"></i> +5.1%</span>
                vs minggu lalu
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-3 chart-row">
    <div class="col-12 col-lg-7">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <p class="chart-title">Penjualan 7 Hari Terakhir</p>
                    <p class="chart-subtitle">Tren penjualan harian</p>
                </div>
                <div class="chart-meta">
                    <div class="chart-total" id="sales-total">—</div>
                    <div class="chart-total-label">Total 7 hari</div>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="salesChart" height="155"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <p class="chart-title">Laba Bulanan</p>
                    <p class="chart-subtitle">Performa bulan berjalan</p>
                </div>
                <div class="chart-meta">
                    <div class="chart-total" id="profit-total">—</div>
                    <div class="chart-total-label">Total tahun ini</div>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="profitChart" height="155"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ─────────────────────────────────────────────────────
   Helpers
───────────────────────────────────────────────────── */
const fmt = (n) => new Intl.NumberFormat('id-ID').format(Math.round(n));
const fmtRp = (n) => 'Rp ' + fmt(n);

// Live date
const liveDate = document.getElementById('live-date');
if (liveDate) {
    const d = new Date();
    liveDate.textContent = d.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
}

/* ─────────────────────────────────────────────────────
   Animated counter
───────────────────────────────────────────────────── */
function animateCounter(el) {
    const target    = parseFloat(el.dataset.count) || 0;
    const prefix    = el.dataset.prefix || '';
    const suffix    = el.dataset.suffix || '';
    const isCur     = el.dataset.format === 'currency';
    const duration  = 900;
    const startTime = performance.now();

    function tick(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // ease-out cubic
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = target * eased;
        el.textContent = prefix + (isCur ? fmt(current) : Math.round(current)) + suffix;
        if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}

// Trigger when visible
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            animateCounter(e.target);
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.3 });

document.querySelectorAll('[data-count]').forEach(el => observer.observe(el));

/* ─────────────────────────────────────────────────────
   Chart.js defaults — clean corporate style
───────────────────────────────────────────────────── */
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#9ca3af';
Chart.defaults.plugins.legend.display = false;

const GRID = { color: '#f1f3f5', drawBorder: false };
const TICKS_X = { padding: 8 };
const TICKS_Y = {
    padding: 10,
    callback: v => v >= 1_000_000 ? (v/1_000_000).toFixed(1)+'jt' : v >= 1_000 ? (v/1_000).toFixed(0)+'rb' : v
};

/* ─────────────────────────────────────────────────────
   Sales Chart (line + area)
───────────────────────────────────────────────────── */
const salesData = @json($sales7days);
const salesLabels = Object.keys(salesData);
const salesValues = Object.values(salesData);

// Compute total
const salesTotal = salesValues.reduce((a, b) => a + b, 0);
document.getElementById('sales-total').textContent = fmtRp(salesTotal);

const salesCtx = document.getElementById('salesChart').getContext('2d');

// Gradient fill
const grad = salesCtx.createLinearGradient(0, 0, 0, 260);
grad.addColorStop(0, 'rgba(59,130,246,0.15)');
grad.addColorStop(1, 'rgba(59,130,246,0)');

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Penjualan',
            data: salesValues,
            borderColor: '#3b82f6',
            backgroundColor: grad,
            borderWidth: 2.5,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#3b82f6',
            pointBorderWidth: 2,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            tooltip: {
                backgroundColor: '#0d1117',
                titleColor: '#fff',
                bodyColor: '#9ca3af',
                padding: 10,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    label: ctx => 'Penjualan: ' + fmtRp(ctx.raw)
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: TICKS_X, border: { display: false } },
            y: { grid: GRID, ticks: TICKS_Y, border: { display: false } }
        }
    }
});

/* ─────────────────────────────────────────────────────
   Profit Chart (bar)
───────────────────────────────────────────────────── */
const profitData   = @json($monthlyProfit);
const profitLabels = Object.keys(profitData);
const profitValues = Object.values(profitData);

const profitTotal  = profitValues.reduce((a, b) => a + b, 0);
document.getElementById('profit-total').textContent = fmtRp(profitTotal);

const profitCtx = document.getElementById('profitChart').getContext('2d');

// Bar gradient
const barGrad = profitCtx.createLinearGradient(0, 0, 0, 240);
barGrad.addColorStop(0, '#10b981');
barGrad.addColorStop(1, '#34d399');

new Chart(profitCtx, {
    type: 'bar',
    data: {
        labels: profitLabels,
        datasets: [{
            label: 'Laba',
            data: profitValues,
            backgroundColor: profitValues.map((_, i) =>
                i === profitValues.length - 1 ? barGrad : 'rgba(16,185,129,0.15)'
            ),
            borderColor: profitValues.map((_, i) =>
                i === profitValues.length - 1 ? '#10b981' : 'rgba(16,185,129,0.3)'
            ),
            borderWidth: 1.5,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            tooltip: {
                backgroundColor: '#0d1117',
                titleColor: '#fff',
                bodyColor: '#9ca3af',
                padding: 10,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    label: ctx => 'Laba: ' + fmtRp(ctx.raw)
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: TICKS_X, border: { display: false } },
            y: { grid: GRID, ticks: TICKS_Y, border: { display: false } }
        }
    }
});
</script>
@endpush