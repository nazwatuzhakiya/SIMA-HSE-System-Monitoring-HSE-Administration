<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@hasSection('title') @yield('title') - SIMA-HSE @else SIMA-HSE - PT Murinda Iron Steel @endif</title>
    <!-- Google Fonts Merriweather & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SheetJS Export Library -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- Chart.js Library for Manager Analytics & Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr Date Picker (DD/MM/YYYY standard without browser cursor jump) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Internal CSS Style (Design System Tokens & Custom Components - Uniform Navy with Murinda Red Accents & Merriweather Typography) -->
    <style>
        :root {
            --murinda-navy-dark: #001f3f;
            --murinda-navy-primary: #003366;
            --murinda-navy-accent: #004080;
            --murinda-red-accent: #dc2626;
            --murinda-green-hse: #16a34a;
            --murinda-bg: #f8fafc;
            --murinda-card-bg: #ffffff;
            --murinda-input-bg: #ffffff;
            --murinda-border: #e2e8f0;
            --murinda-border-dark: #000000;
            --murinda-text: #0f172a;
            --murinda-text-muted: #64748b;
            --bs-primary: #003366 !important;
            --bs-primary-rgb: 0, 51, 102 !important;
            --bs-link-color: #003366 !important;
            --bs-link-hover-color: #001f3f !important;
            --bs-focus-ring-color: rgba(0, 51, 102, 0.25) !important;
        }

        /* Penyeragaman Warna & Eliminasi Warna Terlalu Ramai */
        .text-primary { color: #003366 !important; }
        .bg-primary { background-color: #003366 !important; color: #ffffff !important; }
        .border-primary { border-color: #003366 !important; }
        .btn-primary { background-color: #003366 !important; border-color: #002244 !important; color: #ffffff !important; }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background-color: #001f3f !important; border-color: #001122 !important; color: #ffffff !important; }
        .btn-outline-primary { color: #003366 !important; border-color: #003366 !important; }
        .btn-outline-primary:hover { background-color: #003366 !important; color: #ffffff !important; }
        .alert-info, .bg-info-subtle { background-color: #f0f7ff !important; border-left: 4px solid #003366 !important; color: #003366 !important; }
        .bg-info { background-color: #003366 !important; color: #ffffff !important; }

        /* Global Clean Typography: Inter Sans-serif (Modern, Bersih, Lega seperti Silokos & Sanggar Goong) */
        body, button, input, select, textarea, p, h1, h2, h3, h4, h5, h6, span, a, div, td, th, label, li, ul, ol, strong, b, em, small, table {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            letter-spacing: -0.01em;
        }

        /* Protect FontAwesome Icons from font override */
        .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-brands, [class*="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", FontAwesome !important;
            font-style: normal;
        }
        .fa::before, .fas::before, .far::before, .fal::before, .fab::before, .fa-solid::before, .fa-regular::before, .fa-brands::before, [class*="fa-"]::before {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", FontAwesome !important;
            font-style: normal;
        }

        body {
            background-color: var(--murinda-bg);
            color: var(--murinda-text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-size: 13.5px;
            line-height: 1.5;
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            color: var(--murinda-navy-primary);
            font-weight: 700;
        }

        /* High Contrast Floating Modal Form Header (Uniform Navy with Red Accent Line) */
        .modal-header {
            background: #001f3f !important;
            color: #ffffff !important;
            border-bottom: 3px solid #dc2626 !important;
            padding: 14px 20px !important;
        }
        .modal-header .modal-title,
        .modal-header h1,
        .modal-header h2,
        .modal-header h3,
        .modal-header h4,
        .modal-header h5,
        .modal-header h6,
        .modal-header span,
        .modal-header i {
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%) !important;
            opacity: 0.9 !important;
        }
        .modal-header .btn-close:hover {
            opacity: 1 !important;
        }

        /* Reusable Soft Blurred Light Grey Badge */
        .badge-blured-gray {
            background: rgba(226, 232, 240, 0.8) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            color: #003366 !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 700;
        }

        /* Standardized Summary KPI Cards (Uniform Navy & White, No Wild Colors) */
        .card-kpi-uniform {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-top: 4px solid var(--murinda-navy-primary) !important;
            border-radius: 8px !important;
            padding: 16px 20px !important;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            height: 100%;
        }
        .card-kpi-uniform:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12) !important;
        }
        .kpi-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--murinda-navy-primary);
            margin: 0;
            line-height: 1.2;
        }
        .kpi-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .kpi-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            background: #edf2f7;
            color: var(--murinda-navy-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Backward Compatibility for KPI Cards */
        .card-kpi, .card-kpi-green, .card-kpi-yellow, .card-kpi-red, .card-kpi-navy {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-top: 4px solid var(--murinda-navy-primary) !important;
            border-left: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-kpi h3, .card-kpi-green h3, .card-kpi-yellow h3, .card-kpi-red h3, .card-kpi-navy h3 {
            color: var(--murinda-navy-primary) !important;
        }
        .card-kpi .text-white-50, .card-kpi-green .text-white-50, .card-kpi-yellow .text-dark, .card-kpi-red .text-white-50 {
            color: #64748b !important;
        }
        .card-kpi .bg-white.bg-opacity-25, .card-kpi .bg-white.bg-opacity-50 {
            background-color: #edf2f7 !important;
            color: var(--murinda-navy-primary) !important;
        }

        /* Multi-Step Wizard Styling (Uniform Navy) */
        .step-wizard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 24px;
        }
        .step-wizard::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: #cbd5e1;
            z-index: 1;
        }
        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px;
            transition: all 0.25s ease;
        }
        .step-item.active .step-circle {
            border-color: var(--murinda-navy-primary);
            background: var(--murinda-navy-primary);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.15);
        }
        .step-item.completed .step-circle {
            border-color: var(--murinda-navy-primary);
            background: var(--murinda-navy-primary);
            color: #ffffff;
        }
        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            display: block;
        }
        .step-item.active .step-label {
            color: var(--murinda-navy-primary);
            font-weight: 700;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
            animation: fadeIn 0.2s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sidebar Styling & Smooth Toggle Collapse */
        #sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #001f3f !important;
            color: #ffffff;
            z-index: 1000;
            transition: transform 0.3s ease, margin-left 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            transform: translateX(-270px);
        }

        #topbar {
            height: 70px;
            margin-left: 270px;
            background: #ffffff;
            border-top: 3px solid #dc2626;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            transition: margin-left 0.3s ease;
        }

        #topbar.expanded {
            margin-left: 0 !important;
        }

        #main-wrapper {
            margin-left: 270px;
            padding: 25px;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
        }

        #main-wrapper.expanded {
            margin-left: 0 !important;
        }

        /* Smooth Horizontal Scroll Containers for Departments & Subconts */
        .horizontal-subcont-scroll {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            gap: 12px;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: #004080 #e2e8f0;
        }

        .horizontal-subcont-scroll::-webkit-scrollbar {
            height: 7px;
        }

        .horizontal-subcont-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .horizontal-subcont-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        .horizontal-subcont-scroll::-webkit-scrollbar-thumb:hover {
            background: #004080;
        }

        .dept-compact-pill,
        .subcont-pill-card {
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            text-align: center;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #0f172a;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .dept-compact-pill:hover,
        .subcont-pill-card:hover {
            border-color: #003366;
            color: #003366;
            background: #f8fafc;
        }

        .dept-compact-pill.active-pill,
        .subcont-pill-card.active-pill {
            background: #003366 !important;
            border-color: #003366 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 51, 102, 0.2);
        }

        .dept-compact-pill .badge,
        .subcont-pill-card .badge {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .dept-compact-pill.active-pill .badge,
        .subcont-pill-card.active-pill .badge {
            background: #ffffff !important;
            color: #003366 !important;
            border-color: #ffffff !important;
        }

        /* Grid Departemen / Subcont / Proyek — Rapi Per Baris */
        .subcont-pill-btn {
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: #0f172a;
            font-size: 12px;
            font-weight: 600;
            height: 38px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .subcont-pill-btn:hover {
            border-color: #003366;
            color: #003366;
            background: #f8fafc;
        }

        .subcont-pill-btn.active-pill {
            background: #003366 !important;
            border-color: #003366 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 51, 102, 0.2);
        }

        .subcont-pill-btn .badge {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 6px;
            flex-shrink: 0;
        }

        .subcont-pill-btn.active-pill .badge {
            background: #ffffff !important;
            color: #003366 !important;
            border-color: #ffffff !important;
        }

        /* Grid Departemen: 7 Atas, 7 Bawah */
        .dept-grid-7 {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }
        @media (max-width: 1200px) {
            .dept-grid-7 {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (max-width: 576px) {
            .dept-grid-7 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Grid Subcont: 4 Atas, 4 Bawah */
        .subcont-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        @media (max-width: 768px) {
            .subcont-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Compact Pill Button (Lebih Kecil Biar Muat & Presisi) */
        .subcont-pill-btn-compact {
            border-radius: 5px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: #0f172a;
            font-size: 11px;
            font-weight: 600;
            height: 32px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .subcont-pill-btn-compact:hover {
            border-color: #003366;
            color: #003366;
            background: #f8fafc;
        }
        .subcont-pill-btn-compact.active-pill {
            background: #003366 !important;
            border-color: #003366 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 5px rgba(0, 51, 102, 0.2);
        }
        .subcont-pill-btn-compact .badge {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            font-size: 10px;
            padding: 1px 4px;
            border-radius: 3px;
            margin-left: 4px;
            flex-shrink: 0;
        }
        .subcont-pill-btn-compact.active-pill .badge {
            background: #ffffff !important;
            color: #003366 !important;
            border-color: #ffffff !important;
        }

        .sidebar-brand {
            padding: 18px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar-logo-box {
            width: 42px;
            height: 42px;
            border-radius: 6px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: var(--murinda-navy-primary);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 2px;
        }

        .sidebar-brand .brand-title {
            font-size: 12px;
            font-weight: 800;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .sidebar-brand .brand-sub {
            font-size: 10.5px;
            opacity: 0.85;
            font-weight: 500;
            margin-top: 2px;
        }

        .sidebar-menu {
            padding: 15px 10px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-item-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8 !important;
            padding: 14px 16px 6px;
        }

        .sidebar-menu .nav-link {
            color: #e2e8f0 !important;
            font-size: 13.5px;
            font-weight: 500;
            padding: 9px 16px;
            border-radius: 6px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 14px;
            width: 20px;
            text-align: center;
            color: #ffffff !important;
            opacity: 0.95;
        }

        .sidebar-menu .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12) !important;
            transform: translateX(3px);
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff !important;
            background: #003366 !important;
            font-weight: 700;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            border-left: 4px solid #dc2626 !important;
        }

        .running-marquee-top {
            background: #edf2f7;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 12.5px;
            color: #003366;
            font-weight: 600;
            max-width: 520px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Cards & Buttons Custom Style */
        .card-custom {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            margin-bottom: 24px;
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12) !important;
        }

        .btn-quick-action {
            background: #ffffff !important;
            color: #003366 !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06) !important;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        .btn-quick-action:hover,
        .btn-quick-action:focus,
        .btn-quick-action:active,
        .btn-quick-action.active {
            background: #003366 !important;
            color: #ffffff !important;
            border-color: #003366 !important;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25) !important;
        }

        .card-custom .card-header-custom {
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border-radius: 8px 8px 0 0;
        }

        .card-custom .card-title-custom {
            font-size: 15px;
            font-weight: 700;
            color: #003366;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* General Boxed Button Styling */
        .btn {
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }
        .btn:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
            transform: translateY(-1px);
        }

        /* Cetak Absen / Lembar Presensi / Primary Action: Murinda Navy (#003366), White on Hover */
        .btn-gradient-blue,
        .btn-murinda-primary,
        .btn-cetak-absen,
        .btn-primary,
        a[href*="absensi/setup"] {
            background-color: #003366 !important;
            color: #ffffff !important;
            border: 1px solid #003366 !important;
            border-radius: 6px;
            padding: 7px 16px;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 2px 6px rgba(0, 51, 102, 0.15);
            transition: all 0.2s ease;
        }

        .btn-gradient-blue:hover,
        .btn-murinda-primary:hover,
        .btn-cetak-absen:hover,
        .btn-primary:hover,
        a[href*="absensi/setup"]:hover {
            background-color: #ffffff !important;
            color: #003366 !important;
            border-color: #003366 !important;
            box-shadow: 0 3px 8px rgba(0, 51, 102, 0.2);
            transform: translateY(-1px);
        }

        /* Excel Export Button: Uniform Green (#16a34a) */
        .btn-excel, .btn-emerald-excel, button[onclick*="exportTableToExcel"], a[onclick*="exportTableToExcel"] {
            background-color: #16a34a !important;
            color: #ffffff !important;
            border: 1px solid #15803d !important;
            border-radius: 6px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 12.5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .btn-excel:hover, .btn-emerald-excel:hover, button[onclick*="exportTableToExcel"]:hover, a[onclick*="exportTableToExcel"]:hover {
            background-color: #15803d !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(22, 163, 74, 0.3);
            transform: translateY(-1px);
        }

        /* PDF Export / Cetak Button: Uniform Red (#dc2626) */
        .btn-pdf, .btn-murinda-danger, button[onclick*="window.print"], a[onclick*="window.print"] {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border: 1px solid #b91c1c !important;
            border-radius: 6px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 12.5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .btn-pdf:hover, .btn-murinda-danger:hover, button[onclick*="window.print"]:hover, a[onclick*="window.print"]:hover {
            background-color: #b91c1c !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(220, 38, 38, 0.3);
            transform: translateY(-1px);
        }

        /* Boxed Action Buttons for Tables */
        .btn-action-view {
            background: #ffffff !important;
            color: #003366 !important;
            border: 1px solid #003366 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(0, 51, 102, 0.1) !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-action-view:hover {
            background: #003366 !important;
            color: #ffffff !important;
        }

        .btn-action-edit {
            background: #ffffff !important;
            color: #b45309 !important;
            border: 1px solid #d97706 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(217, 119, 6, 0.1) !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-action-edit:hover {
            background: #d97706 !important;
            color: #ffffff !important;
        }

        .btn-action-finish {
            background: #ffffff !important;
            color: #15803d !important;
            border: 1px solid #16a34a !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(22, 163, 74, 0.1) !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-action-finish:hover {
            background: #16a34a !important;
            color: #ffffff !important;
        }

        /* Status Badges - Standardized Clean Design */
        .badge-aman {
            background-color: #f1f5f9;
            color: #003366;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            display: inline-flex;
            align-items: center;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            display: inline-flex;
            align-items: center;
        }

        .badge-bahaya {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            display: inline-flex;
            align-items: center;
        }

        /* ======================================================== */
        /* STANDARD ENTERPRISE CLEAN TABLE                          */
        /* Putih bersih, garis (list) hitam, teks hitam, paten abu */
        /* ======================================================== */
        .table-custom,
        .table {
            background-color: #ffffff !important;
            border-collapse: collapse !important;
            width: 100%;
            border: 1px solid #000000 !important;
            font-size: 13px;
        }

        .table-custom th,
        .table th {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000000 !important;
            border-bottom: 2px solid #000000 !important;
            padding: 10px 12px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-custom tbody tr td,
        .table tbody tr td {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 9px 12px;
            vertical-align: middle;
            border: 1px solid #000000 !important;
            font-size: 13px;
        }

        .table-custom tbody tr:hover td,
        .table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        .table-custom tbody td a:not(.badge):not(.btn),
        .table tbody td a:not(.badge):not(.btn) {
            color: #000000 !important;
        }

        /* Teks yang paten dari sistem (misalnya ID Pekerja / NIP, Kode Unik) dibuat warna abu-abu */
        .system-paten,
        .text-system-paten,
        .table-custom tbody td .system-paten,
        .table tbody td .system-paten,
        .table-custom tbody td small.font-monospace,
        .table tbody td small.font-monospace,
        .table-custom tbody td .font-monospace,
        .table tbody td .font-monospace,
        .table-custom td.system-paten,
        .table td.system-paten {
            color: #64748b !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            letter-spacing: 0.3px;
        }

        /* Form Filter & Input Text: Uniform Solid Black */
        .form-control-custom, .form-select-custom, input.form-control, select.form-select, textarea.form-control {
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            border-radius: 6px !important;
            padding: 7px 12px !important;
            font-size: 13px !important;
            transition: all 0.2s ease;
        }

        .form-label, label {
            color: #000000 !important;
            font-weight: 600;
        }

        .form-control-custom:focus, .form-select-custom:focus, input.form-control:focus, select.form-select:focus {
            background-color: #ffffff !important;
            color: #000000 !important;
            border-color: #003366 !important;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.15) !important;
            outline: none;
        }
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            #sidebar, #topbar, .btn, .no-print, .alert, .modal, .pagination, .dataTables_filter, .dataTables_info, .nav-tabs, form.row, .card-header-custom button, .card-header-custom a {
                display: none !important;
            }
            #main-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11px !important;
            }
            .card-custom {
                box-shadow: none !important;
                border: none !important;
                margin-bottom: 0 !important;
            }
            .table {
                width: 100% !important;
                font-size: 10px !important;
                border-collapse: collapse !important;
            }
            .table th, .table td {
                border: 1px solid #000000 !important;
                padding: 5px 8px !important;
                color: #000000 !important;
            }
            .table th:last-child, .table td:last-child {
                display: none !important;
            }
        }

        @readonly
        /* Mode Read-Only Otomatis untuk Role HSE Manager */
        .btn-murinda-primary[data-bs-toggle="modal"],
        [data-bs-target="#modalTambahPekerja"],
        [data-bs-target="#modalTambahPekerjaSubcont"],
        [data-bs-target="#modalTambahProyek"],
        [data-bs-target="#modalTambahVisitor"],
        [data-bs-target="#modalEditCompany"],
        [data-bs-target="#modalEditPersonel"],
        [data-bs-target="#modalEditProyek"],
        [data-bs-target="#modalEditVisitor"],
        .btn-action-edit,
        .btn-action-finish,
        .btn-action-delete,
        .form-delete-confirm,
        a[href*="apd/create"],
        #input-tab,
        #formInputAbsensi,
        .btn-simpan-absensi,
        .read-only-hide {
            display: none !important;
        }
        @endreadonly
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div id="sidebar">
        <div class="sidebar-brand text-center" style="padding: 16px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.12); background: rgba(0, 0, 0, 0.15);">
            <a href="{{ route('dashboard') }}" class="d-inline-block text-center w-100">
                <img src="{{ asset('gambar/logo-murinda.png') }}" alt="MURINDA" style="max-width: 160px; width: 75%; height: auto; object-fit: contain;">
            </a>
        </div>

        <div class="sidebar-menu">
            @readonly
            <div class="nav-item-header">Ringkasan Eksekutif</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard Eksekutif
            </a>

            <div class="nav-item-header">Statistik & Monitoring</div>
            <a href="{{ route('subcont.departemen') }}" class="nav-link {{ request()->routeIs('subcont.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Monitoring Tenaga Kerja
            </a>
            <a href="{{ route('manpower.index') }}" class="nav-link {{ request()->routeIs('manpower.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-viewfinder"></i> Statistik Manpower
            </a>
            <a href="{{ route('apd.index') }}" class="nav-link {{ request()->routeIs('apd.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i> Ringkasan Distribusi APD
            </a>
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> Rekapitulasi Presensi SMT
            </a>
            @else
            <div class="nav-item-header">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <div class="nav-item-header">Modul Monitoring</div>
            <a href="{{ route('subcont.departemen') }}" class="nav-link {{ request()->routeIs('subcont.departemen') ? 'active' : '' }}">
                <i class="fa-solid fa-building-user"></i> Monitoring Departemen
            </a>
            <a href="{{ route('subcont.subcont') }}" class="nav-link {{ request()->routeIs('subcont.subcont') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i> Monitoring Subcont
            </a>
            <a href="{{ route('subcont.proyek', ['company' => 'ENT']) }}" class="nav-link {{ request()->routeIs('subcont.proyek') && request('company', 'ENT') === 'ENT' ? 'active' : '' }}">
                <i class="fa-solid fa-trowel-bricks"></i> Monitoring ENT
            </a>
            <a href="{{ route('subcont.proyek', ['company' => 'FA/PROJECT']) }}" class="nav-link {{ request()->routeIs('subcont.proyek') && (request('company') === 'FA/PROJECT' || request('company') === 'FA') ? 'active' : '' }}">
                <i class="fa-solid fa-briefcase"></i> Monitoring FA
            </a>
            <a href="{{ route('subcont.visitor') }}" class="nav-link {{ request()->routeIs('subcont.visitor') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i> Monitoring Tamu
            </a>

            <div class="nav-item-header">Master List Manpower</div>
            <a href="{{ route('manpower.index') }}" class="nav-link {{ request()->routeIs('manpower.index') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card"></i> Manpower Pekerja
            </a>
            <a href="{{ route('manpower.proyek') }}" class="nav-link {{ request()->routeIs('manpower.proyek') ? 'active' : '' }}">
                <i class="fa-solid fa-helmet-safety"></i> Manpower ENT
            </a>

            <div class="nav-item-header">Logistik & Presensi</div>
            <a href="{{ route('apd.index') }}" class="nav-link {{ request()->routeIs('apd.*') ? 'active' : '' }}">
                <i class="fa-solid fa-hard-hat"></i> Logistik APD
            </a>
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-user"></i> Absensi Lapangan
            </a>
            @endreadonly
        </div>

        <div style="padding: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100 font-weight-bold" style="border-radius: 6px;">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </div>

    <!-- Topbar Header -->
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="sidebarToggle" class="btn btn-light border btn-sm shadow-sm px-3 py-1.5 me-2" title="Tutup / Buka Menu Sidebar">
                <i class="fa-solid fa-bars-staggered text-primary fs-6"></i>
            </button>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: #003366; color: #ffffff; font-size: 10px; letter-spacing: 0.5px; font-weight: 700; padding: 3px 6px;">SIMA-HSE</span>
                    <h5 class="fw-bold text-navy m-0" style="color: #003366; font-size: 16px; line-height: 1.2;">
                        @yield('page_heading', trim($__env->yieldContent('title')) ?: 'DEPARTEMEN HSE & K3LH')
                    </h5>
                </div>
                @hasSection('page_subheading')
                    <div class="text-muted small fw-semibold" style="font-size: 11px; margin-top: 2px;">
                        @yield('page_subheading')
                    </div>
                @endif
            </div>
        </div>

        <div class="profile-badge d-flex align-items-center gap-2 p-1 px-2.5 rounded-3 bg-light border">
            <div class="text-end me-1">
                @readonly
                    <div class="fw-bold fs-7 text-navy" style="font-size: 13px; line-height: 1.2;">{{ session('user_name', 'HSE Manager') }}</div>
                @else
                    <div class="fw-bold text-navy" style="font-size: 13px; line-height: 1.2;">HSE Admin</div>
                @endreadonly
            </div>
            <div class="avatar-circle shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border: 2px solid @readonly #16a34a @else #003366 @endreadonly;">
                <i class="fa-solid @readonly fa-user-tie text-success @else fa-user text-primary @endreadonly fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-wrapper">
        @php
            $isChildForm = request()->routeIs(['*.create', '*.edit', '*.show', 'absensi.setup', 'absensi.print']) || (View::hasSection('is_child_form'));
        @endphp
        @if($isChildForm)
            <div class="mb-3 no-print">
                <button type="button" onclick="if(window.history.length > 1){ window.history.back(); } else { window.location.href='{{ route('dashboard') }}'; }" class="btn btn-outline-secondary btn-sm shadow-sm px-3 py-1.5 d-inline-flex align-items-center gap-2" style="border-radius: 6px; font-size: 12.5px; font-weight: 600; background: #ffffff; color: #003366; border: 1px solid #cbd5e1;" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
            </div>
        @endif
        @if(session('success'))
            <!-- Pop-up Notifikasi Berhasil Masuk ke Sistem (Posisi Tepat di Tengah Layar) -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Pemberitahuan Sistem',
                            text: @json(session('success')),
                            showConfirmButton: true,
                            confirmButtonText: 'Lanjutkan',
                            confirmButtonColor: '#003366',
                            timer: 3500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-4 shadow-lg border p-3',
                                title: 'fw-bold fs-5 text-dark',
                                confirmButton: 'btn btn-murinda-primary px-4 py-2 fw-semibold'
                            }
                        });
                    }
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: @json(session('error')),
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Tutup',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border p-3'
                            }
                        });
                    }
                });
            </script>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Excel Export Function Helper & Auto-Dismiss Alerts -->
    <script>
        function exportTableToExcel(tableID, filename = '', customTitle = '') {
            var tableSelect = document.getElementById(tableID);
            if (!tableSelect) {
                alert('Tabel tidak ditemukan!');
                return;
            }

            // Clean Document Title & Code
            var docTitle = customTitle || (filename ? filename.replace(/_/g, ' ').toUpperCase() : 'REKAPITULASI DATA HSE RESMI');
            var docNumber = 'MIS/HSE/REC/' + new Date().getFullYear() + '/' + String(Math.floor(100 + Math.random() * 900)).padStart(3, '0');
            var exportDate = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });

            // Build Official Header Block (AOA - Array of Arrays)
            var dataAOA = [
                ['PT MURINDA IRON STEEL — PLANT KALIJATI SUBANG'],
                ['DEPARTEMEN HEALTH, SAFETY & ENVIRONMENT (HSE)'],
                [docTitle],
                ['No. Dokumen: ' + docNumber + '  |  Tanggal Ekspor: ' + exportDate],
                [] // Spacing row
            ];

            // Filter out action columns (AKSI, KELOLA, ACTION)
            var headerRow = tableSelect.querySelector('thead tr');
            var excludedColIndices = [];
            var tableHeaderTitles = [];

            if (headerRow) {
                var headerCells = Array.from(headerRow.querySelectorAll('th, td'));
                headerCells.forEach(function(cell, idx) {
                    var text = (cell.textContent || cell.innerText || '').trim();
                    var upperText = text.toUpperCase();
                    if (upperText === 'AKSI' || upperText === 'ACTION' || upperText.includes('KELOLA') || upperText === 'AKSI EDIT') {
                        excludedColIndices.push(idx);
                    } else {
                        tableHeaderTitles.push(text);
                    }
                });
                dataAOA.push(tableHeaderTitles);
            }

            // Extract tbody data rows
            var tbodyRows = tableSelect.querySelectorAll('tbody tr');
            tbodyRows.forEach(function(tr) {
                var rowData = [];
                var cells = Array.from(tr.querySelectorAll('th, td'));
                if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
                    return; // Skip empty table notification rows
                }
                cells.forEach(function(cell, idx) {
                    if (!excludedColIndices.includes(idx)) {
                        var cellClone = cell.cloneNode(true);
                        cellClone.querySelectorAll('button, form, script, .btn').forEach(function(el) { el.remove(); });
                        var cellText = (cellClone.textContent || cellClone.innerText || '').replace(/\s+/g, ' ').trim();
                        rowData.push(cellText);
                    }
                });
                if (rowData.length > 0) {
                    dataAOA.push(rowData);
                }
            });

            // Create Worksheet
            var ws = XLSX.utils.aoa_to_sheet(dataAOA);

            // Compute Precision Column Widths
            var colWidths = [];
            dataAOA.forEach(function(row, rIdx) {
                if (rIdx < 4) return; // Skip header title rows
                row.forEach(function(val, cIdx) {
                    var strVal = String(val || '');
                    colWidths[cIdx] = Math.max(colWidths[cIdx] || 12, strVal.length + 5);
                });
            });

            ws['!cols'] = colWidths.map(function(w) { return { wch: Math.min(Math.max(w, 14), 55) }; });

            // Create Workbook & Export File
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Data HSE Resmi");

            var saveFileName = (filename ? filename : 'Export_Data_HSE_Murinda') + '.xlsx';
            XLSX.writeFile(wb, saveFileName);
        }

        // Auto-dismiss all system alert notifications after 3.5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                });
            }, 3500);

            // Intercept form delete confirmation with SweetAlert2
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form && (form.classList.contains('form-delete-confirm') || (form.getAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm')))) {
                    if (form.dataset.confirmed === 'true') return;
                    e.preventDefault();
                    e.stopPropagation();
                    Swal.fire({
                        title: 'Konfirmasi Hapus Data',
                        text: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus Data',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0',
                            confirmButton: 'px-4 py-2 fw-bold',
                            cancelButton: 'px-4 py-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            }, true);

            // Sidebar Toggle (Bisa di-hide manual, tapi tidak otomatis ke-hide saat ganti menu)
            const sidebar = document.getElementById('sidebar');
            const topbar = document.getElementById('topbar');
            const mainWrapper = document.getElementById('main-wrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.toggle('collapsed');
                    if (topbar) topbar.classList.toggle('expanded');
                    if (mainWrapper) mainWrapper.classList.toggle('expanded');
                });
            }

            // Standard Flatpickr Initialization (DD/MM/YYYY without cursor jumps)
            if (typeof flatpickr !== 'undefined') {
                if (flatpickr.l10ns && flatpickr.l10ns.id) {
                    flatpickr.localize(flatpickr.l10ns.id);
                }
                document.querySelectorAll('input[type="date"], .flatpickr-date').forEach(function(input) {
                    if (!input._flatpickr) {
                        flatpickr(input, {
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "d/m/Y",
                            allowInput: true,
                            disableMobile: "true",
                            onChange: function(selectedDates, dateStr, instance) {
                                instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                                instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        });
                    }
                });
            }
            // Horizontal Scroller Auto-Center Active Pill & Drag-to-Scroll
            document.querySelectorAll('.horizontal-subcont-scroll').forEach(function(scroller) {
                const activePill = scroller.querySelector('.active-pill');
                if (activePill) {
                    // Center the active pill horizontally without jumping vertical window scroll
                    const scrollerRect = scroller.getBoundingClientRect();
                    const pillRect = activePill.getBoundingClientRect();
                    const offset = (pillRect.left + pillRect.width / 2) - (scrollerRect.left + scrollerRect.width / 2);
                    scroller.scrollLeft += offset;
                }

                let isDown = false;
                let startX, scrollLeft;
                scroller.addEventListener('mousedown', function(e) {
                    if (e.target.closest('a') && !e.shiftKey) return;
                    isDown = true;
                    scroller.style.cursor = 'grabbing';
                    startX = e.pageX - scroller.offsetLeft;
                    scrollLeft = scroller.scrollLeft;
                });
                window.addEventListener('mouseup', function() {
                    isDown = false;
                    scroller.style.cursor = '';
                });
                scroller.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - scroller.offsetLeft;
                    const walk = (x - startX) * 1.5;
                    scroller.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: @json(session('error')),
                confirmButtonColor: '#004080'
            });
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>

