@extends('layouts.app')

@section('title', 'Monitoring Departemen')
@section('page_heading', 'Monitoring Tenaga Kerja Departemen')
@section('page_subheading', 'Monitoring Data Tenaga Kerja Departemen PT Murinda Iron Steel')

@section('content')
<div class="container-fluid px-0">

    <!-- Official Header for Print -->
    <x-murinda-header
        title="MONITORING DEPARTEMEN PABRIK"
        subhead="DATABASE TENAGA KERJA INTERNAL PABRIK PT MURINDA IRON STEEL"
        docNo="DOC-HSE-MIS-DEPT-2026"
    />

    <!-- ======================================================== -->
    <!-- 1. HEADER AKSI & PERIODE AKTIF (BADGE DI KANAN / PINGGIR)  -->
    <!-- ======================================================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-murinda-primary btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPekerja">
                <i class="fa-solid fa-plus me-1"></i> Tambah Pekerja
            </button>
            <button type="button" class="btn btn-excel btn-sm fw-bold px-3 shadow-sm" onclick="exportTableToExcel('tabel-subcont-pekerja', 'DATA_DEPARTEMEN_{{ $activeCompany }}', 'DAFTAR TENAGA KERJA DEPARTEMEN {{ $activeCompany }}')">
                <i class="fa-solid fa-file-excel me-1"></i> Ekspor Excel
            </button>
            <button type="button" class="btn btn-pdf btn-sm fw-bold px-3 shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF
            </button>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
            <span class="badge badge-blured-gray px-3 py-2 shadow-sm" style="font-size: 12px; border-radius: 6px;">
                <i class="fa-regular fa-clock me-1 text-primary"></i> Periode (26 s/d 25): {{ $period['label'] }}
            </span>
            <span class="badge badge-blured-gray px-3 py-2 shadow-sm" style="font-size: 12px; border-radius: 6px;">
                <i class="fa-regular fa-calendar-check me-1 text-primary"></i> Hari Ini: {{ date('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 2. KARTU KPI RESMI (FORMAT SERAGAM NAVY & PUTIH)          -->
    <!-- ======================================================== -->
    <div class="row g-3 mb-4 no-print">
        <!-- Pekerja Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Pekerja Aktif</div>
                        <h3 class="kpi-value">{{ $totalActiveWorkers }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pekerja Non-Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Pekerja Non-Aktif</div>
                        <h3 class="kpi-value">{{ $totalNonActiveWorkers }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Induction Baru -->
        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Induction Periode Ini</div>
                        <h3 class="kpi-value">{{ $totalInductionPeriod }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pekerja Keluar Periode Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Keluar Periode Ini</div>
                        <h3 class="kpi-value">{{ $totalExitPeriod }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 3. PILIH DEPARTEMEN RESMI (HORIZONTAL SCROLL)            -->
    <!-- ======================================================== -->
    <div class="card-custom mb-4 no-print">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <span class="card-title-custom" style="color: #003366;">
                <i class="fa-solid fa-building me-1"></i> Pilih Departemen Internal Pabrik
            </span>
            <small class="text-muted">Pilih departemen untuk melihat daftar pekerja</small>
        </div>
        <div class="card-body p-3">
            <div class="horizontal-subcont-scroll">
                @foreach($companies as $comp)
                    @php
                        $isActive = ($comp->code === $activeCompany);
                        $count = $companyCounts[$comp->code] ?? 0;
                    @endphp
                    <div class="subcont-pill-card {{ $isActive ? 'active-pill' : '' }}">
                        <a href="{{ route('subcont.departemen', ['company' => $comp->code]) }}" class="text-decoration-none d-block">
                            <span class="badge {{ $isActive ? 'bg-white text-dark' : 'bg-light text-dark border' }} px-2 py-1 mb-1 font-monospace" style="font-size: 11px;">
                                {{ $comp->code }}
                            </span>
                            <div class="fw-bold fs-5 {{ $isActive ? 'text-white' : 'text-dark' }}">{{ $count }}</div>
                            <small class="{{ $isActive ? 'text-white-50' : 'text-muted' }}" style="font-size: 11px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $comp->name }}">
                                {{ $comp->name }}
                            </small>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 4. FILTER PENCARIAN                                      -->
    <!-- ======================================================== -->
    <div class="card-custom mb-3 no-print">
        <div class="card-body p-3">
            <form action="{{ route('subcont.departemen') }}" method="GET" class="row g-2 align-items-end" id="form-subcont-filter">
                <input type="hidden" name="company" value="{{ $activeCompany }}">

                <div class="col-lg-4 col-md-4">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Cari Nama Personel / Jabatan:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ketik nama atau jabatan pekerja...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Status Pekerja:</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <div class="col-lg-4 col-md-5">
                    <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 12px;">Rentang Tanggal Masuk (DD/MM/YYYY):</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="start_date" value="{{ request('start_date') }}" class="form-control flatpickr-date" placeholder="DD/MM/YYYY">
                        <span class="input-group-text bg-light">s/d</span>
                        <input type="text" name="end_date" value="{{ request('end_date') }}" class="form-control flatpickr-date" placeholder="DD/MM/YYYY">
                    </div>
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
                        <a href="{{ route('subcont.departemen', ['company' => $activeCompany]) }}" class="btn btn-light border btn-sm" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 5. TABEL DATA PEKERJA DEPARTEMEN                         -->
    <!-- ======================================================== -->
    <div class="card-custom">
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0" style="color: #001f3f;">
                    <i class="fa-solid fa-users me-1 text-primary"></i> Data Pekerja: {{ $activeCompany }}
                </h5>
                <small class="text-muted">Total: <strong>{{ count($personnels) }}</strong> personel terdaftar pada departemen ini</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditCompany">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Nama Bagian
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover m-0 align-middle" id="tabel-subcont-pekerja">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">NO</th>
                            <th>NAMA PEKERJA</th>
                            <th>DEPARTEMEN</th>
                            <th>JABATAN</th>
                            <th class="text-center">TGL INDUCTION</th>
                            <th>ALAMAT LENGKAP (DOMISILI)</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center no-print" style="width: 140px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnels as $index => $p)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->nama }}</div>
                                    <small class="text-muted font-monospace" style="font-size: 11px;">EMP-{{ $p->company_code }}-{{ str_pad($p->id, 3, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary px-2 py-1 font-monospace" style="font-size: 11px;">
                                        {{ $p->company_code }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">{{ $p->posisi ?: '-' }}</td>
                                <td class="text-center">
                                    <span class="fw-semibold text-dark">{{ $p->tgl_induction ? date('d/m/Y', strtotime($p->tgl_induction)) : ($p->tgl_masuk ? date('d/m/Y', strtotime($p->tgl_masuk)) : '-') }}</span>
                                    @if($p->tgl_induction_ulang)
                                        <div class="text-muted small" style="font-size: 10px;">Re-ind: {{ date('d/m/Y', strtotime($p->tgl_induction_ulang)) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 11.5px;">{{ $p->domisili_alamat ?: '-' }}</div>
                                    @php
                                        $addr = array_filter([
                                            $p->domisili_desa ? 'Ds. ' . $p->domisili_desa : null,
                                            $p->domisili_kec ? 'Kec. ' . $p->domisili_kec : null,
                                            $p->domisili_kota ? $p->domisili_kota : null,
                                        ]);
                                    @endphp
                                    @if(!empty($addr))
                                        <small class="text-muted" style="font-size: 10.5px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ implode(', ', $addr) }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->status === 'Aktif')
                                        <span class="badge-aman"><i class="fa-solid fa-check me-1"></i> Aktif</span>
                                    @else
                                        <span class="badge-bahaya"><i class="fa-solid fa-xmark me-1"></i> Non-Aktif</span>
                                        @if($p->tgl_keluar)
                                            <div class="text-muted" style="font-size: 11px;">Keluar: {{ date('d/m/Y', strtotime($p->tgl_keluar)) }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-action-view" title="Lihat Data Lengkap" onclick="showDetailPersonel({{ json_encode($p) }})">
                                            <i class="fa-solid fa-eye"></i> Lihat Data
                                        </button>
                                        <button type="button" class="btn btn-action-edit" title="Edit Data" onclick="editPersonel({{ json_encode($p) }})">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Data
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 opacity-50 d-block"></i>
                                    Belum ada data pekerja untuk departemen <strong>{{ $activeCompany }}</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL TAMBAH PEKERJA DENGAN MULTI-STEP WIZARD FORM       -->
<!-- ======================================================== -->
<div class="modal fade" id="modalTambahPekerja" tabindex="-1" aria-labelledby="modalTambahPekerjaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <h6 class="modal-title fw-bold" id="modalTambahPekerjaLabel">
                    <i class="fa-solid fa-user-plus me-1 text-warning"></i> Tambah Data Pekerja Departemen
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('subcont.store') }}" method="POST" id="formTambahPekerja">
                @csrf
                <div class="modal-body p-4">

                    <!-- Multi-Step Indicators -->
                    <div class="step-wizard mb-4">
                        <div class="step-item active" data-step="1">
                            <span class="step-circle">1</span>
                            <span class="step-label">Data Identitas</span>
                        </div>
                        <div class="step-item" data-step="2">
                            <span class="step-circle">2</span>
                            <span class="step-label">Penempatan Kerja</span>
                        </div>
                        <div class="step-item" data-step="3">
                            <span class="step-circle">3</span>
                            <span class="step-label">Induction & TTD</span>
                        </div>
                    </div>

                    <!-- STEP 1: IDENTITAS PEKERJA -->
                    <div class="form-step active" id="wizard-step-1">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap Pekerja: <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="step_nama" class="form-control form-control-custom" required placeholder="Contoh: Budi Santoso">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Lahir (DD/MM/YYYY):</label>
                                <input type="text" name="tgl_lahir" class="form-control form-control-custom flatpickr-birth" placeholder="DD/MM/YYYY">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Alamat Domisili (Jalan / RT RW / Dusun):</label>
                                <input type="text" name="domisili_alamat" class="form-control form-control-custom" placeholder="Nama Jalan / RT RW / Dusun">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Desa / Kelurahan:</label>
                                <input type="text" name="domisili_desa" class="form-control form-control-custom" placeholder="Contoh: Kalijati Timur">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kecamatan:</label>
                                <input type="text" name="domisili_kec" class="form-control form-control-custom" placeholder="Contoh: Kalijati">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kabupaten / Kota Asal:</label>
                                <input type="text" name="domisili_kota" class="form-control form-control-custom" value="Subang">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: PENEMPATAN & TANGGAL -->
                    <div class="form-step" id="wizard-step-2">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Departemen Internal: <span class="text-danger">*</span></label>
                                <select name="company_code" id="step_company_code" class="form-select form-select-custom" required>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->code }}" {{ $activeCompany == $comp->code ? 'selected' : '' }}>{{ $comp->code }} ({{ $comp->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Jabatan / Posisi Pekerjaan: <span class="text-danger">*</span></label>
                                <input type="text" name="posisi" id="step_posisi" class="form-control form-control-custom" list="posisiDeptList" required placeholder="Pilih atau ketik jabatan...">
                                <datalist id="posisiDeptList">
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk Kerja: <span class="text-danger">*</span></label>
                                <input type="text" name="tgl_masuk" id="step_tgl_masuk" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" required placeholder="DD/MM/YYYY">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status Pekerja: <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-custom">
                                    <option value="Aktif" selected>Aktif (Bekerja)</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: INDUCTION K3 -->
                    <div class="form-step" id="wizard-step-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Safety Induction:</label>
                                <input type="text" name="tgl_induction" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" placeholder="DD/MM/YYYY">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Catatan Induction / Keterangan:</label>
                                <input type="text" name="catatan_induction" class="form-control form-control-custom" placeholder="Contoh: Lulus evaluasi K3, APD lengkap">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm px-3" id="btnPrevStep" style="display: none;">
                            <i class="fa-solid fa-arrow-left me-1"></i> Sebelumnya
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" id="btnNextStep">
                            Selanjutnya <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="btnSubmitWizard" style="display: none;">
                            <i class="fa-solid fa-save me-1"></i> Simpan Data Pekerja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL LIHAT DETAIL PEKERJA (UNTUK TAMPILAN RAPI)          -->
<!-- ======================================================== -->
<div class="modal fade" id="modalDetailPersonel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <h6 class="modal-title fw-bold">
                    <i class="fa-solid fa-id-card me-1 text-warning"></i> Detail Data Pekerja
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar-circle mx-auto mb-2 text-white fw-bold d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 50%; background: #003366; font-size: 20px;" id="detail_avatar">
                        P
                    </div>
                    <h5 class="fw-bold mb-0 text-dark" id="detail_nama">-</h5>
                    <span class="badge bg-secondary font-monospace mt-1" id="detail_code">-</span>
                    <span class="badge ms-1" id="detail_status_badge">-</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 140px;">Departemen</td>
                                <td class="fw-semibold text-dark" id="detail_company">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan / Posisi</td>
                                <td class="fw-semibold text-dark" id="detail_posisi">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Lahir</td>
                                <td class="text-dark" id="detail_tgl_lahir">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat Domisili</td>
                                <td class="text-dark" id="detail_alamat">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kota / Kab Asal</td>
                                <td class="text-dark" id="detail_kota">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Masuk</td>
                                <td class="text-dark" id="detail_tgl_masuk">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Induction</td>
                                <td class="text-dark" id="detail_tgl_induction">-</td>
                            </tr>
                            <tr id="row_detail_tgl_keluar" style="display: none;">
                                <td class="text-danger fw-semibold">Tanggal Keluar</td>
                                <td class="text-danger fw-semibold" id="detail_tgl_keluar">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Catatan Induction</td>
                                <td class="text-dark" id="detail_keterangan">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL EDIT PEKERJA                                       -->
<!-- ======================================================== -->
<div class="modal fade" id="modalEditPersonel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-user-pen me-2 text-danger"></i> Edit Data Pekerja Departemen
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPersonel" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap:</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Lahir:</label>
                            <input type="text" name="tgl_lahir" id="edit_tgl_lahir" class="form-control form-control-custom flatpickr-birth" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Departemen:</label>
                            <select name="company_code" id="edit_company_code" class="form-select form-select-custom" required>
                                @foreach($companies as $comp)
                                    <option value="{{ $comp->code }}">{{ $comp->code }} ({{ $comp->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Jabatan / Posisi:</label>
                            <input type="text" name="posisi" id="edit_posisi" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk:</label>
                            <input type="text" name="tgl_masuk" id="edit_tgl_masuk" class="form-control form-control-custom flatpickr-date" required placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Induction Awal:</label>
                            <input type="text" name="tgl_induction" id="edit_tgl_induction" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status Pekerja:</label>
                            <select name="status" id="edit_status" class="form-select form-select-custom" required onchange="handleStatusChangeDepartemen()">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="wrapper_edit_tgl_keluar">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Keluar (Jika Non-Aktif):</label>
                            <input type="text" name="tgl_keluar" id="edit_tgl_keluar" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-8" id="wrapper_edit_tgl_induction_ulang">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Tanggal Induction Ulang:
                                <span id="label_req_ind_ulang" class="text-danger fw-bold small" style="font-size: 11px; display: none;">* (Wajib diisi saat diaktifkan kembali)</span>
                                <span id="label_frozen_ind_ulang" class="badge bg-secondary ms-1 fw-normal" style="font-size: 10.5px;">Dibekukan</span>
                            </label>
                            <input type="text" name="tgl_induction_ulang" id="edit_tgl_induction_ulang" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                            <small id="hint_ind_ulang" class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="fa-solid fa-circle-info me-1 text-primary"></i> Otomatis terisi tanggal hari ini saat diaktifkan kembali, namun tetap dapat diubah bila perlu.
                            </small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Alamat Domisili (Jalan / RT RW / Dusun):</label>
                            <input type="text" name="domisili_alamat" id="edit_domisili_alamat" class="form-control form-control-custom" placeholder="Nama Jalan / RT RW / Dusun">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Desa / Kelurahan:</label>
                            <input type="text" name="domisili_desa" id="edit_domisili_desa" class="form-control form-control-custom" placeholder="Desa / Kelurahan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kecamatan:</label>
                            <input type="text" name="domisili_kec" id="edit_domisili_kec" class="form-control form-control-custom" placeholder="Kecamatan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kabupaten / Kota:</label>
                            <input type="text" name="domisili_kota" id="edit_domisili_kota" class="form-control form-control-custom" placeholder="Kabupaten / Kota">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Catatan / Keterangan:</label>
                            <input type="text" name="catatan_induction" id="edit_keterangan" class="form-control form-control-custom" placeholder="Catatan tambahan...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL EDIT NAMA ENTITAS                                  -->
<!-- ======================================================== -->
<div class="modal fade" id="modalEditCompany" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <h6 class="modal-title fw-bold">Edit Nama Departemen: {{ $activeCompany }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('subcont.company.update') }}" method="POST">
                @csrf
                <input type="hidden" name="company_code" value="{{ $activeCompany }}">
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Resmi / Deskripsi Bagian:</label>
                    <input type="text" name="new_name" class="form-control form-control-custom" value="{{ $companies->where('code', $activeCompany)->first()->name ?? $activeCompany }}" required>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Nama</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Flatpickr
        flatpickr(".flatpickr-birth", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            locale: "id",
            maxDate: "today"
        });

        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            locale: "id"
        });

        // ----------------------------------------------------
        // Multi-Step Form Wizard Logic
        // ----------------------------------------------------
        let currentStep = 1;
        const totalSteps = 3;
        const btnPrev = document.getElementById('btnPrevStep');
        const btnNext = document.getElementById('btnNextStep');
        const btnSubmit = document.getElementById('btnSubmitWizard');

        function showStep(step) {
            currentStep = step;
            document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => {
                const itemStep = parseInt(el.getAttribute('data-step'));
                el.classList.remove('active', 'completed');
                if (itemStep === step) {
                    el.classList.add('active');
                } else if (itemStep < step) {
                    el.classList.add('completed');
                }
            });

            const currentStepEl = document.getElementById('wizard-step-' + step);
            if (currentStepEl) currentStepEl.classList.add('active');

            if (step === 1) {
                btnPrev.style.display = 'none';
                btnNext.style.display = 'inline-block';
                btnSubmit.style.display = 'none';
            } else if (step === totalSteps) {
                btnPrev.style.display = 'inline-block';
                btnNext.style.display = 'none';
                btnSubmit.style.display = 'inline-block';
                resizeCanvas();
            } else {
                btnPrev.style.display = 'inline-block';
                btnNext.style.display = 'inline-block';
                btnSubmit.style.display = 'none';
            }
        }

        if (btnNext) {
            btnNext.addEventListener('click', function() {
                // Simple validation for required fields in current step
                if (currentStep === 1) {
                    const nama = document.getElementById('step_nama');
                    if (!nama.value.trim()) {
                        nama.focus();
                        nama.classList.add('is-invalid');
                        return;
                    } else {
                        nama.classList.remove('is-invalid');
                    }
                } else if (currentStep === 2) {
                    const posisi = document.getElementById('step_posisi');
                    const tglMasuk = document.getElementById('step_tgl_masuk');
                    if (!posisi.value.trim()) {
                        posisi.focus();
                        posisi.classList.add('is-invalid');
                        return;
                    } else {
                        posisi.classList.remove('is-invalid');
                    }
                    if (!tglMasuk.value.trim()) {
                        tglMasuk.focus();
                        tglMasuk.classList.add('is-invalid');
                        return;
                    } else {
                        tglMasuk.classList.remove('is-invalid');
                    }
                }

                if (currentStep < totalSteps) {
                    showStep(currentStep + 1);
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });
        }

        // Reset wizard upon modal open
        const modalTambah = document.getElementById('modalTambahPekerja');
        if (modalTambah) {
            modalTambah.addEventListener('show.bs.modal', function() {
                showStep(1);
            });
        }

        // ----------------------------------------------------
        // Digital Signature Canvas Handling
    });

    // ----------------------------------------------------
    // Lihat Detail Pekerja Modal
    // ----------------------------------------------------
    function showDetailPersonel(p) {
        document.getElementById('detail_avatar').innerText = (p.nama ? p.nama.charAt(0).toUpperCase() : 'P');
        document.getElementById('detail_nama').innerText = p.nama || '-';
        document.getElementById('detail_code').innerText = 'EMP-' + p.company_code + '-' + String(p.id).padStart(3, '0');

        const statusBadge = document.getElementById('detail_status_badge');
        if (p.status === 'Aktif') {
            statusBadge.className = 'badge bg-success ms-1';
            statusBadge.innerText = 'Aktif';
        } else {
            statusBadge.className = 'badge bg-danger ms-1';
            statusBadge.innerText = 'Non-Aktif';
        }

        document.getElementById('detail_company').innerText = p.company_code || '-';
        document.getElementById('detail_posisi').innerText = p.posisi || '-';
        document.getElementById('detail_tgl_lahir').innerText = p.tgl_lahir ? formatDate(p.tgl_lahir) : '-';
        const fullAddress = [
            p.domisili_alamat,
            p.domisili_desa ? 'Ds. ' + p.domisili_desa : null,
            p.domisili_kec ? 'Kec. ' + p.domisili_kec : null,
            p.domisili_kota
        ].filter(Boolean).join(', ');
        document.getElementById('detail_alamat').innerText = fullAddress || '-';
        document.getElementById('detail_kota').innerText = p.domisili_kota || '-';
        document.getElementById('detail_tgl_masuk').innerText = p.tgl_masuk ? formatDate(p.tgl_masuk) : '-';
        document.getElementById('detail_tgl_induction').innerText = p.tgl_induction ? formatDate(p.tgl_induction) : '-';
        document.getElementById('detail_keterangan').innerText = p.catatan_induction || p.keterangan || '-';

        const rowKeluar = document.getElementById('row_detail_tgl_keluar');
        if (p.status === 'Non-Aktif' && p.tgl_keluar) {
            rowKeluar.style.display = 'table-row';
            document.getElementById('detail_tgl_keluar').innerText = formatDate(p.tgl_keluar);
        } else {
            rowKeluar.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalDetailPersonel'));
        modal.show();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }

    // ----------------------------------------------------
    // Edit Personel Modal Logic & State Validation
    // ----------------------------------------------------
    let currentEditingWorker = null;

    function editPersonel(p) {
        currentEditingWorker = p;
        document.getElementById('formEditPersonel').action = '/subcont/' + p.id;
        document.getElementById('edit_nama').value = p.nama || '';
        document.getElementById('edit_company_code').value = p.company_code || '';
        document.getElementById('edit_posisi').value = p.posisi || '';
        document.getElementById('edit_status').value = p.status || 'Aktif';
        document.getElementById('edit_domisili_alamat').value = p.domisili_alamat || '';
        document.getElementById('edit_domisili_desa').value = p.domisili_desa || '';
        document.getElementById('edit_domisili_kec').value = p.domisili_kec || '';
        document.getElementById('edit_domisili_kota').value = p.domisili_kota || '';
        document.getElementById('edit_keterangan').value = p.catatan_induction || p.keterangan || '';

        if (p.tgl_lahir) {
            const fp = document.getElementById('edit_tgl_lahir')._flatpickr;
            if (fp) fp.setDate(p.tgl_lahir, true);
        }
        if (p.tgl_masuk) {
            const fp = document.getElementById('edit_tgl_masuk')._flatpickr;
            if (fp) fp.setDate(p.tgl_masuk, true);
        }
        if (p.tgl_induction) {
            const fp = document.getElementById('edit_tgl_induction')._flatpickr;
            if (fp) fp.setDate(p.tgl_induction, true);
        }
        if (p.tgl_keluar) {
            const fp = document.getElementById('edit_tgl_keluar')._flatpickr;
            if (fp) fp.setDate(p.tgl_keluar, true);
        } else {
            const fp = document.getElementById('edit_tgl_keluar')._flatpickr;
            if (fp) fp.clear();
        }
        if (p.tgl_induction_ulang) {
            const fp = document.getElementById('edit_tgl_induction_ulang')._flatpickr;
            if (fp) fp.setDate(p.tgl_induction_ulang, true);
        } else {
            const fp = document.getElementById('edit_tgl_induction_ulang')._flatpickr;
            if (fp) fp.clear();
        }

        handleStatusChangeDepartemen();

        const modal = new bootstrap.Modal(document.getElementById('modalEditPersonel'));
        modal.show();
    }

    function handleStatusChangeDepartemen() {
        const statusVal = document.getElementById('edit_status').value;
        const indUlangEl = document.getElementById('edit_tgl_induction_ulang');
        const labelReq = document.getElementById('label_req_ind_ulang');
        const labelFrozen = document.getElementById('label_frozen_ind_ulang');
        const keluarEl = document.getElementById('edit_tgl_keluar');
        const isOriginallyNonAktif = currentEditingWorker && currentEditingWorker.status === 'Non-Aktif';

        if (statusVal === 'Non-Aktif') {
            // Pekerja Non-Aktif: Bekukan tgl induction ulang
            indUlangEl.disabled = true;
            indUlangEl.required = false;
            indUlangEl.classList.add('bg-light');
            if (indUlangEl._flatpickr && indUlangEl._flatpickr._input) {
                indUlangEl._flatpickr._input.disabled = true;
                indUlangEl._flatpickr._input.classList.add('bg-light');
            }
            if (labelFrozen) labelFrozen.style.display = 'inline-block';
            if (labelReq) labelReq.style.display = 'none';

            if (keluarEl) {
                keluarEl.disabled = false;
                if (keluarEl._flatpickr && keluarEl._flatpickr._input) {
                    keluarEl._flatpickr._input.disabled = false;
                }
                if (!keluarEl.value) {
                    const todayStr = new Date().toISOString().split('T')[0];
                    if (keluarEl._flatpickr) keluarEl._flatpickr.setDate(todayStr, true);
                    else keluarEl.value = todayStr;
                }
            }
        } else {
            // Status Aktif: Buka kembali tgl induction ulang
            indUlangEl.disabled = false;
            indUlangEl.classList.remove('bg-light');
            if (indUlangEl._flatpickr && indUlangEl._flatpickr._input) {
                indUlangEl._flatpickr._input.disabled = false;
                indUlangEl._flatpickr._input.classList.remove('bg-light');
            }
            if (labelFrozen) labelFrozen.style.display = 'none';

            if (isOriginallyNonAktif) {
                // Diaktifkan kembali dari Non-Aktif: Wajib diisi dan otomatis terisi tanggal hari ini jika belum ada
                indUlangEl.required = true;
                if (labelReq) labelReq.style.display = 'inline-block';
                if (!indUlangEl.value) {
                    const todayStr = new Date().toISOString().split('T')[0];
                    if (indUlangEl._flatpickr) indUlangEl._flatpickr.setDate(todayStr, true);
                    else indUlangEl.value = todayStr;
                }
            } else {
                indUlangEl.required = false;
                if (labelReq) labelReq.style.display = 'none';
            }

            if (keluarEl) {
                if (keluarEl._flatpickr) keluarEl._flatpickr.clear();
                else keluarEl.value = '';
                keluarEl.disabled = true;
                if (keluarEl._flatpickr && keluarEl._flatpickr._input) {
                    keluarEl._flatpickr._input.disabled = true;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const formEdit = document.getElementById('formEditPersonel');
        if (formEdit) {
            formEdit.addEventListener('submit', function(e) {
                const statusVal = document.getElementById('edit_status').value;
                const indUlangVal = document.getElementById('edit_tgl_induction_ulang').value.trim();
                const isOriginallyNonAktif = currentEditingWorker && currentEditingWorker.status === 'Non-Aktif';

                if (statusVal === 'Aktif' && isOriginallyNonAktif && !indUlangVal) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Tanggal Induction Ulang wajib diisi saat mengaktifkan kembali pekerja!');
                    document.getElementById('edit_tgl_induction_ulang').focus();
                    return false;
                }
            });
        }
    });

    // Instant Live Filter: Otomatis memfilter saat diketik tanpa perlu tekan enter / klik tombol
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        function applyLiveDeptFilter() {
            const query = (searchInput?.value || '').toLowerCase().trim();
            const statusVal = (statusSelect?.value || '').trim();
            const startDateVal = startDateInput?.value || '';
            const endDateVal = endDateInput?.value || '';

            let startTime = null;
            let endTime = null;
            if (startDateVal) {
                const parts = startDateVal.split(/[\/\-]/);
                if (parts.length === 3) startTime = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime();
            }
            if (endDateVal) {
                const parts = endDateVal.split(/[\/\-]/);
                if (parts.length === 3) endTime = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime() + 86400000;
            }

            const rows = document.querySelectorAll('#tabel-subcont-pekerja tbody tr');
            rows.forEach(row => {
                if (row.cells.length < 5) return;
                const text = row.innerText.toLowerCase();
                const matchQuery = !query || text.includes(query);

                let matchStatus = true;
                if (statusVal) {
                    matchStatus = row.innerText.includes(statusVal);
                }

                let matchDate = true;
                if (startTime || endTime) {
                    const dateCell = row.cells[4]?.innerText || '';
                    const m = dateCell.match(/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/);
                    if (m) {
                        const rowTime = new Date(`${m[3]}-${m[2]}-${m[1]}`).getTime();
                        if (startTime && rowTime < startTime) matchDate = false;
                        if (endTime && rowTime > endTime) matchDate = false;
                    }
                }

                row.style.display = (matchQuery && matchStatus && matchDate) ? '' : 'none';
            });
        }

        searchInput?.addEventListener('input', applyLiveDeptFilter);
        statusSelect?.addEventListener('change', applyLiveDeptFilter);
        startDateInput?.addEventListener('input', applyLiveDeptFilter);
        startDateInput?.addEventListener('change', applyLiveDeptFilter);
        endDateInput?.addEventListener('input', applyLiveDeptFilter);
        endDateInput?.addEventListener('change', applyLiveDeptFilter);
    });
</script>
@endpush
@endsection
