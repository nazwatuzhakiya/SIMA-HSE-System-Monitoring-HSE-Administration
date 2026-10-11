@extends('layouts.app')

@readonly
    @section('title', 'Dashboard Utama HSE - Eksekutif & Pengawasan')
    @section('page_heading', 'Dashboard Utama HSE (Eksekutif & Pengawasan K3LH)')
    @section('page_subheading', 'PT Murinda Iron Steel Plant Kalijati Subang — Executive Monitoring View')
@else
    @section('title', 'Dashboard Utama HSE - Operasional')
    @section('page_heading', 'Dashboard Utama HSE (Pusat Operasional K3LH)')
    @section('page_subheading', 'PT Murinda Iron Steel Plant Kalijati Subang')
@endreadonly

@section('content')
<div class="container-fluid px-0">

    @readonly
    <!-- ======================================================================= -->
    <!-- TAMPILAN KHUSUS ROLE HSE MANAGER (STATISTIK, GRAFIK & PERSENTASE)      -->
    <!-- Fokus pada metrik makro, visualisasi tren & analisis tanpa data mentah   -->
    <!-- ======================================================================= -->

    <!-- Indikator Tanggal & Periode (Simpel & Bersih) -->
    <div class="d-flex justify-content-end align-items-center mb-3 gap-2">
        <span class="badge bg-white shadow-sm" style="color: #003366; border: 1px solid #cbd5e1; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 6px;">
            <i class="fa-regular fa-calendar-check me-1 text-primary"></i> Periode: {{ $currentPeriodLabel }}
        </span>
        <span class="badge bg-white shadow-sm" style="color: #0f172a; border: 1px solid #cbd5e1; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 6px;">
            <i class="fa-regular fa-clock me-1 text-danger"></i> {{ date('d/m/Y') }}
        </span>
    </div>

    <!-- 4 KARTU STATISTIK UTAMA (BERSIH & ELEGAN) -->
    <div class="row g-3 mb-4">
        <!-- 1. Safe Man Hours -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #16a34a;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Jam Kerja Selamat (Man Hours)</div>
                        <h3 class="kpi-number text-dark mt-1 mb-0">{{ number_format($aggregateStats['total_man_hours'] ?? 0, 0, ',', '.') }} <small class="fs-6 fw-normal text-muted">Jam</small></h3>
                        <div class="kpi-subtext mt-1">
                            <span class="text-success fw-bold"><i class="fa-solid fa-check-circle me-1"></i>Zero Accident</span> &bull; {{ number_format($aggregateStats['total_man_days'] ?? 0, 0, ',', '.') }} Man Days
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fa-solid fa-shield-heart fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Indeks Kehadiran SMT Rata-rata -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #003366;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Indeks Kehadiran SMT (4 Minggu)</div>
                        <h3 class="kpi-number mt-1 mb-0" style="color: #003366;">{{ number_format($avgSmtPercentage, 1, ',', '.') }}%</h3>
                        <div class="kpi-subtext mt-1">
                            Rata-rata Kehadiran Safety Talk
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #eff6ff; color: #003366;">
                        <i class="fa-solid fa-chart-line fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Personel & Pengawasan Subcont -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #003366;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Pengawasan Tenaga Kerja</div>
                        <h3 class="kpi-number mt-1 mb-0" style="color: #003366;">{{ $totalSeluruhPersonel }} <small class="fs-6 fw-normal text-muted">Orang</small></h3>
                        <div class="kpi-subtext mt-1">
                            {{ $totalManpowerAktif }} Aktif &bull; {{ $totalMitraSubcont }} Subkontraktor
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #eff6ff; color: #003366;">
                        <i class="fa-solid fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. SMT Terendah Perminggu (Semua Merah) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-clean p-3 h-100" style="border: 1.5px solid #f87171 !important; border-top: 4px solid #dc2626 !important; background: #fff5f5 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label text-danger fw-bold">SMT Terendah (Perminggu)</div>
                        <h3 class="kpi-number text-danger fw-bold mt-1 mb-0">
                            {{ number_format($smtWeeklyStats['lowest_percentage'] ?? 0, 1, ',', '.') }}%
                        </h3>
                        <div class="kpi-subtext text-danger fw-semibold text-truncate mt-1" style="max-width: 175px;" title="{{ $smtWeeklyStats['lowest_dept_name'] ?? '-' }}">
                            {{ $smtWeeklyStats['has_data'] ? $smtWeeklyStats['lowest_dept_name'] : 'Semua Sesuai Target' }}
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                        <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PREPARE DATA UNTUK GRAFIK ANALYTICS -->
    @php
        $chartWeekLabels = [];
        $chartWeekPcts = [];
        foreach($smtWeeklyStats['weeks'] as $w) {
            $chartWeekLabels[] = $w['label'];
            $chartWeekPcts[] = $w['persentase_total'] !== null ? round($w['persentase_total'], 1) : 0;
        }
        $chartApdLabels = ['Safety Helmet', 'Rompi Reflective', 'Sepatu Safety', 'Body Harness'];
        $chartApdData = [
            $apdSummaryBreakdown['Helmet'] ?? 0,
            $apdSummaryBreakdown['Rompi'] ?? 0,
            $apdSummaryBreakdown['Sepatu'] ?? 0,
            $apdSummaryBreakdown['Harness'] ?? 0,
        ];
    @endphp

    <!-- ROW 1 GRAFIK: TREN KEHADIRAN SMT & DISTRIBUSI APD -->
    <div class="row g-3 mb-4">
        <!-- Grafik A: Rekap Kehadiran SMT Mingguan dan Persentase Terendah -->
        <div class="col-lg-8">
            <div class="card-custom h-100 mb-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-line text-primary"></i>
                        <h6 class="fw-bold m-0 text-dark">
                            Rekap Kehadiran SMT Mingguan dan Persentase Terendah
                        </h6>
                    </div>
                    <span class="badge border px-2.5 py-1 text-dark" style="background: #ffffff; border-color: #000000 !important; font-size: 11px;">
                        Grafik Tren 4 Minggu
                    </span>
                </div>
                <div class="card-body p-3">
                    <div style="height: 230px; position: relative;">
                        <canvas id="chartSmtTrend"></canvas>
                    </div>

                    <!-- Ringkasan Persentase Per Minggu (Bersih & Rapi) -->
                    <div class="row g-2 mt-3 pt-3 border-top text-center">
                        @foreach($smtWeeklyStats['weeks'] as $w)
                            @php
                                $isLowest = ($smtWeeklyStats['has_data'] && $w['persentase_total'] !== null && $w['persentase_total'] == $smtWeeklyStats['lowest_percentage']);
                            @endphp
                            <div class="col-3">
                                <div class="p-2 rounded border {{ $isLowest ? 'border-danger bg-danger-subtle' : 'bg-light' }}">
                                    <div class="text-muted small fw-semibold" style="font-size: 11px;">{{ $w['label'] }}</div>
                                    <div class="fw-bold {{ $isLowest ? 'text-danger' : 'text-primary' }} mt-1" style="font-size: 15px;">
                                        {{ $w['persentase_total'] !== null ? number_format($w['persentase_total'], 1, ',', '.') . '%' : '-' }}
                                    </div>
                                    <div class="small mt-0.5 text-muted" style="font-size: 10px;">
                                        {{ $w['hadir'] }} / {{ $w['total_recorded'] }} hadir
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik B: Distribusi Penyerahan APD Lapangan -->
        <div class="col-lg-4">
            <div class="card-custom h-100 mb-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-primary"></i>
                        <h6 class="fw-bold m-0 text-dark">
                            Distribusi Penyerahan APD
                        </h6>
                    </div>
                    <span class="badge border px-2 py-0.5 text-dark" style="background: #ffffff; border-color: #000000 !important; font-size: 10.5px;">
                        {{ number_format($totalDistribusiApd, 0, ',', '.') }} Pcs
                    </span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div style="height: 190px; position: relative;">
                        <canvas id="chartApdDist"></canvas>
                    </div>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted" style="font-size: 11px;">Safety Helmet:</span>
                            <span class="fw-bold text-dark" style="font-size: 11px;">{{ number_format($apdSummaryBreakdown['Helmet'] ?? 0, 0, ',', '.') }} Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted" style="font-size: 11px;">Rompi Reflective:</span>
                            <span class="fw-bold text-dark" style="font-size: 11px;">{{ number_format($apdSummaryBreakdown['Rompi'] ?? 0, 0, ',', '.') }} Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted" style="font-size: 11px;">Sepatu Safety:</span>
                            <span class="fw-bold text-dark" style="font-size: 11px;">{{ number_format($apdSummaryBreakdown['Sepatu'] ?? 0, 0, ',', '.') }} Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted" style="font-size: 11px;">Body Harness:</span>
                            <span class="fw-bold text-dark" style="font-size: 11px;">{{ number_format($apdSummaryBreakdown['Harness'] ?? 0, 0, ',', '.') }} Pcs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2 GRAFIK: KOMPOSISI TENAGA KERJA & INDIKATOR PERSENTASE K3 -->
    <div class="row g-3 mb-4">
        <!-- Grafik C: Komposisi Tenaga Kerja -->
        <div class="col-lg-6">
            <div class="card-custom h-100 mb-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users-viewfinder text-primary"></i>
                        <h6 class="fw-bold m-0 text-dark">
                            Komposisi Tenaga Kerja Plant
                        </h6>
                    </div>
                    <span class="badge border px-2 py-0.5 text-dark" style="background: #ffffff; border-color: #000000 !important; font-size: 10.5px;">
                        {{ $totalSeluruhPersonel }} Total
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div style="height: 180px; position: relative;">
                                <canvas id="chartManpowerComp"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex flex-column gap-2 mt-2 mt-sm-0">
                                <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold text-dark" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-circle text-success me-1.5" style="font-size: 9px;"></i> Tenaga Kerja Aktif
                                    </span>
                                    <span class="fw-bold text-success" style="font-size: 12px;">{{ $totalManpowerAktif }}</span>
                                </div>
                                <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold text-dark" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-circle text-secondary me-1.5" style="font-size: 9px;"></i> Non-Aktif (Off)
                                    </span>
                                    <span class="fw-bold text-secondary" style="font-size: 12px;">{{ $totalManpowerNonAktif }}</span>
                                </div>
                                <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold text-dark" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-circle text-primary me-1.5" style="font-size: 9px;"></i> Mitra Subkontraktor
                                    </span>
                                    <span class="fw-bold text-primary" style="font-size: 12px;">{{ $totalMitraSubcont }} Vendor</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Persentase & Matriks Performa K3 -->
        <div class="col-lg-6">
            <div class="card-custom h-100 mb-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-primary"></i>
                        <h6 class="fw-bold m-0 text-dark">
                            Persentase & Performa Keselamatan Kerja
                        </h6>
                    </div>
                    <span class="badge border px-2 py-0.5 text-dark" style="background: #ffffff; border-color: #000000 !important; font-size: 10.5px;">
                        K3LH Plant
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        <!-- Progress 1: Indeks Kehadiran SMT -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size: 12px;">Indeks Kehadiran SMT Rata-rata</span>
                                <span class="fw-bold text-primary" style="font-size: 12px;">{{ number_format($avgSmtPercentage, 1, ',', '.') }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; background-color: #e2e8f0; border-radius: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $avgSmtPercentage) }}%; border-radius: 4px;"></div>
                            </div>
                        </div>

                        <!-- Progress 2: Rasio Kesiapan Tenaga Kerja Aktif -->
                        @php
                            $activeRatio = $totalSeluruhPersonel > 0 ? round(($totalManpowerAktif / $totalSeluruhPersonel) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size: 12px;">Rasio Tenaga Kerja Siap Kerja</span>
                                <span class="fw-bold text-success" style="font-size: 12px;">{{ $activeRatio }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; background-color: #e2e8f0; border-radius: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activeRatio }}%; border-radius: 4px;"></div>
                            </div>
                        </div>

                        <!-- Progress 3: Tingkat Kehadiran SMT Terendah -->
                        @php
                            $lowestPct = $smtWeeklyStats['lowest_percentage'] ?? 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size: 12px;">Tingkat Kehadiran Terendah (Mingguan)</span>
                                <span class="fw-bold text-danger" style="font-size: 12px;">{{ number_format($lowestPct, 1, ',', '.') }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; background-color: #e2e8f0; border-radius: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ min(100, $lowestPct) }}%; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT INISIALISASI CHART.JS ELEGAN & RESPONSIF UNTUK HSE MANAGER -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Grafik Tren Kehadiran SMT Mingguan (Line Chart)
            const ctxSmt = document.getElementById('chartSmtTrend');
            if (ctxSmt && typeof Chart !== 'undefined') {
                new Chart(ctxSmt, {
                    type: 'line',
                    data: {
                        labels: @json($chartWeekLabels),
                        datasets: [{
                            label: '% Kehadiran SMT',
                            data: @json($chartWeekPcts),
                            borderColor: '#003366',
                            backgroundColor: 'rgba(0, 51, 102, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#003366',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                grid: { color: '#f1f5f9' },
                                ticks: { callback: function(val) { return val + '%'; }, font: { size: 11 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' }, color: '#0f172a' }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return 'Kehadiran: ' + context.parsed.y + '%'; }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Distribusi APD Lapangan (Bar Chart)
            const ctxApd = document.getElementById('chartApdDist');
            if (ctxApd && typeof Chart !== 'undefined') {
                new Chart(ctxApd, {
                    type: 'bar',
                    data: {
                        labels: ['Helmet', 'Rompi', 'Sepatu', 'Harness'],
                        datasets: [{
                            data: @json($chartApdData),
                            backgroundColor: ['#003366', '#16a34a', '#d97706', '#dc2626'],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { size: 10 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10.5, weight: '600' } }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) { return 'Jumlah: ' + ctx.parsed.y + ' Pcs'; }
                                }
                            }
                        }
                    }
                });
            }

            // 3. Grafik Komposisi Tenaga Kerja (Doughnut Chart)
            const ctxMp = document.getElementById('chartManpowerComp');
            if (ctxMp && typeof Chart !== 'undefined') {
                new Chart(ctxMp, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tenaga Kerja Aktif', 'Non-Aktif (Off)', 'Mitra Vendor'],
                        datasets: [{
                            data: [{{ $totalManpowerAktif }}, {{ $totalManpowerNonAktif }}, {{ $totalMitraSubcont }}],
                            backgroundColor: ['#16a34a', '#94a3b8', '#003366'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.label + ': ' + ctx.parsed + ' (' + Math.round((ctx.parsed / {{ max(1, $totalSeluruhPersonel) }}) * 100) + '%)';
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        });
    </script>

    @else
    <!-- ======================================================================= -->
    <!-- TAMPILAN KHUSUS ROLE ADMIN HSE (OPERASIONAL LAPANGAN & PENGELOLAAN DATA)-->
    <!-- Fokus pada ringkasan kerja harian, aksi cepat & tugas administrasi      -->
    <!-- ======================================================================= -->

    <!-- Indikator Tanggal & Periode Operasional (Kompak & Bersih) -->
    <div class="d-flex justify-content-end align-items-center mb-3 gap-2">
        <span class="badge bg-white shadow-sm" style="color: #003366; border: 1px solid #cbd5e1; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 6px;">
            <i class="fa-regular fa-calendar-check me-1 text-primary"></i> Periode: {{ $currentPeriodLabel }}
        </span>
        <span class="badge bg-white shadow-sm" style="color: #0f172a; border: 1px solid #cbd5e1; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 6px;">
            <i class="fa-regular fa-clock me-1 text-danger"></i> {{ date('d/m/Y') }}
        </span>
    </div>

    <!-- 3 Kartu Ringkasan Operasional Inti (Simpel, Bersih, Tidak Menumpuk) -->
    <div class="row g-3 mb-4">
        <!-- 1. Manpower Lapangan Aktif -->
        <div class="col-md-4">
            <a href="{{ route('manpower.index', ['status' => 'Aktif']) }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #003366;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Tenaga Kerja Siap Kerja (Aktif)</div>
                            <h3 class="kpi-number mt-1 mb-0" style="color: #003366;">{{ $totalManpowerAktif }} <small class="fs-6 fw-normal text-muted">Personel</small></h3>
                            <div class="kpi-subtext mt-1">
                                Siap tugas di Plant Kalijati
                            </div>
                        </div>
                        <div class="kpi-icon-circle" style="background: #eff6ff; color: #003366;">
                            <i class="fa-solid fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Presensi SMT Hari Ini -->
        <div class="col-md-4">
            <a href="{{ route('absensi.index') }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #16a34a;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label" style="color: #16a34a;">Presensi SMT Hari Ini</div>
                            <h3 class="kpi-number mt-1 mb-0" style="color: #16a34a;">
                                {{ $todayAbsensiPct }}%
                            </h3>
                            <div class="kpi-subtext mt-1">
                                {{ $todayAbsensiHadir + $todayAbsensiTelat }} hadir dari {{ $todayAbsensiCount }} tercatat
                            </div>
                        </div>
                        <div class="kpi-icon-circle" style="background: #f0fdf4; color: #16a34a;">
                            <i class="fa-solid fa-clipboard-user fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Logistik APD Terdistribusi -->
        <div class="col-md-4">
            <a href="{{ route('apd.index') }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-clean p-3 h-100" style="border-top: 3px solid #dc2626;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label" style="color: #dc2626;">Logistik APD Terdistribusi</div>
                            <h3 class="kpi-number mt-1 mb-0" style="color: #dc2626;">
                                {{ number_format($totalDistribusiApd, 0, ',', '.') }} <small class="fs-6 fw-normal text-muted">Pcs</small>
                            </h3>
                            <div class="kpi-subtext mt-1">
                                Penyerahan APD periode berjalan
                            </div>
                        </div>
                        <div class="kpi-icon-circle" style="background: #fef2f2; color: #dc2626;">
                            <i class="fa-solid fa-hard-hat fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Akses Aksi Cepat Harian (Quick Action Bar — Ringkas & Fungsional) -->
    <div class="card-custom mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">
                        Aksi Cepat Operasional Harian
                    </h6>
                    <small class="text-muted">Akses langsung tanpa perlu mencari di submenu navigasi:</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('absensi.setup') }}" class="btn btn-quick-action btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-print me-1"></i> Cetak Lembar Presensi SMT
                    </a>
                    <a href="{{ route('apd.create') }}" class="btn btn-quick-action btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-box-open me-1"></i> Input Distribusi APD Baru
                    </a>
                    <a href="{{ route('subcont.index') }}" class="btn btn-quick-action btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-user-plus me-1"></i> Tambah / Perbarui Pekerja
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Rekap Kehadiran SMT Mingguan Operasional -->
    <div class="card-custom mb-4">
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-chart-line text-primary"></i>
                <h6 class="fw-bold m-0 text-dark">
                    Rekap Kehadiran SMT Mingguan dan Persentase Terendah
                </h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge border border-danger px-2.5 py-1 text-danger fw-bold" style="background: #fff5f5; font-size: 11px;">
                    SMT Terendah (Perminggu): <strong class="text-danger">{{ number_format($smtWeeklyStats['lowest_percentage'] ?? 0, 1, ',', '.') }}%</strong>
                </span>
                <a href="{{ route('absensi.index') }}" class="btn btn-sm btn-outline-dark py-1 px-2.5 fw-semibold" style="font-size: 11.5px;">
                    Buka Absensi Lengkap
                </a>
            </div>
        </div>
        <div class="card-body p-3">
            <div style="height: 240px; position: relative;">
                <canvas id="chartSmtTrendAdmin"></canvas>
            </div>

            <!-- Ringkasan Persentase Per Minggu (Bersih & Rapi) -->
            <div class="row g-2 mt-3 pt-3 border-top text-center">
                @foreach($smtWeeklyStats['weeks'] as $w)
                    @php
                        $isLowest = ($smtWeeklyStats['has_data'] && $w['persentase_total'] !== null && $w['persentase_total'] == $smtWeeklyStats['lowest_percentage']);
                    @endphp
                    <div class="col-3">
                        <div class="p-2 rounded border {{ $isLowest ? 'border-danger bg-danger-subtle' : 'bg-light' }}">
                            <div class="text-muted small fw-semibold" style="font-size: 11px;">{{ $w['label'] }}</div>
                            <div class="fw-bold {{ $isLowest ? 'text-danger' : 'text-primary' }} mt-1" style="font-size: 15px;">
                                {{ $w['persentase_total'] !== null ? number_format($w['persentase_total'], 1, ',', '.') . '%' : '-' }}
                            </div>
                            <div class="small mt-0.5 text-muted" style="font-size: 10px;">
                                {{ $w['hadir'] }} / {{ $w['total_recorded'] }} hadir
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Script Chart SMT untuk Admin -->
    @php
        $admChartLabels = [];
        $admChartPcts = [];
        foreach($smtWeeklyStats['weeks'] as $w) {
            $admChartLabels[] = $w['label'];
            $admChartPcts[] = $w['persentase_total'] !== null ? round($w['persentase_total'], 1) : 0;
        }
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctxAdm = document.getElementById('chartSmtTrendAdmin');
            if (ctxAdm && typeof Chart !== 'undefined') {
                new Chart(ctxAdm, {
                    type: 'line',
                    data: {
                        labels: @json($admChartLabels),
                        datasets: [{
                            label: '% Kehadiran SMT',
                            data: @json($admChartPcts),
                            borderColor: '#003366',
                            backgroundColor: 'rgba(0, 51, 102, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#003366',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                grid: { color: '#f1f5f9' },
                                ticks: { callback: function(val) { return val + '%'; }, font: { size: 11 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' }, color: '#0f172a' }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return 'Kehadiran: ' + context.parsed.y + '%'; }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endreadonly

</div>

<style>
/* Styling Kartu KPI Clean (Dominasi Putih, Border Halus, Aksen Rapi) */
.card-kpi-clean {
    background: #ffffff !important;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.card-kpi-clean:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 31, 63, 0.08);
}
.kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}
.kpi-number {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.2;
}
.kpi-subtext {
    font-size: 12px;
    color: #64748b;
}
.kpi-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
@endsection
