<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMA-HSE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #004080;
            --gradient-navy: linear-gradient(135deg, #001f3f 0%, #004080 100%);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-card-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 64, 128, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 940px;
            display: flex;
        }
        .login-left-brand {
            background: var(--gradient-navy);
            color: #ffffff;
            padding: 50px 40px;
            width: 48%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .login-right-form {
            padding: 50px 45px;
            width: 52%;
        }
        .btn-gradient-blue {
            background: linear-gradient(135deg, #004080 0%, #002b55 100%);
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 64, 128, 0.3);
            transition: all 0.2s ease;
        }
        .btn-gradient-blue:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004080 100%);
            color: #ffffff;
        }
        .logo-box {
            width: 54px;
            height: 54px;
            background: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: var(--primary-blue);
            margin-bottom: 16px;
            overflow: hidden;
            padding: 3px;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 45px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            border: none;
            background: none;
            padding: 0;
            font-size: 15px;
        }
        .toggle-password:hover {
            color: var(--primary-blue);
        }
    </style>
</head>
<body>

    <div class="login-card-container">
        <!-- Visual Branding -->
        <div class="login-left-brand">
            <div class="mb-3">
                <img src="{{ asset('gambar/logo-murinda.png') }}" alt="MURINDA" style="max-width: 210px; width: 100%; height: auto; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));">
            </div>

            <div class="my-4">
                <div class="p-3 rounded mb-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);">
                    <h6 class="fw-bold mb-2">SIMA-HSE: System Monitoring & HSE Administration</h6>
                    <p class="small m-0 text-white-50">Portal Pengelolaan Manpower, Monitoring Pekerja & Subcont, serta Pendataan APD Terintegrasi.</p>
                </div>

                <div class="p-3 rounded" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                    <span class="small text-white-75 fw-semibold">"Safety First, Quality Always. Utamakan Keselamatan Dalam Setiap Pekerjaan."</span>
                </div>
            </div>

            <div class="small text-white-50">
                &copy; {{ date('Y') }} PT Murinda Iron Steel.
            </div>
        </div>

        <!-- Form Login -->
        <div class="login-right-form">
            <h3 class="fw-bold mb-1" style="color: var(--primary-blue);">Masuk Sistem HSE</h3>
            <p class="text-muted small mb-4">Akses Keamanan Terenkripsi. Masukkan Kredensial Akun Anda.</p>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP / Email Terdaftar</label>
                    <div class="input-group password-wrapper">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user-shield text-muted"></i></span>
                        <input type="text"
                               name="email"
                               id="input-username"
                               class="form-control"
                               placeholder="email / NIP"
                               autocomplete="username"
                               required>
                        <button type="button" class="toggle-password" onclick="toggleUsername()" title="Lihat / Sembunyikan Username">
                            <i class="fa-solid fa-eye-slash" id="icon-toggle-user"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Kata Sandi / Password</label>
                    <div class="input-group password-wrapper">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password"
                               name="password"
                               id="input-password"
                               class="form-control"
                               placeholder="••••••••••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword()" title="Lihat / Sembunyikan Password">
                            <i class="fa-solid fa-eye" id="icon-toggle"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient-blue w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sistem HSE
                </button>
            </form>

            <div class="mt-3 p-2 text-center rounded border" style="background-color: #f8fafc; font-size: 11.5px; color: #475569;">
                <i class="fa-solid fa-shield-halved text-primary me-1"></i> Masuk menggunakan kredensial akun HSE resmi yang terdaftar
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('landing') }}" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Menu Tindak Lanjut / Portal
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleUsername() {
            const input = document.getElementById('input-username');
            const icon = document.getElementById('icon-toggle-user');
            if (input.type === 'text') {
                input.type = 'password';
                icon.className = 'fa-solid fa-eye';
            } else {
                input.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            }
        }

        function togglePassword() {
            const input = document.getElementById('input-password');
            const icon = document.getElementById('icon-toggle');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }
    </script>
</body>
</html>
