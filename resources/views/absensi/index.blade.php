@extends('layouts.app')

@section('title', 'Modul Absensi Lapangan')
@section('page_heading', 'Modul Absensi Lapangan')
@section('page_subheading', 'Rekapitulasi Persentase SMT Otomatis & Presensi Harian PT Murinda Iron Steel')

@section('content')
<style>
    /* Modul Absensi — hanya style khusus pill status, table ikuti CSS global */
    .attendance-pill-group {
        display: inline-flex;
        gap: 3px;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
    }
    .att-pill {
        margin: 0;
        cursor: pointer;
        position: relative;
    }
    .att-pill input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .att-pill span {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        font-size: 11.5px;
        font-weight: 600;
        border-radius: 4px;
        color: #1e293b;
        transition: all 0.15s ease-in-out;
        user-select: none;
    }
    .att-pill:hover span {
        background: rgba(0, 0, 0, 0.05);
    }
    .att-hadir input:checked + span {
        background: #003366;
        color: #ffffff !important;
        font-weight: 700;
        border: 1px solid #002244;
        box-shadow: 0 1px 3px rgba(0, 51, 102, 0.25);
    }
    .att-telat input:checked + span {
        background: #dc2626;
        color: #ffffff !important;
        font-weight: 700;
        border: 1px solid #b91c1c;
        box-shadow: 0 1px 3px rgba(220, 38, 38, 0.25);
    }
    .att-tidak input:checked + span {
        background: #eab308;
        color: #000000 !important;
        font-weight: 700;
        border: 1px solid #ca8a04;
        box-shadow: 0 1px 3px rgba(234, 179, 8, 0.25);
    }
    .form-control-custom-table {
        font-size: 12px;
        color: #000000 !important;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
    }
    .form-control-custom-table:focus {
        border-color: #003366;
        box-shadow: 0 0 0 2px rgba(0, 51, 102, 0.15);
    }
</style>

<div class="container-fluid px-0">

    <!-- Official Header for Print -->
    <x-murinda-header
        title="REKAPITULASI & DAFTAR HADIR LAPANGAN (SMT)"
        subhead="SAFETY MORNING TALK & TOOLBOX MEETING PT MURINDA IRON STEEL"
        docNo="DOC-HSE-MIS-ABS-2026"
    />

    <!-- ======================================================== -->
    <!-- 1. HEADER AKSI & INDIKATOR TANGGAL AKTIF                  -->
    <!-- ======================================================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('absensi.setup') }}" class="btn btn-murinda-primary btn-sm fw-bold px-3 shadow-sm">
                Cetak Absensi
            </a>
            <button type="button" class="btn btn-excel btn-sm fw-bold px-3 shadow-sm" onclick="exportTableToExcel('table-rekap-smt', 'REKAP_SMT_{{ $tanggal }}', 'REKAPITULASI ABSENSI SMT {{ $tanggal }}')">
                Ekspor Excel
            </button>
            <button type="button" class="btn btn-pdf btn-sm fw-bold px-3 shadow-sm" onclick="window.print()">
                Cetak PDF
            </button>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
            <span class="badge badge-blured-gray px-3 py-2 shadow-sm text-dark" style="font-size: 12px; border-radius: 6px;">
                Tanggal Data: <strong>{{ date('d/m/Y', strtotime($tanggal)) }}</strong>
            </span>
            <span class="badge badge-blured-gray px-3 py-2 shadow-sm text-dark" style="font-size: 12px; border-radius: 6px;">
                Hari Ini: <strong>{{ date('d/m/Y') }}</strong>
            </span>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 2. KARTU KPI RESMI (SERAGAM DENGAN MODUL LAIN)            -->
    <!-- ======================================================== -->
    @php
        $totalHadir = $grandTotals['hadir'];
        $totalHarusHadir = $grandTotals['hadir'] + $grandTotals['telat'];
        $totalGaMasuk = $grandTotals['tidak_hadir'];
        $persentaseKehadiran = $totalHarusHadir > 0 ? round(($totalHadir / $totalHarusHadir) * 100, 1) : 0;
    @endphp

    <!-- ======================================================== -->
    <!-- 2. KARTU KPI RESMI (RINGKASAN MODUL ABSENSI LAPANGAN)     -->
    <!-- ======================================================== -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4 no-print">
        <!-- 1. Total Hadir -->
        <div class="col">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Total Hadir</div>
                        <h3 class="kpi-value" style="color: #003366;">{{ $totalHadir }}</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Pekerja Hadir Tepat Waktu
                        </div>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Yang Harus Hadir (Hadir + Telat) -->
        <div class="col">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Total Yang Harus Hadir</div>
                        <h3 class="kpi-value text-dark">{{ $totalHarusHadir }}</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Hadir ({{ $grandTotals['hadir'] }}) + Telat ({{ $grandTotals['telat'] }})
                        </div>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Tidak Masuk (Izin, Sakit & Alfa) -->
        <div class="col">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Total Tidak Masuk</div>
                        <h3 class="kpi-value text-danger">{{ $totalGaMasuk }}</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Izin, Sakit, & Alfa
                        </div>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Persentase Kehadiran (% = Total Hadir / Yang Harus Hadir) -->
        <div class="col">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Persentase Kehadiran</div>
                        <h3 class="kpi-value" style="color: #003366;">{{ number_format($persentaseKehadiran, 1, ',', '.') }}%</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Total Hadir / Yang Harus Hadir
                        </div>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. SMT Terendah Perminggu (Semua Merah) -->
        <div class="col">
            <div class="card-kpi-uniform p-3 h-100" style="border: 1.5px solid #f87171 !important; border-top: 4px solid #dc2626 !important; background: #fff5f5 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title text-danger fw-bold">% Total SMT Terendah (Perminggu)</div>
                        <h3 class="kpi-value text-danger fw-bold" style="color: #dc2626 !important;">
                            {{ number_format($smtWeeklyStats['lowest_percentage'] ?? 0, 1, ',', '.') }}%
                        </h3>
                        <div class="text-danger small mt-1 fw-semibold text-truncate" style="font-size: 11px; max-width: 170px;" title="{{ $smtWeeklyStats['lowest_dept_name'] ?? '-' }}">
                            @if($smtWeeklyStats['has_data'])
                                {{ $smtWeeklyStats['lowest_dept_name'] }}
                            @else
                                Belum Ada Data
                            @endif
                        </div>
                    </div>
                    <div class="kpi-icon-box" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $activeTab = request('tab', 'mingguan');
        if (auth()->user()?->role === 'hse_manager' && $activeTab === 'rekap') {
            $activeTab = 'mingguan';
        }
    @endphp

    @readonly
    <!-- ======================================================== -->
    <!-- GRAFIK TREN KEHADIRAN SMT MINGGUAN                       -->
    <!-- ======================================================== -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-12">
            <div class="card-custom h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold m-0" style="color: #003366;">
                            <i class="fa-solid fa-chart-line me-2 text-primary"></i> Rekapitulasi Kehadiran SMT Perminggu (Grafik Tren)
                        </h6>
                        <small class="text-muted" style="font-size: 11px;">Tren persentase kehadiran briefing K3LH 4 minggu terakhir</small>
                    </div>
                    <span class="badge" style="background: #003366; color: #fff; font-size: 10px;">4 Minggu Terakhir</span>
                </div>
                <div class="card-body p-3">
                    <canvas id="chartWeeklySmt" style="max-height: 240px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endreadonly

    <!-- ======================================================== -->
    <!-- 3. NAV TABS MODUL ABSENSI (3 TAB RESMI)                  -->
    <!-- ======================================================== -->
    <ul class="nav nav-tabs border-bottom mb-3 no-print" id="absensiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ ($activeTab == 'mingguan' || $activeTab == 'tren') ? 'active' : '' }} fw-bold py-2 px-3" id="mingguan-tab" data-bs-toggle="tab" data-bs-target="#mingguan-pane" type="button" role="tab" style="color: #001f3f;">
                Persentase Kehadiran SMT 4 Minggu Terakhir
            </button>
        </li>
        @canwrite
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'rekap' ? 'active' : '' }} fw-bold py-2 px-3" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#rekap-pane" type="button" role="tab" style="color: #001f3f;">
                Rekap Kehadiran
            </button>
        </li>
        @endcanwrite
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'history' ? 'active' : '' }} fw-bold py-2 px-3" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab" style="color: #001f3f;">
                Riwayat
            </button>
        </li>
    </ul>

    <div class="tab-content" id="absensiTabContent">

        <!-- ======================================================== -->
        <!-- TAB 1: PERSENTASE KEHADIRAN SMT 4 MINGGU TERAKHIR        -->
        <!-- ======================================================== -->
        <div class="tab-pane fade {{ ($activeTab == 'mingguan' || $activeTab == 'tren') ? 'show active' : '' }}" id="mingguan-pane" role="tabpanel">

            <!-- Card Rekap Kehadiran SMT Perminggu -->
            <div class="card-custom mb-3 no-print">
                <div class="card-header-custom d-flex justify-content-between align-items-center py-2.5">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-week text-primary"></i>
                        <span class="fw-bold text-navy" style="font-size: 13.5px; color: #003366;">
                            Rekapitulasi Kehadiran SMT Perminggu (4 Minggu Terakhir)
                        </span>
                    </div>
                    <span class="badge border px-2 py-0.5 text-dark" style="background: #edf2f7; font-size: 11px;">
                        Data Real-Time
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover m-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">NO</th>
                                    <th>RENTANG MINGGU</th>
                                    <th class="text-center" style="width: 120px;">TOTAL TERCATAT</th>
                                    <th class="text-center" style="width: 90px;">HADIR</th>
                                    <th class="text-center" style="width: 90px;">TELAT</th>
                                    <th class="text-center" style="width: 140px;">% SMT TOTAL</th>
                                    <th>DEPARTEMEN / SUBCONT TERENDAH</th>
                                    <th class="text-center" style="width: 110px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $wIdx = 1; @endphp
                                @foreach($smtWeeklyStats['weeks'] as $w)
                                    @php
                                        $isLowest = ($smtWeeklyStats['has_data'] && $w['persentase_total'] !== null && $w['persentase_total'] == $smtWeeklyStats['lowest_percentage']);
                                    @endphp
                                    <tr style="{{ $isLowest ? 'background-color: #fef2f2 !important;' : '' }}">
                                        <td class="text-center fw-bold text-muted">{{ $wIdx++ }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $w['label'] }}</div>
                                            @if($w['is_current_week'])
                                                <span class="badge bg-primary text-white py-0 px-2" style="font-size: 10px;">Minggu Berjalan</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold text-dark">{{ $w['total_recorded'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $w['hadir'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $w['telat'] }}</td>
                                        <td class="text-center">
                                            @if($w['persentase_total'] !== null)
                                                <span class="fw-bold fs-6 {{ $isLowest ? 'text-danger' : 'text-dark' }}">
                                                    {{ number_format($w['persentase_total'], 1, ',', '.') }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($w['lowest_dept'])
                                                <span class="fw-semibold text-dark">{{ $w['lowest_dept']['name'] }}</span>
                                                <span class="badge {{ $isLowest ? 'bg-danger text-white' : 'bg-light text-dark border' }} ms-1" style="font-size: 10.5px;">
                                                    {{ number_format($w['lowest_dept']['persentase'], 1, ',', '.') }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isLowest)
                                                <span class="badge bg-danger text-white px-2 py-1" style="font-size: 11px;">
                                                    <i class="fa-solid fa-arrow-down me-1"></i> Terendah
                                                </span>
                                            @elseif($w['persentase_total'] !== null)
                                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                                    Terekam
                                                </span>
                                            @else
                                                <span class="text-muted small">Kosong</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @canwrite
        <!-- ======================================================== -->
        <!-- TAB 2: REKAP KEHADIRAN (REKAPITULASI PERSENTASE SMT)     -->
        <!-- ======================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'rekap' ? 'show active' : '' }}" id="rekap-pane" role="tabpanel">

            <div class="card-custom">
                <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            Rekapitulasi Persentase SMT: {{ date('d/m/Y', strtotime($tanggal)) }}
                        </h5>
                        <small class="text-muted">Rekapitulasi kehadiran Safety Morning Talk seluruh departemen perusahaan, subkontraktor, dan proyek ENT</small>
                    </div>
                    <!-- Inline Tanggal Picker Bersih -->
                    <form action="{{ route('absensi.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0 no-print">
                        <input type="hidden" name="tab" value="rekap">
                        <label class="form-label mb-0 fw-bold text-dark small">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm fw-bold text-dark" style="width: 145px;" onchange="this.form.submit()">
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover m-0 align-middle" id="table-rekap-smt">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">NO</th>
                                    <th>DIVISI / SUBKONTRAKTOR</th>
                                    <th class="text-center" style="width: 100px;">KATEGORI</th>
                                    <th class="text-center" style="width: 95px;">TOTAL PEKERJA</th>
                                    <th class="text-center" style="width: 80px;">HADIR</th>
                                    <th class="text-center" style="width: 80px;">TELAT</th>
                                    <th class="text-center" style="width: 95px;">TIDAK HADIR</th>
                                    <th class="text-center" style="width: 110px;">HARUSNYA HADIR</th>
                                    <th class="text-center" style="width: 110px;">% SMT</th>
                                    <th class="text-center" style="width: 95px;">STATUS</th>
                                    <th class="text-center no-print" style="width: 110px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $globalIndex = 1; @endphp

                                @foreach($recapCategories as $categoryKey => $catData)
                                    <!-- Category Header Row -->
                                    <tr style="background-color: #f1f5f9 !important;">
                                        <td colspan="11" class="fw-bold py-2 px-3 text-dark" style="font-size: 12px; letter-spacing: 0.5px;">
                                            {{ strtoupper($catData['title']) }}
                                        </td>
                                    </tr>

                                    @foreach($catData['items'] as $itemCode => $item)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $globalIndex++ }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                                <small class="text-muted font-monospace" style="font-size: 11px;">{{ $item['code'] }}</small>
                                            </td>
                                            <td class="text-center fw-semibold text-dark">{{ $item['category'] }}</td>
                                            <td class="text-center fw-semibold text-dark">{{ $item['total_pekerja'] }}</td>
                                            <td class="text-center fw-bold text-dark">{{ $item['hadir'] }}</td>
                                            <td class="text-center fw-bold text-dark">{{ $item['telat'] }}</td>
                                            <td class="text-center fw-bold text-dark">{{ $item['tidak_hadir'] }}</td>
                                            <td class="text-center fw-bold text-dark">{{ $item['harusnya_hadir'] }}</td>
                                            <td class="text-center fw-bold text-dark">
                                                {{ number_format($item['persentase'], 1, ',', '.') }}%
                                            </td>
                                            <td class="text-center">
                                                @if($item['is_recorded'])
                                                    <span class="fw-semibold text-dark">Terisi</span>
                                                @else
                                                    <span class="text-muted">Belum Diisi</span>
                                                @endif
                                            </td>
                                            <td class="text-center no-print">
                                                <a href="{{ route('absensi.index', ['tab' => 'input', 'departemen' => $item['code'], 'tanggal' => $tanggal, 'jenis_kegiatan' => 'SMT']) }}" class="btn btn-outline-primary btn-sm py-1 px-2 fw-semibold" style="font-size: 11px;">
                                                    Buka Absen
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Category Subtotal Row -->
                                    <tr style="background-color: #f8fafc !important; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                        <td colspan="3" class="text-end fw-bold py-2 text-dark" style="font-size: 12px;">
                                            SUBTOTAL {{ strtoupper($catData['title']) }}:
                                        </td>
                                        <td class="text-center fw-bold text-dark">{{ $catData['totals']['total_pekerja'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $catData['totals']['hadir'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $catData['totals']['telat'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $catData['totals']['tidak_hadir'] }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $catData['totals']['harusnya_hadir'] }}</td>
                                        <td class="text-center fw-bold text-dark">
                                            {{ number_format($catData['totals']['persentase'], 1, ',', '.') }}%
                                        </td>
                                        <td colspan="2" class="no-print"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <!-- Grand Total Footer -->
                            <tfoot>
                                <tr style="background: #001f3f !important; color: #ffffff !important;">
                                    <td colspan="3" class="text-end fw-bold py-2 text-white" style="font-size: 12px;">
                                        TOTAL KESELURUHAN PERUSAHAAN:
                                    </td>
                                    <td class="text-center fw-bold text-white">{{ $grandTotals['total_pekerja'] }}</td>
                                    <td class="text-center fw-bold text-white">{{ $grandTotals['hadir'] }}</td>
                                    <td class="text-center fw-bold text-white">{{ $grandTotals['telat'] }}</td>
                                    <td class="text-center fw-bold text-white">{{ $grandTotals['tidak_hadir'] }}</td>
                                    <td class="text-center fw-bold text-white">{{ $grandTotals['harusnya_hadir'] }}</td>
                                    <td class="text-center fw-bold text-white fs-6">
                                        {{ number_format($grandTotals['persentase'], 1, ',', '.') }}%
                                    </td>
                                    <td colspan="2" class="no-print text-center text-white-50 small">Selesai</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcanwrite

        <!-- TAB 3: RIWAYAT ABSENSI (FILTER INLINE TERINTEGRASI)     -->
        <!-- ======================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'history' ? 'show active' : '' }}" id="history-pane" role="tabpanel">
            <div class="card-custom">
                <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            Log Riwayat Absensi
                        </h5>
                        <small class="text-muted">Total: <strong>{{ $historyLogs->count() }}</strong> record absensi tercatat</small>
                    </div>
                    <button type="button" onclick="exportTableToExcel('table-history-absensi', 'Riwayat_Absensi_HSE', 'LOG HISTORI ABSENSI')" class="btn btn-excel btn-sm px-3 fw-bold no-print">
                        Ekspor Excel
                    </button>
                </div>

                <!-- Slim Inline Filter Bar Khusus Riwayat -->
                <div class="p-3 bg-light border-bottom no-print">
                    <form action="{{ route('absensi.index') }}" method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="history">
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11px;">Mulai Tanggal:</label>
                            <input type="date" name="hist_start_date" value="{{ request('hist_start_date') }}" class="form-control form-control-sm form-control-custom-table fw-semibold" onchange="this.form.submit()">
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11px;">Sampai Tanggal:</label>
                            <input type="date" name="hist_end_date" value="{{ request('hist_end_date') }}" class="form-control form-control-sm form-control-custom-table fw-semibold" onchange="this.form.submit()">
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label class="form-label mb-1 fw-bold text-dark" style="font-size: 11px;">Filter Subcont / Dept:</label>
                            <select name="hist_dept" class="form-select form-select-sm form-control-custom-table fw-semibold" onchange="this.form.submit()">
                                <option value="SEMUA">-- Semua Subcont & Dept --</option>
                                @foreach($selectableDivisions as $d)
                                    <option value="{{ $d }}" {{ request('hist_dept') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-murinda-primary btn-sm w-100 fw-bold">
                                <i class="fa-solid fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('absensi.index', ['tab' => 'history']) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover m-0 align-middle" id="table-history-absensi">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">NO</th>
                                    <th class="text-center" style="width: 100px;">TANGGAL</th>
                                    <th class="text-center" style="width: 80px;">KEGIATAN</th>
                                    <th>NAMA PERSONEL</th>
                                    <th class="text-center" style="width: 140px;">SUBCONT / DEPT</th>
                                    <th class="text-center" style="width: 120px;">KEHADIRAN</th>
                                    <th>KETERANGAN</th>
                                    <th>ALASAN PERUBAHAN</th>
                                    <th class="text-center no-print" style="width: 100px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyLogs as $idx => $hl)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td class="text-center fw-semibold text-dark" style="font-size: 11.5px;">{{ date('d/m/Y', strtotime($hl->tanggal)) }}</td>
                                    <td class="text-center font-monospace text-dark">{{ $hl->jenis_kegiatan }}</td>
                                    <td class="fw-bold text-dark">{{ $hl->nama_personel }}</td>
                                    <td class="text-center font-monospace text-dark">{{ $hl->departemen_subcont }}</td>
                                    <td class="text-center fw-semibold text-dark">
                                        {{ $hl->status_kehadiran }}
                                    </td>
                                    <td class="text-dark" style="font-size: 12px;">{{ $hl->keterangan ?: '-' }}</td>
                                    <td class="text-muted italic" style="font-size: 12px;">{{ $hl->alasan_perubahan ?: '-' }}</td>
                                    <td class="text-center no-print">
                                        <button type="button" class="btn btn-outline-warning btn-sm py-1 px-2 fw-semibold" style="font-size: 11px;"
                                                onclick="openEditHistoryModal('{{ $hl->id }}', '{{ addslashes($hl->nama_personel) }}', '{{ $hl->status_kehadiran }}', '{{ addslashes($hl->keterangan) }}', '{{ addslashes($hl->alasan_perubahan) }}')">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Koreksi
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada histori data absensi yang tersimpan sesuai filter.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Edit Status Kehadiran khusus Riwayat Absensi -->
<div class="modal fade" id="modalEditHistoryAbsensi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header text-white" style="background: #001f3f;">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Status Absensi Pekerja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('absensi.updateSingle') }}" method="POST">
                @csrf
                <input type="hidden" name="absensi_id" id="hist-absensi-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nama Pekerja</label>
                        <input type="text" id="hist-nama-pekerja" class="form-control form-control-custom-table bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Status Kehadiran Baru <span class="text-danger">*</span></label>
                        <select name="status_kehadiran" id="hist-status-kehadiran" class="form-select form-control-custom-table" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Telat">Telat</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Keterangan</label>
                        <input type="text" name="keterangan" id="hist-keterangan" class="form-control form-control-custom-table" placeholder="Contoh: Sakit / Izin / Kesiangan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Alasan Perubahan Data <span class="text-danger">*</span></label>
                        <textarea name="alasan_perubahan" id="hist-alasan" class="form-control form-control-custom-table" rows="2" placeholder="Tuliskan alasan perbaikan status..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-murinda-primary btn-sm"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditHistoryModal(absensiId, nama, currentStatus, keterangan, alasan) {
        document.getElementById('hist-absensi-id').value = absensiId;
        document.getElementById('hist-nama-pekerja').value = nama;
        document.getElementById('hist-status-kehadiran').value = currentStatus || 'Hadir';
        document.getElementById('hist-keterangan').value = keterangan || '';
        document.getElementById('hist-alasan').value = alasan || '';
        new bootstrap.Modal(document.getElementById('modalEditHistoryAbsensi')).show();
    }

    // Live SMT Calculator while checking radio buttons in Form Absensi Harian
    function recalculateLiveSmt() {
        const radios = document.querySelectorAll('.attendance-radio:checked');
        let hadir = 0;
        let telat = 0;
        let tidakHadir = 0;

        radios.forEach(function(r) {
            if (r.value === 'Hadir') {
                hadir++;
            } else if (r.value === 'Telat') {
                telat++;
            } else {
                tidakHadir++;
            }
        });

        const harusnyaHadir = hadir + telat;
        const persentase = (harusnyaHadir > 0) ? ((hadir / harusnyaHadir) * 100).toFixed(1) : '0.0';

        const elHadir = document.getElementById('live-hadir');
        const elTelat = document.getElementById('live-telat');
        const elTidakHadir = document.getElementById('live-tidak-hadir');
        const elHarusnya = document.getElementById('live-harusnya-hadir');
        const elPersen = document.getElementById('live-persentase');

        if (elHadir) elHadir.textContent = hadir;
        if (elTelat) elTelat.textContent = telat;
        if (elTidakHadir) elTidakHadir.textContent = tidakHadir;
        if (elHarusnya) elHarusnya.textContent = harusnyaHadir;
    }

    document.addEventListener('DOMContentLoaded', function() {
        recalculateLiveSmt();

        @readonly
        // Chart Tren Mingguan SMT untuk HSE Manager
        const ctxWeeklySmt = document.getElementById('chartWeeklySmt');
        if (ctxWeeklySmt && typeof Chart !== 'undefined') {
            const weekLabels = {!! json_encode(array_column($smtWeeklyStats['weeks'] ?? [], 'label')) !!};
            const weekData = {!! json_encode(array_map(fn($w) => $w['persentase_total'] ?? 0, $smtWeeklyStats['weeks'] ?? [])) !!};

            new Chart(ctxWeeklySmt, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: '% Kehadiran SMT',
                        data: weekData,
                        borderColor: '#003366',
                        backgroundColor: 'rgba(0, 51, 102, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#003366',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ctx.raw + '%' } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Persentase (%)' }
                        }
                    }
                }
            });
        }

        
        @endreadonly
    });
</script>
@endpush
