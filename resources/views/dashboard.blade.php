@extends('layouts.app')

@section('title', 'Dashboard Utama HSE')
@section('page_heading', 'Dashboard Utama HSE')
@section('page_subheading', 'PT Murinda Iron Steel Plant Kalijati Subang')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h5 class="fw-bold mb-0 text-navy" style="color: #003366;">
            <i class="fa-solid fa-chart-pie me-2 text-primary"></i> Ringkasan Operasional HSE
        </h5>
    </div>
    <div class="d-flex gap-2">
        <span class="badge shadow-sm" style="background: rgba(226, 232, 240, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); color: #003366; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 700; padding: 7px 14px;">
            <i class="fa-regular fa-clock me-1 text-primary"></i> Periode (26 s/d 25): {{ $currentPeriodLabel }}
        </span>
        <span class="badge shadow-sm" style="background: rgba(226, 232, 240, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; padding: 7px 14px;">
            <i class="fa-regular fa-calendar-days me-1" style="color: #003366;"></i> Hari Ini: {{ date('d/m/Y') }}
        </span>
    </div>
</div>

<!-- 4 Core Summary Cards (Clickable Interactive Navigation) -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('manpower.index', ['status' => 'Aktif']) }}" class="text-decoration-none d-block h-100">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Manpower Aktif</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $totalManpowerAktif }}</h3>
                        <div class="small mt-1" style="font-size: 11px; color: #0f5132;">
                            Siap Kerja Periode Ini
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subcont.index') }}" class="text-decoration-none d-block h-100">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Monitoring Seluruh Pekerja</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $totalSeluruhPersonel }}</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Master Database Kontraktor
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-building-user fa-2x"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-kpi-uniform p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Akumulasi Man Hours</div>
                    <h3 class="fw-bold m-0" style="color: #003366;">{{ number_format($aggregateStats['total_man_hours'] ?? 0, 0, ',', '.') }} <small class="fs-6 fw-normal text-muted">Jam</small></h3>
                    <div class="text-muted small mt-1" style="font-size: 11px;">
                        Total: {{ number_format($aggregateStats['total_man_days'] ?? 0, 0, ',', '.') }} Man Days
                    </div>
                </div>
                <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                    <i class="fa-solid fa-business-time fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('apd.index') }}" class="text-decoration-none d-block h-100">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total APD Terdistribusi</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $totalDistribusiApd }} <small class="fs-6 fw-normal text-muted">Pcs</small></h3>
                        <div class="small mt-1" style="font-size: 11px; color: #0f5132;">
                            Logistik APD Tercatat
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-hard-hat fa-2x"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Quick Action Panels: SMT / TBM Absensi, Manpower & Jam Kerja, Logistik APD -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-kpi-uniform p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 rounded-3" style="background: #edf2f7; color: #003366;">
                    <i class="fa-solid fa-clipboard-user fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0" style="color: #003366;">Presensi SMT & TBM Harian</h6>
                    <small class="text-muted">Cetak lembar daftar hadir resmi</small>
                </div>
            </div>
            <p class="text-muted small mb-3">
                Pelaksanaan <em>Safety Morning Talk</em> & <em>Tool Box Meeting</em> harian secara otomatis.
            </p>
            <a href="{{ route('absensi.setup') }}" class="btn btn-murinda-primary btn-sm px-3 fw-bold">
                Cetak Presensi
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-kpi-uniform p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 rounded-3" style="background: #edf2f7; color: #003366;">
                    <i class="fa-solid fa-user-clock fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0" style="color: #003366;">Manpower & Jam Kerja</h6>
                    <small class="text-muted">Perhitungan Man Hours & Man Days</small>
                </div>
            </div>
            <p class="text-muted small mb-3">
                Rekap jam kerja aman dan kehadiran harian pekerja departemen dan subkontraktor.
            </p>
            <a href="{{ route('manpower.index') }}" class="btn btn-murinda-primary btn-sm px-3 fw-bold">
                Rekap Manpower
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-kpi-uniform p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 rounded-3" style="background: #edf2f7; color: #003366;">
                    <i class="fa-solid fa-boxes-stacked fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0" style="color: #003366;">Pendataan & Logistik APD</h6>
                    <small class="text-muted">Distribusi & inventaris perlengkapan K3</small>
                </div>
            </div>
            <p class="text-muted small mb-3">
                Pencatatan serah terima alat pelindung diri bagi pekerja pabrik dan proyek.
            </p>
            <a href="{{ route('apd.index') }}" class="btn btn-murinda-primary btn-sm px-3 fw-bold">
                Kelola APD
            </a>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 31, 63, 0.12);
}
.hover-subcont-tile {
    transition: all 0.2s ease;
}
.hover-subcont-tile:hover {
    background-color: #e0f2fe !important;
    border-color: #0284c7 !important;
    transform: translateY(-2px);
}
</style>
@endsection
