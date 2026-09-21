@extends('layouts.app')

@section('title', 'Pendataan & Logistik APD Tenaga Kerja')
@section('page_heading', 'Pendataan & Distribusi Alat Pelindung Diri (APD)')
@section('page_subheading', 'Monitoring & Pencatatan Logistik K3 Tenaga Kerja PT Murinda Iron Steel')

@section('content')
<div class="container-fluid px-0">

    <!-- Official Header Document for Print View -->
    <x-murinda-header
        title="BUKTI DISTRIBUSI & LOGISTIK APD PEKERJA"
        subhead="DEPARTEMEN K3 (HSE) PT MURINDA IRON STEEL"
        docNo="DOC-HSE-MIS-APD-2026"
    />

    <!-- Summary KPI Uniform Cards (APD Terdistribusi) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Safety Helmet</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $apdStats['Helmet'] }} Pcs</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">Pelindung Kepala Terdata</div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-hard-hat fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Rompi Reflective</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $apdStats['Rompi Reflective'] }} Pcs</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">Visibilitas & Identifikasi</div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-vest fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Sepatu Safety</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $apdStats['Sepatu Safety'] }} Pcs</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">Pelindung Kaki Konstruksi</div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-shoe-prints fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-kpi-uniform p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Body Harness</div>
                        <h3 class="fw-bold m-0" style="color: #003366;">{{ $apdStats['Body Harness'] }} Pcs</h3>
                        <div class="text-muted small mt-1" style="font-size: 11px;">Bekerja di Ketinggian</div>
                    </div>
                    <div class="p-3 rounded" style="background: #edf2f7; color: #003366;">
                        <i class="fa-solid fa-person-falling fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Aksi Utama: Tombol Input APD Baru -->
    <div class="card-custom mb-3 no-print">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0" style="color: #003366;">
                        <i class="fa-solid fa-boxes-stacked me-1 text-primary"></i> Rekap Riwayat Distribusi APD
                    </h5>
                    <span class="badge px-2.5 py-1 font-monospace" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1;">
                        {{ $logs->count() }} Transaksi
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <!-- Tombol Utama: Hanya 1 Tombol Input Pengambilan APD Baru Pindah Form -->
                    <a href="{{ route('apd.create') }}" class="btn btn-gradient-blue btn-sm fw-bold px-3 shadow-sm" title="Formulir Input Penyerahan APD Baru">
                        <i class="fa-solid fa-cart-plus me-1"></i> Input Pengambilan APD Baru
                    </a>
                    <button type="button" onclick="exportTableToExcel('table-apd', 'Log_Distribusi_APD_Murinda')" class="btn btn-excel btn-sm px-3 fw-bold">
                        <i class="fa-solid fa-file-excel me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-light border btn-sm px-3" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>

            <!-- Filter Bar Transaksi APD -->
            <form action="{{ route('apd.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Subcont / Departemen:</label>
                    <select name="departemen" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Subcont & Departemen</option>
                        @foreach($departments as $d)
                            <option value="{{ $d }}" {{ $selectedDept == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-4">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Rentang Tanggal Pengambilan:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="start_date" value="{{ request('start_date') }}" class="form-control flatpickr-date" placeholder="DD/MM/YYYY">
                        <span class="input-group-text bg-light">s/d</span>
                        <input type="text" name="end_date" value="{{ request('end_date') }}" class="form-control flatpickr-date" placeholder="DD/MM/YYYY">
                    </div>
                </div>

                <div class="col-lg-3 col-md-4">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Cari Nama / Jenis APD:</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Ketik nama personel / APD...">
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['departemen', 'start_date', 'end_date', 'search']))
                        <a href="{{ route('apd.index') }}" class="btn btn-light border btn-sm" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat APD Lengkap -->
    <div class="card-custom">
        <div class="card-body p-0 table-responsive">
            <table class="table table-custom align-middle mb-0" id="table-apd">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 45px;" class="text-center">No</th>
                        <th style="width: 105px;" class="text-center">Tanggal</th>
                        <th style="min-width: 170px;">Nama Personel</th>
                        <th style="min-width: 130px;" class="text-center">Subcont / Dept</th>
                        <th style="min-width: 200px;">Jenis APD Diberikan</th>
                        <th style="width: 85px;" class="text-center">Jumlah</th>
                        <th style="min-width: 140px;">Keterangan</th>
                        <th style="width: 130px;" class="text-center no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $idx => $l)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td class="text-center fw-semibold text-dark" style="font-size: 12px;">
                                {{ date('d/m/Y', strtotime($l->tanggal_pengambilan)) }}
                            </td>
                            <td class="fw-bold text-dark">
                                {{ $l->nama_personel }}
                            </td>
                            <td class="text-center">
                                <span class="badge px-2.5 py-1 font-monospace" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1;">
                                    {{ $l->departemen_subcont }}
                                </span>
                            </td>
                            <td class="fw-bold" style="color: #003366;">{{ $l->jenis_apd }}</td>
                            <td class="text-center">
                                <span class="badge px-2 py-1" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1;">
                                    {{ $l->jumlah }} Pcs
                                </span>
                            </td>
                            <td class="small text-muted">{{ $l->keterangan ?: '-' }}</td>
                            <td class="text-center no-print">
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 fw-semibold"
                                        onclick="showRiwayatApd('{{ addslashes($l->nama_personel) }}', '{{ addslashes($l->departemen_subcont) }}')">
                                    <i class="fa-solid fa-receipt text-danger me-1"></i> Riwayat APD
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fa-solid fa-boxes-packing fa-2x mb-2 d-block text-secondary"></i>
                                Tidak ada catatan distribusi APD yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL RIWAYAT PENGAMBILAN APD PER NAMA PEKERJA            -->
<!-- ======================================================== -->
<div class="modal fade" id="modalRiwayatApd" tabindex="-1" aria-labelledby="modalRiwayatApdLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-receipt text-danger fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalRiwayatApdLabel">Kartu Riwayat Pengambilan APD Pekerja</h6>
                        <small class="text-white-50" style="font-size: 11px;">Catatan Distribusi Logistik Alat Pelindung Diri (APD) PT Murinda Iron Steel</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Pekerja -->
                <div class="card bg-light border mb-3">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-7">
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 11px;">Nama Personel / Tenaga Kerja</div>
                                <h5 class="fw-bold mb-0 text-dark" id="riwayat_nama_disp">-</h5>
                                <div class="text-muted small mt-1">
                                    Departemen / Subcont: <span class="badge bg-primary text-white" id="riwayat_dept_disp">-</span>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="badge bg-white text-dark border p-2 shadow-sm text-start text-md-end">
                                    <div class="small text-muted" style="font-size: 11px;">Total APD Yang Pernah Diambil</div>
                                    <div class="fs-5 fw-bold text-danger" id="riwayat_total_disp">0 Pcs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat APD -->
                <div class="table-responsive border rounded mb-2">
                    <table class="table table-sm table-hover align-middle mb-0" id="table-riwayat-detail">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th style="width: 45px;" class="text-center">No</th>
                                <th style="width: 110px;" class="text-center">Tanggal</th>
                                <th>Jenis APD Diberikan</th>
                                <th style="width: 90px;" class="text-center">Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat_tbody">
                            <!-- Injected by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <small class="text-muted fst-italic">
                    <i class="fa-solid fa-circle-info me-1"></i> Riwayat di atas merangkum seluruh transaksi penyerahan APD (sepatu safety, rompi, helm, kacamata, dll) untuk nama pekerja ini.
                </small>
            </div>
            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm" onclick="cetakRiwayatApdPrint()">
                    <i class="fa-solid fa-print me-1"></i> Cetak Riwayat Pengambilan APD
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const allApdLogs = @json($allApdLogs ?? $logs);
    let currentRiwayatData = {
        nama: '',
        dept: '',
        items: []
    };

    function showRiwayatApd(nama, dept) {
        const targetNama = (nama || '').trim().toLowerCase();
        currentRiwayatData.nama = nama;
        currentRiwayatData.dept = dept;
        currentRiwayatData.items = allApdLogs.filter(item => (item.nama_personel || '').trim().toLowerCase() === targetNama);

        document.getElementById('riwayat_nama_disp').innerText = nama;
        document.getElementById('riwayat_dept_disp').innerText = dept;

        let totalPcs = 0;
        let tbodyHtml = '';

        if (currentRiwayatData.items.length === 0) {
            tbodyHtml = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fa-solid fa-boxes-packing fa-2x mb-2 d-block text-secondary"></i>
                        Belum ada riwayat pengambilan APD untuk pekerja ini.
                    </td>
                </tr>
            `;
        } else {
            currentRiwayatData.items.forEach((item, idx) => {
                totalPcs += parseInt(item.jumlah || 1);
                let formattedDate = item.tanggal_pengambilan || '-';
                if (item.tanggal_pengambilan) {
                    const d = new Date(item.tanggal_pengambilan);
                    if (!isNaN(d.getTime())) {
                        formattedDate = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
                    }
                }
                tbodyHtml += `
                    <tr>
                        <td class="text-center fw-bold text-muted">${idx + 1}</td>
                        <td class="text-center fw-semibold text-dark">${escapeModalHtml(formattedDate)}</td>
                        <td class="fw-bold" style="color: #003366;">${escapeModalHtml(item.jenis_apd)}</td>
                        <td class="text-center">
                            <span class="badge px-2 py-1" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1;">
                                ${item.jumlah} Pcs
                            </span>
                        </td>
                        <td class="small text-muted">${escapeModalHtml(item.keterangan || '-')}</td>
                    </tr>
                `;
            });
        }

        document.getElementById('riwayat_total_disp').innerText = totalPcs + ' Pcs';
        document.getElementById('riwayat_tbody').innerHTML = tbodyHtml;

        const modalEl = document.getElementById('modalRiwayatApd');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function cetakRiwayatApdPrint() {
        if (!currentRiwayatData.nama) return;

        const now = new Date();
        const formattedToday = ('0' + now.getDate()).slice(-2) + '/' + ('0' + (now.getMonth() + 1)).slice(-2) + '/' + now.getFullYear();

        let tableRowsHtml = '';
        if (currentRiwayatData.items.length === 0) {
            tableRowsHtml = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Tidak ada riwayat pengambilan APD tercatat.</td></tr>';
        } else {
            currentRiwayatData.items.forEach((item, idx) => {
                let formattedDate = item.tanggal_pengambilan || '-';
                if (item.tanggal_pengambilan) {
                    const d = new Date(item.tanggal_pengambilan);
                    if (!isNaN(d.getTime())) {
                        formattedDate = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
                    }
                }
                tableRowsHtml += `
                    <tr>
                        <td style="text-align: center; font-weight: bold;">${idx + 1}</td>
                        <td style="text-align: center;">${formattedDate}</td>
                        <td style="font-weight: 600;">${item.jenis_apd}</td>
                        <td style="text-align: center;">${item.jumlah} Pcs</td>
                        <td>${item.keterangan || '-'}</td>
                        <td style="text-align: center; width: 90px; height: 32px;"></td>
                    </tr>
                `;
            });
        }

        const printContent = `
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>Kartu Riwayat Pengambilan APD - ${currentRiwayatData.nama}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        color: #111827;
                        margin: 20mm 15mm;
                        font-size: 12px;
                    }
                    .header-table {
                        width: 100%;
                        border-bottom: 2.5px solid #003366;
                        padding-bottom: 8px;
                        margin-bottom: 16px;
                    }
                    .company-title {
                        font-size: 16px;
                        font-weight: 900;
                        color: #003366;
                        margin: 0;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .doc-title {
                        font-size: 13px;
                        font-weight: 700;
                        color: #dc2626;
                        margin: 3px 0 0 0;
                        text-transform: uppercase;
                    }
                    .info-box {
                        width: 100%;
                        margin-bottom: 16px;
                        border-collapse: collapse;
                    }
                    .info-box td {
                        padding: 4px 6px;
                        font-size: 12px;
                    }
                    .info-label {
                        width: 160px;
                        font-weight: bold;
                        color: #4b5563;
                    }
                    .info-val {
                        font-weight: bold;
                        color: #001f3f;
                    }
                    table.data-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 24px;
                    }
                    table.data-table th {
                        background-color: #f1f5f9;
                        border: 1px solid #94a3b8;
                        padding: 7px 8px;
                        font-size: 11px;
                        text-align: left;
                        text-transform: uppercase;
                    }
                    table.data-table td {
                        border: 1px solid #cbd5e1;
                        padding: 6px 8px;
                        font-size: 11px;
                    }
                    .sig-section {
                        width: 100%;
                        margin-top: 30px;
                        page-break-inside: avoid;
                    }
                    .sig-col {
                        text-align: center;
                        vertical-align: top;
                        width: 33.33%;
                    }
                    .sig-line {
                        margin-top: 60px;
                        border-top: 1px solid #000;
                        width: 80%;
                        display: inline-block;
                        font-weight: bold;
                        padding-top: 4px;
                    }
                    @media print {
                        @page {
                            size: A4 portrait;
                            margin: 15mm;
                        }
                    }
                </style>
            </head>
            <body>
                <table class="header-table">
                    <tr>
                        <td style="width: 70%;">
                            <div class="company-title">PT MURINDA IRON STEEL</div>
                            <div style="font-size: 11px; color: #475569; font-weight: 600;">HEALTH, SAFETY & ENVIRONMENT (HSE) DEPARTMENT</div>
                            <div class="doc-title">KARTU RIWAYAT PENGAMBILAN ALAT PELINDUNG DIRI (APD)</div>
                        </td>
                        <td style="text-align: right; vertical-align: top; font-size: 10px; color: #64748b;">
                            <div>No. Dokumen: MIS-HSE-APD-REC</div>
                            <div>Tanggal Cetak: <strong>${formattedToday}</strong></div>
                        </td>
                    </tr>
                </table>

                <table class="info-box">
                    <tr>
                        <td class="info-label">Nama Pekerja / Personel</td>
                        <td style="width: 10px;">:</td>
                        <td class="info-val">${currentRiwayatData.nama}</td>
                        <td class="info-label">Departemen / Subcont</td>
                        <td style="width: 10px;">:</td>
                        <td class="info-val">${currentRiwayatData.dept}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Total Riwayat Pengambilan</td>
                        <td>:</td>
                        <td class="info-val">${currentRiwayatData.items.length} Kali Transaksi</td>
                        <td class="info-label">Status Distribusi</td>
                        <td>:</td>
                        <td class="info-val" style="color: #16a34a;">Terdata di HSE MIS</td>
                    </tr>
                </table>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">No</th>
                            <th style="width: 90px; text-align: center;">Tanggal</th>
                            <th>Jenis APD Diberikan</th>
                            <th style="width: 75px; text-align: center;">Jumlah</th>
                            <th>Keterangan / Alasan</th>
                            <th style="width: 95px; text-align: center;">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRowsHtml}
                    </tbody>
                </table>

                <table class="sig-section">
                    <tr>
                        <td class="sig-col">
                            <div>Diterima Oleh,</div>
                            <div style="font-size: 11px; color: #6b7280;">Pekerja / Personel</div>
                            <div class="sig-line">${currentRiwayatData.nama}</div>
                        </td>
                        <td class="sig-col">
                            <div>Diserahkan Oleh,</div>
                            <div style="font-size: 11px; color: #6b7280;">Petugas HSE / Logistik APD</div>
                            <div class="sig-line">Petugas Logistik HSE</div>
                        </td>
                        <td class="sig-col">
                            <div>Mengetahui,</div>
                            <div style="font-size: 11px; color: #6b7280;">HSE Coordinator / Site Manager</div>
                            <div class="sig-line">HSE Coordinator</div>
                        </td>
                    </tr>
                </table>

                <script>
                    window.onload = function() {
                        window.print();
                    };
                <\/script>
            </body>
            </html>
        `;

        const printWindow = window.open('', '_blank', 'width=850,height=700');
        if (printWindow) {
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
        } else {
            alert('Silakan izinkan pop-up browser untuk mencetak kartu riwayat APD.');
        }
    }

    function escapeModalHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Instant Live Filter on table-apd (Tanpa perlu enter / klik tombol)
    document.addEventListener('DOMContentLoaded', function() {
        const apdSearch = document.querySelector('form[action="{{ route('apd.index') }}"] input[name="search"]');
        const apdStart = document.querySelector('form[action="{{ route('apd.index') }}"] input[name="start_date"]');
        const apdEnd = document.querySelector('form[action="{{ route('apd.index') }}"] input[name="end_date"]');
        const apdDept = document.querySelector('form[action="{{ route('apd.index') }}"] select[name="departemen"]');

        function applyLiveApdFilter() {
            const q = (apdSearch?.value || '').toLowerCase().trim();
            const dept = (apdDept?.value || '').toLowerCase().trim();
            const startVal = apdStart?.value || '';
            const endVal = apdEnd?.value || '';

            let startTime = null;
            let endTime = null;
            if (startVal) {
                const parts = startVal.split(/[\/\-]/);
                if (parts.length === 3) startTime = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime();
            }
            if (endVal) {
                const parts = endVal.split(/[\/\-]/);
                if (parts.length === 3) endTime = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime() + 86400000;
            }

            const rows = document.querySelectorAll('#table-apd tbody tr');
            rows.forEach(row => {
                if (row.cells.length < 5) return;
                const rowText = row.innerText.toLowerCase();
                const matchQ = !q || rowText.includes(q);
                const matchDept = !dept || rowText.includes(dept);

                let matchDate = true;
                if (startTime || endTime) {
                    const dateCell = row.cells[1]?.innerText || '';
                    const m = dateCell.match(/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/);
                    if (m) {
                        const rowTime = new Date(`${m[3]}-${m[2]}-${m[1]}`).getTime();
                        if (startTime && rowTime < startTime) matchDate = false;
                        if (endTime && rowTime > endTime) matchDate = false;
                    }
                }

                row.style.display = (matchQ && matchDept && matchDate) ? '' : 'none';
            });
        }

        apdSearch?.addEventListener('input', applyLiveApdFilter);
        apdStart?.addEventListener('input', applyLiveApdFilter);
        apdStart?.addEventListener('change', applyLiveApdFilter);
        apdEnd?.addEventListener('input', applyLiveApdFilter);
        apdEnd?.addEventListener('change', applyLiveApdFilter);
        apdDept?.addEventListener('change', applyLiveApdFilter);
    });
</script>
@endpush
