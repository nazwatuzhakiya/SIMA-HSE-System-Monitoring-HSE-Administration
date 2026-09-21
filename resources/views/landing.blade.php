<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMA-HSE - PT Murinda Iron Steel</title>
    <!-- Google Fonts Merriweather & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-navy: #001f3f;
            --secondary-blue: #003366;
            --brand-blue: #004080;
            --accent-yellow: #f59e0b;
        }

        body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, select, label {
            font-family: 'Merriweather', 'Times New Roman', Times, Georgia, serif !important;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* Navbar Header */
        .navbar-custom {
            background: #001f3f;
            padding: 16px 0;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Hero Section */
        .hero-banner {
            background: #001f3f;
            color: #ffffff;
            padding: 70px 0 80px;
            position: relative;
        }

        .hero-card-kpi {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-top: 4px solid #003366 !important;
            border-radius: 8px;
            padding: 24px;
            color: #1e293b;
            box-shadow: 0 4px 15px rgba(0, 31, 63, 0.08);
            transition: all 0.25s ease;
        }

        .hero-card-kpi:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 31, 63, 0.15);
        }

        .btn-access-portal {
            background: #003366;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 6px;
            border: 1px solid #003366;
            box-shadow: 0 4px 15px rgba(0, 51, 102, 0.25);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-access-portal:hover {
            background: #001f3f;
            color: #ffffff;
            transform: translateY(-2px);
        }

        footer {
            background: #001f3f;
            color: #94a3b8;
            padding: 35px 0 20px;
            border-top: 3px solid #003366;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- Public Navbar -->
    <nav class="navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('landing') }}" class="d-inline-block">
                    <img src="{{ asset('gambar/logo-murinda.png') }}" alt="MURINDA" style="height: 38px; width: auto; object-fit: contain;">
                </a>
                <span class="d-none d-md-inline text-white-50 border-start border-secondary ps-3 small fw-bold" style="font-size: 12px; letter-spacing: 0.8px;">
                    SIMA-HSE
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3 fw-bold" style="border-radius: 6px;">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk Sistem HSE
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-banner">
        <div class="container text-center">
            <div class="badge bg-warning text-dark px-3 py-1.5 fw-bold mb-3" style="font-size: 12px; border-radius: 20px;">
                PLANT KALIJATI SUBANG &bull; SIMA-HSE
            </div>
            <h1 class="fw-bold display-5 mb-3">
                SIMA-HSE: System Monitoring & HSE Administration
            </h1>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 720px; font-size: 16px;">
                Pusat pengawasan keselamatan kerja konstruksi & fabrikasi baja, pemantauan tenaga kerja kontraktor, dan distribusi logistik perlengkapan APD resmi PT Murinda Iron Steel.
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-5">
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSeC-hDDXPRaCg0b4adFef0gSCRPoMXRoodD-Izm7tkWdy67ag/viewform?usp=publish-editor" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light fw-bold px-4 py-2.5 shadow-sm d-flex align-items-center" style="border-radius: 8px; font-size: 14px; border-color: rgba(255,255,255,0.4);">
                    <i class="fa-solid fa-clipboard-check me-2 text-warning"></i> Form Penilaian 5R
                </a>
                <a href="https://inductionhse.vercel.app/" target="_blank" rel="noopener noreferrer" class="btn btn-warning text-dark fw-bold px-4 py-2.5 shadow-sm d-flex align-items-center" style="border-radius: 8px; font-size: 14px;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-2"></i> Portal Pelaporan & Induksi K3
                </a>
            </div>

            <!-- KPI Cards Row -->
            <div class="row g-3 justify-content-center text-start">
                <div class="col-md-4 col-sm-6">
                    <div class="hero-card-kpi">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Manpower Aktif</div>
                                <h2 class="fw-bold m-0 mt-1" style="color: #003366;">{{ $totalManpower }}</h2>
                                <small class="text-muted">Personel Siap Operasional</small>
                            </div>
                            <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                                <i class="fa-solid fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="hero-card-kpi">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Mitra Subcont</div>
                                <h2 class="fw-bold m-0 mt-1" style="color: #003366;">{{ $totalSubcont }}</h2>
                                <small class="text-muted">Perusahaan Terdaftar</small>
                            </div>
                            <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                                <i class="fa-solid fa-building-flag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="hero-card-kpi">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Distribusi APD</div>
                                <h2 class="fw-bold m-0 mt-1" style="color: #003366;">{{ $totalApd }} Pcs</h2>
                                <small class="text-muted">Logistik K3 Terdistribusi</small>
                            </div>
                            <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                                <i class="fa-solid fa-hard-hat fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <div class="mb-2">
                <img src="{{ asset('gambar/logo-murinda.png') }}" alt="MURINDA" style="height: 30px; width: auto; object-fit: contain;">
            </div>
            <div class="small text-white-50">
                PT Murinda Iron Steel &bull; Plant Kalijati Subang &bull; Departemen HSE & K3LH &bull; &copy; {{ date('Y') }} All Rights Reserved.
            </div>
        </div>
    </footer>

</body>
</html>
