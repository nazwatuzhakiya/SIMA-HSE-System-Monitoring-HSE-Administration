@extends('layouts.app')

@section('title', 'Monitoring Proyek (FA & ENT)')
@section('page_heading', 'Monitoring Pekerja Proyek (FA & ENT)')
@section('page_subheading', 'Pengawasan Keselamatan Kerja Tenaga Kerja Konstruksi & Lapangan Proyek')

@section('content')
<div class="container-fluid px-0">

    <!-- Official Header for Print -->
    <x-murinda-header
        title="MONITORING DATA PEKERJA PROYEK"
        subhead="DATABASE TENAGA KERJA PROYEK & KONSTRUKSI (FA/PROJECT & ENT)"
        docNo="DOC-HSE-MIS-PROYEK-2026"
    />

    <!-- ======================================================== -->
    <!-- 1. RINGKASAN DATA PEKERJA PROYEK PER PERIODE AKTIF        -->
    <!-- ======================================================== -->
    <!-- ======================================================== -->
    <!-- 1. HEADER AKSI & PERIODE AKTIF (BADGE DI KANAN / PINGGIR)  -->
    <!-- ======================================================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-murinda-primary btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProyek">
                <i class="fa-solid fa-plus me-1"></i> Tambah Pekerja Proyek
            </button>
            <button type="button" class="btn btn-excel btn-sm fw-bold px-3 shadow-sm" onclick="exportTableToExcel('tabel-proyek-pekerja', 'DATA_PEKERJA_PROYEK_{{ $activeCompany }}', 'DAFTAR TENAGA KERJA PROYEK {{ $activeCompany }}')">
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
    <!-- 1. RINGKASAN TERPISAH: ENT vs FA/PROJECT (CLEAN MINIMAL) -->
    <!-- ======================================================== -->
    <div class="row g-3 mb-3 no-print">
        <div class="col-md-6">
            <div class="card p-3 h-100 {{ $activeCompany === 'ENT' ? 'bg-white shadow-sm' : 'bg-light' }}" 
                 style="border-radius: 8px; border: 1px solid {{ $activeCompany === 'ENT' ? '#cbd5e1' : '#e2e8f0' }}; {{ $activeCompany === 'ENT' ? 'border-top: 3px solid #003366 !important;' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold" style="font-size: 13.5px; color: #003366;">
                                <i class="fa-solid fa-building me-1.5 text-secondary"></i> ENT (Enterprise)
                            </span>
                            @if($activeCompany === 'ENT')
                                <span class="badge px-2 py-0.5" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1; font-size: 10.5px;">
                                    <i class="fa-solid fa-check text-success me-1"></i> Sedang Dilihat
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h4 class="fw-bold m-0 text-dark" style="font-size: 20px;">{{ $entStats['total'] }}</h4>
                            <span class="text-muted" style="font-size: 12px;">Total Personel</span>
                            <span class="text-muted mx-1">&bull;</span>
                            <span class="text-success fw-semibold" style="font-size: 12px;">{{ $entStats['aktif'] }} Aktif</span>
                            <span class="text-muted mx-1">&bull;</span>
                            <span class="text-secondary" style="font-size: 12px;">{{ $entStats['non_aktif'] }} Non-Aktif (Off)</span>
                        </div>
                    </div>
                    <div>
                        @if($activeCompany === 'ENT')
                            <span class="badge px-3 py-2 fw-semibold" style="background: #edf2f7; color: #003366; font-size: 11.5px;">
                                Aktif
                            </span>
                        @else
                            <a href="{{ route('subcont.proyek', ['company' => 'ENT']) }}" class="btn btn-sm btn-murinda-outline px-3 fw-semibold" style="font-size: 12px;">
                                Pilih ENT <i class="fa-solid fa-chevron-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 h-100 {{ $activeCompany === 'FA/PROJECT' ? 'bg-white shadow-sm' : 'bg-light' }}" 
                 style="border-radius: 8px; border: 1px solid {{ $activeCompany === 'FA/PROJECT' ? '#cbd5e1' : '#e2e8f0' }}; {{ $activeCompany === 'FA/PROJECT' ? 'border-top: 3px solid #003366 !important;' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold" style="font-size: 13.5px; color: #003366;">
                                <i class="fa-solid fa-helmet-safety me-1.5 text-secondary"></i> FA / PROJECT
                            </span>
                            @if($activeCompany === 'FA/PROJECT')
                                <span class="badge px-2 py-0.5" style="background: #edf2f7; color: #003366; border: 1px solid #cbd5e1; font-size: 10.5px;">
                                    <i class="fa-solid fa-check text-success me-1"></i> Sedang Dilihat
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h4 class="fw-bold m-0 text-dark" style="font-size: 20px;">{{ $faStats['total'] }}</h4>
                            <span class="text-muted" style="font-size: 12px;">Total Personel</span>
                            <span class="text-muted mx-1">&bull;</span>
                            <span class="text-success fw-semibold" style="font-size: 12px;">{{ $faStats['aktif'] }} Aktif</span>
                            <span class="text-muted mx-1">&bull;</span>
                            <span class="text-secondary" style="font-size: 12px;">{{ $faStats['non_aktif'] }} Non-Aktif (Off)</span>
                        </div>
                    </div>
                    <div>
                        @if($activeCompany === 'FA/PROJECT')
                            <span class="badge px-3 py-2 fw-semibold" style="background: #edf2f7; color: #003366; font-size: 11.5px;">
                                Aktif
                            </span>
                        @else
                            <a href="{{ route('subcont.proyek', ['company' => 'FA/PROJECT']) }}" class="btn btn-sm btn-murinda-outline px-3 fw-semibold" style="font-size: 12px;">
                                Pilih FA <i class="fa-solid fa-chevron-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KARTU KPI PROYEK SPESIFIK ENTITAS TERPILIH (ENT / FA) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Pekerja {{ $activeCompany }} Aktif</div>
                        <h3 class="kpi-value">{{ $totalActiveWorkers }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Pekerja {{ $activeCompany }} Off</div>
                        <h3 class="kpi-value">{{ $totalNonActiveWorkers }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Induction {{ $activeCompany }}</div>
                        <h3 class="kpi-value">{{ $totalInductionPeriod }}</h3>
                    </div>
                    <div class="kpi-icon-box">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card-kpi-uniform">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-title">Keluar / Off {{ $activeCompany }}</div>
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
    <!-- 3. TABEL DATA PEKERJA PROYEK                             -->
    <!-- ======================================================== -->
    <div class="card-custom">
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0" style="color: #003366;">
                    <i class="fa-solid fa-helmet-safety me-1 text-primary"></i> Data Pekerja: {{ $activeCompany }}
                </h5>
                <small class="text-muted">Total: <strong>{{ count($personnels) }}</strong> personel terdaftar</small>
            </div>
            <form action="{{ route('subcont.proyek') }}" method="GET" class="d-flex gap-2">
                <input type="hidden" name="company" value="{{ $activeCompany }}">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama pekerja...">
                <button type="submit" class="btn btn-murinda-primary btn-sm"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover m-0 align-middle" id="tabel-proyek-pekerja">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">NO</th>
                            <th>NAMA PEKERJA</th>
                            <th>ENTITAS PROYEK</th>
                            <th>JABATAN</th>
                            <th class="text-center">TGL MASUK</th>
                            <th class="text-center">TGL INDUCTION</th>
                            <th class="text-center">STATUS</th>
                            <th>LOKASI PENEMPATAN PROYEK</th>
                            <th class="text-center no-print" style="width: 170px;">AKSI</th>
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
                                <td class="text-center">{{ $p->tgl_masuk ? date('d/m/Y', strtotime($p->tgl_masuk)) : '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ $p->tgl_induction ? date('d/m/Y', strtotime($p->tgl_induction)) : ($p->tgl_masuk ? date('d/m/Y', strtotime($p->tgl_masuk)) : '-') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($p->status === 'Aktif')
                                        <span class="badge-aman"><i class="fa-solid fa-check me-1"></i> Aktif</span>
                                    @else
                                        <span class="badge-bahaya"><i class="fa-solid fa-xmark me-1"></i> Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $p->keterangan ?: ($p->domisili_alamat ?: 'Proyek Lapangan') }}</span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-action-view" title="Lihat Data Lengkap" onclick="showDetailProyek({{ json_encode($p) }})">
                                            <i class="fa-solid fa-eye"></i> Lihat Data
                                        </button>
                                        <button type="button" class="btn btn-action-edit" title="Edit Data" onclick="editProyek({{ json_encode($p) }})">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Data
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada data pekerja proyek untuk <strong>{{ $activeCompany }}</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PEKERJA PROYEK -->
<div class="modal fade" id="modalTambahProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: #001f3f; color: #ffffff;">
                <h6 class="modal-title fw-bold">
                    <i class="fa-solid fa-trowel-bricks me-1 text-warning"></i> Tambah Data Pekerja Proyek
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('subcont.proyek.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap Pekerja: <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-custom" required placeholder="Nama pekerja proyek...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Entitas Proyek: <span class="text-danger">*</span></label>
                            <select name="company_code" class="form-select form-select-custom" required>
                                <option value="ENT" {{ $activeCompany === 'ENT' ? 'selected' : '' }}>ENT (Tersinkron ke Manpower ENT)</option>
                                <option value="FA/PROJECT" {{ $activeCompany === 'FA/PROJECT' ? 'selected' : '' }}>FA/PROJECT (Khusus Monitoring)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Posisi / Jabatan Lapangan: <span class="text-danger">*</span></label>
                            <input type="text" name="posisi" class="form-control form-control-custom" list="posisiProyekList" required placeholder="Contoh: Site Engineer, Foreman, Welder">
                            <datalist id="posisiProyekList">
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status: <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-custom" required>
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk Proyek: <span class="text-danger">*</span></label>
                            <input type="text" name="tgl_masuk" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" required placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Safety Induction:</label>
                            <input type="text" name="tgl_induction" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Lokasi Penempatan Proyek (misal: Proyek PT ..., Kota ...):</label>
                            <input type="text" name="keterangan" class="form-control form-control-custom" placeholder="Contoh: Proyek PT Indah Kiat Pulp & Paper, Serang">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Pekerja Proyek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DETAIL PEKERJA PROYEK                              -->
<!-- ======================================================== -->
<div class="modal fade" id="modalDetailProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: #001f3f; color: #ffffff;">
                <h6 class="modal-title fw-bold">
                    <i class="fa-solid fa-id-card me-1 text-primary"></i> Detail Personel Proyek: <span id="detail_proyek_nama"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered table-sm mb-0">
                    <tr>
                        <th style="width: 40%; background: #f8fafc;">No. ID / Registrasi</th>
                        <td id="detail_proyek_id" class="fw-bold text-navy"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Entitas Proyek</th>
                        <td id="detail_proyek_company"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Jabatan / Posisi</th>
                        <td id="detail_proyek_posisi" class="fw-semibold"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Status Pekerja</th>
                        <td><span id="detail_proyek_status_badge"></span></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Tanggal Masuk</th>
                        <td id="detail_proyek_tgl_masuk"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Tanggal Induction</th>
                        <td id="detail_proyek_tgl_induction"></td>
                    </tr>
                    <tr id="row_detail_proyek_induction_ulang" style="display: none;">
                        <th style="background: #f8fafc;">Induction Ulang</th>
                        <td id="detail_proyek_tgl_induction_ulang" class="text-primary fw-bold"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Lokasi Penempatan Proyek</th>
                        <td id="detail_proyek_lokasi" class="fw-semibold text-dark"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light p-2 text-end">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL EDIT PEKERJA PROYEK                                -->
<!-- ======================================================== -->
<div class="modal fade" id="modalEditProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-user-pen me-2 text-danger"></i> Edit Data Pekerja Proyek
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditProyek" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap: <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_proyek_nama" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Entitas Proyek: <span class="text-danger">*</span></label>
                            <select name="company_code" id="edit_proyek_company" class="form-select form-select-custom" required>
                                <option value="ENT">ENT (Enterprise)</option>
                                <option value="FA/PROJECT">FA/PROJECT</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Posisi / Jabatan: <span class="text-danger">*</span></label>
                            <input type="text" name="posisi" id="edit_proyek_posisi" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status Pekerja: <span class="text-danger">*</span></label>
                            <select name="status" id="edit_proyek_status" class="form-select form-select-custom" required onchange="handleStatusChangeProyek()">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk Proyek: <span class="text-danger">*</span></label>
                            <input type="text" name="tgl_masuk" id="edit_proyek_tgl_masuk" class="form-control form-control-custom flatpickr-date" required placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Induction Awal:</label>
                            <input type="text" name="tgl_induction" id="edit_proyek_tgl_induction" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-4" id="wrapper_proyek_tgl_keluar">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Keluar (Jika Non-Aktif):</label>
                            <input type="text" name="tgl_keluar" id="edit_proyek_tgl_keluar" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-12" id="wrapper_proyek_tgl_induction_ulang">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Tanggal Induction Ulang:
                                <span id="label_req_proyek_ind_ulang" class="text-danger fw-bold small" style="font-size: 11px; display: none;">* (Wajib diisi saat diaktifkan kembali)</span>
                                <span id="label_frozen_proyek_ind_ulang" class="badge bg-secondary ms-1 fw-normal" style="font-size: 10.5px;">Dibekukan</span>
                            </label>
                            <input type="text" name="tgl_induction_ulang" id="edit_proyek_tgl_induction_ulang" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                            <small id="hint_proyek_ind_ulang" class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="fa-solid fa-circle-info me-1 text-primary"></i> Otomatis terisi tanggal hari ini saat diaktifkan kembali, namun tetap dapat diubah bila perlu.
                            </small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Lokasi Penempatan Proyek (misal: Proyek PT ..., Kota ...):</label>
                            <input type="text" name="keterangan" id="edit_proyek_keterangan" class="form-control form-control-custom" placeholder="Contoh: Proyek PT Indah Kiat Pulp & Paper, Serang">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            locale: "id"
        });
    });

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }

    function showDetailProyek(p) {
        document.getElementById('detail_proyek_nama').innerText = p.nama || '-';
        document.getElementById('detail_proyek_id').innerText = 'EMP-' + p.company_code + '-' + String(p.id).padStart(3, '0');
        document.getElementById('detail_proyek_company').innerText = p.company_code === 'ENT' ? 'ENT (Enterprise)' : p.company_code;
        document.getElementById('detail_proyek_posisi').innerText = p.posisi || '-';

        const badge = document.getElementById('detail_proyek_status_badge');
        if (p.status === 'Aktif') {
            badge.className = 'badge bg-success';
            badge.innerText = 'Aktif';
        } else {
            badge.className = 'badge bg-danger';
            badge.innerText = 'Non-Aktif';
        }

        document.getElementById('detail_proyek_tgl_masuk').innerText = p.tgl_masuk ? formatDate(p.tgl_masuk) : '-';
        document.getElementById('detail_proyek_tgl_induction').innerText = p.tgl_induction ? formatDate(p.tgl_induction) : '-';

        const rowIndUlang = document.getElementById('row_detail_proyek_induction_ulang');
        if (p.tgl_induction_ulang) {
            rowIndUlang.style.display = 'table-row';
            document.getElementById('detail_proyek_tgl_induction_ulang').innerText = formatDate(p.tgl_induction_ulang);
        } else {
            rowIndUlang.style.display = 'none';
        }

        document.getElementById('detail_proyek_lokasi').innerText = p.keterangan || p.domisili_alamat || 'Proyek Lapangan';

        const modal = new bootstrap.Modal(document.getElementById('modalDetailProyek'));
        modal.show();
    }

    let currentEditingProyek = null;

    function editProyek(p) {
        currentEditingProyek = p;
        document.getElementById('formEditProyek').action = '/subcont/' + p.id;
        document.getElementById('edit_proyek_nama').value = p.nama || '';
        document.getElementById('edit_proyek_company').value = p.company_code || 'ENT';
        document.getElementById('edit_proyek_posisi').value = p.posisi || '';
        document.getElementById('edit_proyek_status').value = p.status || 'Aktif';
        document.getElementById('edit_proyek_keterangan').value = p.keterangan || p.domisili_alamat || '';

        if (p.tgl_masuk) {
            const fp = document.getElementById('edit_proyek_tgl_masuk')._flatpickr;
            if (fp) fp.setDate(p.tgl_masuk, true);
        }
        if (p.tgl_induction) {
            const fp = document.getElementById('edit_proyek_tgl_induction')._flatpickr;
            if (fp) fp.setDate(p.tgl_induction, true);
        }
        if (p.tgl_keluar) {
            const fp = document.getElementById('edit_proyek_tgl_keluar')._flatpickr;
            if (fp) fp.setDate(p.tgl_keluar, true);
        } else {
            const fp = document.getElementById('edit_proyek_tgl_keluar')._flatpickr;
            if (fp) fp.clear();
        }
        if (p.tgl_induction_ulang) {
            const fp = document.getElementById('edit_proyek_tgl_induction_ulang')._flatpickr;
            if (fp) fp.setDate(p.tgl_induction_ulang, true);
        } else {
            const fp = document.getElementById('edit_proyek_tgl_induction_ulang')._flatpickr;
            if (fp) fp.clear();
        }

        handleStatusChangeProyek();

        const modal = new bootstrap.Modal(document.getElementById('modalEditProyek'));
        modal.show();
    }

    function handleStatusChangeProyek() {
        const statusVal = document.getElementById('edit_proyek_status').value;
        const indUlangEl = document.getElementById('edit_proyek_tgl_induction_ulang');
        const labelReq = document.getElementById('label_req_proyek_ind_ulang');
        const labelFrozen = document.getElementById('label_frozen_proyek_ind_ulang');
        const keluarEl = document.getElementById('edit_proyek_tgl_keluar');
        const isOriginallyNonAktif = currentEditingProyek && currentEditingProyek.status === 'Non-Aktif';

        if (statusVal === 'Non-Aktif') {
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
            indUlangEl.disabled = false;
            indUlangEl.classList.remove('bg-light');
            if (indUlangEl._flatpickr && indUlangEl._flatpickr._input) {
                indUlangEl._flatpickr._input.disabled = false;
                indUlangEl._flatpickr._input.classList.remove('bg-light');
            }
            if (labelFrozen) labelFrozen.style.display = 'none';

            if (isOriginallyNonAktif) {
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

    // Instant Live Filter: Otomatis memfilter saat diketik tanpa perlu enter / klik tombol
    document.addEventListener('DOMContentLoaded', function() {
        const formEdit = document.getElementById('formEditProyek');
        if (formEdit) {
            formEdit.addEventListener('submit', function(e) {
                const statusVal = document.getElementById('edit_proyek_status').value;
                const indUlangVal = document.getElementById('edit_proyek_tgl_induction_ulang').value.trim();
                const isOriginallyNonAktif = currentEditingProyek && currentEditingProyek.status === 'Non-Aktif';

                if (statusVal === 'Aktif' && isOriginallyNonAktif && !indUlangVal) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Tanggal Induction Ulang wajib diisi saat mengaktifkan kembali pekerja!');
                    document.getElementById('edit_proyek_tgl_induction_ulang').focus();
                    return false;
                }
            });
        }

        const searchInput = document.querySelector('input[name="search"]');
        searchInput?.addEventListener('input', function() {
            const q = (this.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#tabel-proyek-pekerja tbody tr');
            rows.forEach(row => {
                if (row.cells.length < 5) return;
                row.style.display = (!q || row.innerText.toLowerCase().includes(q)) ? '' : 'none';
            });
        });
    });
</script>
@endpush
@endsection
