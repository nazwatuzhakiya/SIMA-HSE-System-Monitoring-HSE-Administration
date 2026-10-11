@extends('layouts.app')

@php
    $isProyek = ($scope ?? 'plant') === 'proyek';
    $currentHeading = $pageTitle ?? ($isProyek ? 'Data Manpower Proyek (ENT) per Periode' : 'Data Manpower Subcont per Periode');
@endphp

@section('title', $currentHeading)
@section('page_heading', $currentHeading)
@section('page_subheading', $isProyek ? 'Data Manpower Proyek (ENT) per Periode' : 'Data Manpower Subcont per Periode')

@section('content')
<div class="container-fluid px-0">

    <!-- Official Header Document for Print View -->
    <x-murinda-header
        title="{{ strtoupper($currentHeading) }}"
        subhead="PERIODE: {{ $selectedPeriod ? strtoupper($selectedPeriod['label']) : 'REKAP TAHUN ' . $selectedYear }}"
        docNo="DOC-HSE-MIS-MP-{{ $selectedYear }}"
    />

    <!-- ======================================================== -->
    <!-- 1. RINGKASAN RESMI DI PALING ATAS (4 KARTU KPI SEIMBANG)  -->
    <!-- ======================================================== -->
    <div class="row g-3 mb-4 no-print">
        <!-- 1. Pekerja Aktif Bulan Ini -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">
                            {{ $selectedPeriod ? 'Pekerja Aktif Bulan Ini' : 'Total Pekerja Aktif' }}
                        </div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $activeCount }} <span class="fs-6 fw-normal text-muted">Orang</span></h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            {{ $selectedPeriod ? $selectedPeriod['month_name'] . ' ' . $selectedYear : 'Tahun ' . $selectedYear }}
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #e8f5e9; color: #2e7d32;">
                        <i class="fa-solid fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Masuk & Keluar Bulan Ini -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">
                            Masuk & Keluar Bulan Ini
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <span class="fs-4 fw-bold text-primary">+{{ $masukCount ?? 0 }}</span>
                            <span class="text-muted" style="font-size: 16px;">/</span>
                            <span class="fs-4 fw-bold text-danger">-{{ $keluarCount ?? 0 }}</span>
                            <span class="text-muted small">Orang</span>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            <span class="text-primary fw-semibold">{{ $masukCount ?? 0 }} Masuk</span> &bull; <span class="text-danger fw-semibold">{{ $keluarCount ?? 0 }} Keluar</span>
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-users-gear fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Man Days -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Man Days</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ number_format($aggregateStats['total_man_days'] ?? 0, 0, ',', '.') }}</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">
                            Hari Kerja Riil
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Akumulasi Man Hours -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Akumulasi Man Hours</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ number_format($aggregateStats['total_man_hours'] ?? 0, 0, ',', '.') }} <small class="fs-6 fw-normal text-muted">Jam</small></h3>
                        @canwrite
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <button type="button" class="btn btn-murinda-outline btn-sm py-0 px-2 fw-bold" style="font-size: 10.5px;" data-bs-toggle="modal" data-bs-target="#modalRekapManHours">
                                Detail Rekap
                            </button>
                        </div>
                        @endcanwrite
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-business-time fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @readonly
    <!-- ======================================================== -->
    <!-- STATISTIK PERBANDINGAN TOTAL MANPOWER PER BULAN (MANAGER) -->
    <!-- ======================================================== -->
    <div class="card-custom mb-4 no-print">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold m-0" style="color: #003366;">
                    <i class="fa-solid fa-chart-column me-2"></i> Statistik Perbandingan Manpower Per Bulan (Tahun {{ $selectedYear }})
                </h6>
                <small class="text-muted" style="font-size: 11px;">
                    Grafik perbandingan 12 siklus bulanan (26 s/d 25). Klik batang bulan pada grafik untuk mengubah data ringkasan dan tabel secara langsung.
                </small>
            </div>
            <span class="badge" style="background: #003366; color: #ffffff; font-size: 10.5px;">12 Siklus Bulanan</span>
        </div>
        <div class="card-body p-3">
            <!-- Grafik Tren Bulanan (Klik Batang untuk Pilih Bulan) -->
            <div class="p-2 bg-light rounded border">
                <canvas id="chartMonthlyManpower" style="max-height: 220px; width: 100%; cursor: pointer;" title="Klik salah satu bulan untuk melihat detail"></canvas>
            </div>
        </div>
    </div>
    @endreadonly

    <!-- ======================================================== -->
    <!-- 2. TOOLBAR AKSI & FILTER CARD TEPAT DI ATAS TABEL DATA   -->
    <!-- ======================================================== -->
    <div class="card-custom mb-3 no-print">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @canwrite
                    <button type="button" class="btn btn-murinda-outline btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRekapManHours">
                        Rekap Man Hours & Days
                    </button>
                    @endcanwrite
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-excel btn-sm fw-bold px-3 shadow-sm" onclick="exportTableToExcel('table-manpower', 'REKAP_MANPOWER_{{ $selectedPeriodKey }}_{{ $selectedYear }}', 'DATA MANPOWER {{ strtoupper($currentHeading) }}')">
                        Ekspor Excel
                    </button>
                    <button type="button" class="btn btn-pdf btn-sm fw-bold px-3 shadow-sm" onclick="window.print()">
                        Cetak PDF
                    </button>
                </div>
            </div>

            <!-- Form Filter Parameter -->
            <form action="{{ $isProyek ? route('manpower.proyek') : route('manpower.index') }}" method="GET" class="row g-2 align-items-end" id="form-filter-manpower">
                <!-- Tahun -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Tahun Kerja:</label>
                    <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Periode Siklus (26 s/d 25) -->
                <div class="col-lg-3 col-md-5 col-sm-6">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Pilih Periode:</label>
                    <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all_year" {{ $selectedPeriodKey == 'all_year' ? 'selected' : '' }}>
                            -- Seluruh Periode (Rekap Tahun {{ $selectedYear }}) --
                        </option>
                        @foreach($periods as $pkey => $pdata)
                            <option value="{{ $pkey }}" {{ $selectedPeriodKey == $pkey ? 'selected' : '' }}>
                                {{ $pdata['month_name'] }} [{{ $pdata['label'] }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcont / Departemen -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Bagian / Dept:</label>
                    <select name="departemen" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Bagian</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Pekerja -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Status:</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <!-- Cari Nama Personel Langsung (Live Instant Filter on Typing) -->
                <div class="col-lg-3 col-md-9 col-sm-12">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Cari Personel:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="live_search_manpower" class="form-control" placeholder="Ketik nama / NIP pekerja...">
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- ======================================================== -->
    <!-- 3. TABEL DATA MANPOWER RESMI (READ-ONLY REPORTING)       -->
    <!-- ======================================================== -->
    <div class="card-custom">
        <div class="card-body p-0 table-responsive">
            <table class="table table-custom align-middle mb-0" id="table-manpower">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 45px;" class="text-center">No</th>
                        <th style="width: 160px;">No. ID / NIP</th>
                        <th style="min-width: 220px;">Nama Personel</th>
                        <th style="min-width: 150px;" class="text-center">Subcont / Dept</th>
                        <th style="min-width: 180px;">Posisi / Jabatan</th>
                        <th style="width: 140px;" class="text-center">Status Periode</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp
                    @forelse($groupedManpowers as $deptName => $deptWorkers)
                        <!-- Header Kelompok Subcont / Departemen (Tanpa Duplikasi Nama Dept) -->
                        <tr style="background: #f1f5f9; border-top: 2px solid #cbd5e1;">
                            <td colspan="6" class="fw-bold py-2 px-3 text-dark" style="font-size: 12px; letter-spacing: 0.3px;">
                                <strong class="me-2 text-dark">{{ $deptName }}</strong>
                                ({{ $deptWorkers->count() }} Personel)
                                @if(isset($aggregateStats['breakdown'][$deptName]))
                                    <span class="text-muted fw-normal ms-2 small">
                                        | Man Days: <strong>{{ number_format($aggregateStats['breakdown'][$deptName]['man_days'], 0, ',', '.') }}</strong>
                                        | Man Hours: <strong>{{ number_format($aggregateStats['breakdown'][$deptName]['man_hours'], 0, ',', '.') }} Jam</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>

                        @foreach($deptWorkers as $m)
                            @php
                                $effStat = $m->status_periode ?? $m->status;
                            @endphp
                            <tr style="{{ $effStat === 'Non-Aktif' ? 'opacity: 0.75; background-color: #f8fafc;' : '' }}">
                                <td class="text-center system-paten">{{ $rowNumber++ }}</td>
                                <td class="system-paten">{{ $m->no_id }}</td>
                                <td class="fw-bold text-dark">{{ $m->nama }}</td>
                                <td class="text-center font-monospace">{{ $m->departemen }}</td>
                                <td>{{ $m->jabatan }}</td>
                                <td class="text-center">
                                    {{ $effStat }}
                                    <small class="d-block text-muted mt-1" style="font-size: 10px;">
                                        {{ $m->man_days_periode ?? 0 }} hari | {{ $m->man_hours_periode ?? 0 }} jam
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Tidak ada data manpower ditemukan pada filter periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


</div>

<!-- MODAL REKAP MAN HOURS & MAN DAYS PER DEPARTEMEN / SUBCONT -->
<div class="modal fade" id="modalRekapManHours" tabindex="-1" aria-labelledby="modalRekapManHoursLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-white" id="modalRekapManHoursLabel">
                    <i class="fa-solid fa-business-time me-2 text-danger"></i> Rekapitulasi Man Hours & Man Days per Bagian
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="border rounded p-3 mb-3" style="background: #edf2f7; border-left: 4px solid #003366 !important; font-size: 12px; color: #003366;">
                    <i class="fa-solid fa-calculator me-1"></i> Rumus Jam Kerja Resmi PT Murinda Iron Steel:
                    <strong>Senin s/d Rabu: 7 jam</strong> | <strong>Kamis: 8 jam</strong> | <strong>Jumat: 7 jam</strong> | <strong>Sabtu: 4 jam</strong> | <strong>Minggu: 0 jam (Libur)</strong>.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Departemen / Subcont</th>
                                <th class="text-center">Total Personel</th>
                                <th class="text-center">Aktif</th>
                                <th class="text-center">Non-Aktif</th>
                                <th class="text-center">Total Man Days</th>
                                <th class="text-center">Total Man Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aggregateStats['breakdown'] as $deptKey => $bData)
                                <tr>
                                    <td class="fw-bold">{{ $bData['entity'] }}</td>
                                    <td class="text-center">{{ $bData['total_personel'] }}</td>
                                    <td class="text-center fw-bold">{{ $bData['active_personel'] }}</td>
                                    <td class="text-center text-muted">{{ $bData['non_active_personel'] }}</td>
                                    <td class="text-center fw-bold">{{ number_format($bData['man_days'], 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold">{{ number_format($bData['man_hours'], 0, ',', '.') }} Jam</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data akumulasi.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TOTAL KESELURUHAN</td>
                                <td class="text-center">{{ $totalCount }}</td>
                                <td class="text-center">{{ $activeCount }}</td>
                                <td class="text-center text-muted">{{ $nonAktifCount }}</td>
                                <td class="text-center">{{ number_format($aggregateStats['total_man_days'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($aggregateStats['total_man_hours'] ?? 0, 0, ',', '.') }} Jam</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-murinda-outline btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Instant Live Filter on Typing in Manpower (Tanpa perlu enter / klik tombol)
    document.addEventListener('DOMContentLoaded', function() {
        const mpSearch = document.getElementById('live_search_manpower');
        if (mpSearch) {
            mpSearch.addEventListener('input', function() {
                const q = (this.value || '').toLowerCase().trim();
                const rows = document.querySelectorAll('#table-manpower tbody tr');
                let currentGroupHeader = null;
                let groupHasVisibleWorker = false;

                rows.forEach(row => {
                    if (row.cells.length === 1 && row.cells[0].getAttribute('colspan') === '6') {
                        if (currentGroupHeader) {
                            currentGroupHeader.style.display = (!q || groupHasVisibleWorker) ? '' : 'none';
                        }
                        currentGroupHeader = row;
                        groupHasVisibleWorker = false;
                    } else if (row.cells.length >= 5) {
                        const rowText = row.innerText.toLowerCase();
                        const match = !q || rowText.includes(q);
                        row.style.display = match ? '' : 'none';
                        if (match) groupHasVisibleWorker = true;
                    }
                });

                if (currentGroupHeader) {
                    currentGroupHeader.style.display = (!q || groupHasVisibleWorker) ? '' : 'none';
                }
            });
        }

        @readonly
        // Data 12 Siklus Bulanan untuk Interaktivitas Klik Grafik Manpower
        const monthlyData = {!! json_encode($monthlyComparison ?? []) !!};
        const periodKeys = Object.keys(monthlyData);
        const currentSelectedKey = '{{ $selectedPeriodKey }}';

        // Chart Perbandingan Manpower Bulanan untuk HSE Manager
        const ctxMpMonth = document.getElementById('chartMonthlyManpower');
        if (ctxMpMonth && typeof Chart !== 'undefined') {
            const labelsMp = {!! json_encode(array_values(array_column($monthlyComparison ?? [], 'month_name'))) !!};
            const dataMp = {!! json_encode(array_values(array_column($monthlyComparison ?? [], 'total_active'))) !!};
            const dataHours = {!! json_encode(array_values(array_column($monthlyComparison ?? [], 'man_hours'))) !!};

            // Beri warna pembeda untuk bulan yang sedang aktif dipilih
            const barColors = periodKeys.map(k => (k === currentSelectedKey ? '#10b981' : '#003366'));

            new Chart(ctxMpMonth, {
                type: 'bar',
                data: {
                    labels: labelsMp,
                    datasets: [
                        {
                            label: 'Total Manpower (Orang)',
                            data: dataMp,
                            backgroundColor: barColors,
                            borderRadius: 4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Man Hours (Jam Kerja)',
                            data: dataHours,
                            type: 'line',
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const pKey = periodKeys[index];
                            const mData = monthlyData[pKey];

                            if (mData && mData.is_frozen) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Periode Belum Berjalan',
                                        text: 'Data manpower untuk periode ' + mData.month_name + ' {{ $selectedYear }} belum tersedia karena siklus belum dimulai.',
                                        confirmButtonColor: '#003366'
                                    });
                                } else {
                                    alert('Periode ' + mData.month_name + ' {{ $selectedYear }} belum tersedia karena siklus belum dimulai.');
                                }
                                return;
                            }

                            if (pKey) {
                                const form = document.getElementById('form-filter-manpower');
                                const selectPeriode = form ? form.querySelector('select[name="periode"]') : null;
                                if (selectPeriode) {
                                    selectPeriode.value = pKey;
                                    form.submit();
                                } else {
                                    window.location.href = "{{ route($isProyek ? 'manpower.proyek' : 'manpower.index') }}?tahun={{ $selectedYear }}&periode=" + pKey;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                footer: (tooltipItems) => {
                                    if (tooltipItems.length > 0) {
                                        const idx = tooltipItems[0].dataIndex;
                                        const pKey = periodKeys[idx];
                                        const d = monthlyData[pKey];
                                        if (d && d.is_frozen) return '(Periode Belum Berjalan)';
                                        return pKey === currentSelectedKey ? '✓ Periode Sedang Ditampilkan' : '👉 Klik batang untuk beralih ke periode ini';
                                    }
                                    return '';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Pekerja' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Total Jam' }
                        }
                    }
                }
            });
        }
        @endreadonly
    });
</script>
@endpush
@endsection
