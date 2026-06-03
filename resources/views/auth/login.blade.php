<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Masuk - Toko Kue Sari Rezeki</title>

    <!-- Google Fonts: Inter + Sora -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Sora:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-soft: #eef2ff;
            --text: #0f172a;
            --text-soft: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 12px 28px rgba(0,0,0,0.08);
            --shadow-glow: 0 0 0 4px rgba(37,99,235,0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #f1f5f9 0%, #e6edf5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Dekorasi blur background */
        .bg-blur {
            position: fixed;
            width: 70vmax;
            height: 70vmax;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,0.12), transparent 70%);
            top: -20vmax;
            right: -20vmax;
            z-index: 0;
            pointer-events: none;
        }

        .bg-blur-2 {
            position: fixed;
            width: 60vmax;
            height: 60vmax;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139,92,246,0.08), transparent 70%);
            bottom: -30vmax;
            left: -20vmax;
            z-index: 0;
            pointer-events: none;
        }

        /* Card utama */
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            border: 1px solid rgba(255,255,255,0.6);
            overflow: hidden;
            box-shadow: var(--shadow-md), 0 1px 1px rgba(0,0,0,0.02);
            transition: transform 0.25s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.2);
        }

        /* LEFT PANEL - Ilustrasi */
        .login-illustration {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 3rem 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .login-illustration::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08), transparent 60%);
            top: -50%;
            left: -50%;
            pointer-events: none;
        }

        .brand-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .illustration-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 1rem;
        }

        .illustration-desc {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 2rem;
            max-width: 90%;
        }

        .feature-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
        }

        .feature-item i {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.08);
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* RIGHT PANEL - Form */
        .login-form {
            padding: 3rem 2.5rem;
            background: transparent;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-custom i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            z-index: 4;
        }

        .input-group-custom .form-control {
            height: 52px;
            padding-left: 2.8rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
            font-size: 0.9rem;
            background: white;
            transition: all 0.2s;
            box-shadow: none;
        }

        .input-group-custom .form-control:focus {
            border-color: var(--primary);
            box-shadow: var(--shadow-glow);
            outline: none;
        }

        .btn-login {
            background: linear-gradient(105deg, var(--primary), var(--primary-dark));
            border: none;
            width: 100%;
            height: 52px;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 0.5rem;
            box-shadow: 0 4px 10px rgba(37,99,235,0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(37,99,235,0.3);
            background: linear-gradient(105deg, var(--primary-dark), #1e3a8a);
        }

        .demo-card {
            background: var(--bg-light);
            border-radius: 1rem;
            padding: 1rem 1.2rem;
            margin-top: 2rem;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .demo-card:hover {
            background: white;
            border-color: var(--primary-soft);
        }

        .demo-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary);
            margin-bottom: 0.4rem;
        }

        .demo-cred {
            font-size: 0.85rem;
            color: var(--text-soft);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .demo-cred span strong {
            color: var(--text);
            font-weight: 700;
        }

        .copyright {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .alert-custom {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #991b1b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Responsif */
        @media (max-width: 992px) {
            .login-illustration {
                display: none;
            }
            .login-form {
                padding: 2rem 1.8rem;
            }
            .form-title {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }
            .login-form {
                padding: 1.8rem 1.2rem;
            }
            .input-group-custom .form-control {
                height: 48px;
            }
            .btn-login {
                height: 48px;
            }
        }

        /* Animasi masuk */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeSlideUp 0.5s cubic-bezier(0.2, 0.9, 0.4, 1) both;
        }
    </style>
</head>
<body>

<div class="bg-blur"></div>
<div class="bg-blur-2"></div>

<div class="login-container">
    <div class="row g-0 login-card">
        <!-- Kolom kiri: ilustrasi -->
        <div class="col-lg-6">
            <div class="login-illustration">
                <div class="brand-icon">
                    🍰
                </div>
                <div class="illustration-title">
                    Kelola Toko Kue<br>Lebih Cerdas
                </div>
                <div class="illustration-desc">
                    Sistem manajemen terpadu untuk penjualan, stok, supplier, dan laporan keuangan dalam satu platform modern.
                </div>
                <div class="feature-grid">
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard realtime</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-boxes"></i>
                        <span>Kontrol stok akurat</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Laporan otomatis</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom kanan: form login -->
        <div class="col-lg-6">
            <div class="login-form">
                <div class="form-header">
                    <div class="form-title">Selamat Datang 👋</div>
                    <div class="form-subtitle">Silakan masuk ke akun Anda</div>
                </div>

                @if($errors->any())
                    <div class="alert-custom">
                        <i class="fas fa-circle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Alamat email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Kata sandi" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-arrow-right-to-bracket"></i> Masuk
                    </button>
                </form>

                <div class="demo-card">
                    <div class="demo-label">
                        <i class="fas fa-flask me-1"></i> Demo Akses
                    </div>
                    <div class="demo-cred">
                        <span><strong>Email:</strong> admin@tokosarirezeki.com</span>
                        <span><strong>Password:</strong> admin123</span>
                    </div>
                </div>

                <div class="copyright">
                    &copy; {{ date('Y') }} Toko Kue Sari Rezeki — All rights reserved
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional Bootstrap JS (tidak wajib untuk tampilan) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>