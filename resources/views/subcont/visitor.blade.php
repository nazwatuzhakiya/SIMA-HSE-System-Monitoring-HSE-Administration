@extends('layouts.app')

@section('title', 'Monitoring Tamu & Khusus (PKL / Visitor / Klien)')
@section('page_heading', 'Monitoring Tamu & Khusus (PKL, Visitor, Klien)')
@section('page_subheading', 'Pencatatan Tamu, Mahasiswa/Siswa PKL, dan Perwakilan Klien PT Murinda Iron Steel')

@section('content')
<div class="container-fluid px-0">

    <!-- Official Header for Print -->
    <x-murinda-header
        title="MONITORING TAMU & KHUSUS"
        subhead="DATABASE PKL, VISITOR, DAN PERWAKILAN KLIEN"
        docNo="DOC-HSE-MIS-VISITOR-2026"
    />

    <!-- ======================================================== -->
    <!-- 1. HEADER AKSI & PERIODE AKTIF (BADGE DI KANAN / PINGGIR)  -->
    <!-- ======================================================== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-murinda-primary btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahVisitor">
                <i class="fa-solid fa-plus me-1"></i> Catat Tamu / Khusus Baru
            </button>
            <button type="button" class="btn btn-excel btn-sm fw-bold px-3 shadow-sm" onclick="exportTableToExcel('tabel-visitor-khusus', 'MONITORING_TAMU_{{ $activeCompany }}', 'DAFTAR {{ $activeCompany }}')">
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

    <!-- 3 KARTU RINGKASAN TAMU (DAPAT DIKLIK LANGSUNG UNTUK BERPINDAH KATEGORI) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-4">
            <a href="{{ route('subcont.visitor', ['company' => 'PKL']) }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-uniform h-100 {{ $activeCompany === 'PKL' ? 'border border-2 border-primary shadow-sm' : '' }}" style="{{ $activeCompany === 'PKL' ? 'background: #f0fdf4;' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="kpi-title mb-0">Siswa / Mahasiswa PKL</span>
                                @if($activeCompany === 'PKL')
                                    <span class="badge badge-aman px-1.5 py-0.5" style="font-size: 10px;">Dipilih</span>
                                @endif
                            </div>
                            <h3 class="kpi-value text-dark">{{ $totalPkl }}</h3>
                        </div>
                        <div class="kpi-icon-box {{ $activeCompany === 'PKL' ? 'bg-primary text-white' : '' }}">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('subcont.visitor', ['company' => 'VISITOR']) }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-uniform h-100 {{ $activeCompany === 'VISITOR' ? 'border border-2 border-primary shadow-sm' : '' }}" style="{{ $activeCompany === 'VISITOR' ? 'background: #eff6ff;' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="kpi-title mb-0">Visitor / Tamu Umum</span>
                                @if($activeCompany === 'VISITOR')
                                    <span class="badge badge-aman px-1.5 py-0.5" style="font-size: 10px;">Dipilih</span>
                                @endif
                            </div>
                            <h3 class="kpi-value text-dark">{{ $totalVisitor }}</h3>
                        </div>
                        <div class="kpi-icon-box {{ $activeCompany === 'VISITOR' ? 'bg-primary text-white' : '' }}">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('subcont.visitor', ['company' => 'KLIEN']) }}" class="text-decoration-none d-block h-100">
                <div class="card-kpi-uniform h-100 {{ $activeCompany === 'KLIEN' ? 'border border-2 border-primary shadow-sm' : '' }}" style="{{ $activeCompany === 'KLIEN' ? 'background: #fefce8;' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="kpi-title mb-0">Klien / Owner Representative</span>
                                @if($activeCompany === 'KLIEN')
                                    <span class="badge badge-aman px-1.5 py-0.5" style="font-size: 10px;">Dipilih</span>
                                @endif
                            </div>
                            <h3 class="kpi-value text-dark">{{ $totalKlien }}</h3>
                        </div>
                        <div class="kpi-icon-box {{ $activeCompany === 'KLIEN' ? 'bg-primary text-white' : '' }}">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 3. TABEL DATA TAMU & KHUSUS                               -->
    <!-- ======================================================== -->
    <div class="card-custom">
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0" style="color: #003366;">
                    <i class="fa-solid fa-user-shield me-1 text-primary"></i> Data: {{ $activeCompany }}
                </h5>
                <small class="text-muted">Total: <strong>{{ count($personnels) }}</strong> orang tercatat</small>
            </div>
            <form action="{{ route('subcont.visitor') }}" method="GET" class="d-flex gap-2">
                <input type="hidden" name="company" value="{{ $activeCompany }}">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama atau instansi...">
                <button type="submit" class="btn btn-murinda-primary btn-sm"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover m-0 align-middle" id="tabel-visitor-khusus">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">NO</th>
                            <th style="min-width: 170px;">NAMA LENGKAP</th>
                            <th class="text-center" style="width: 90px;">KATEGORI</th>
                            <th style="min-width: 160px;">ASAL INSTANSI / SEKOLAH</th>
                            <th style="min-width: 180px;">PENEMPATAN & POSISI</th>
                            <th class="text-center" style="width: 110px;">TGL MASUK</th>
                            <th class="text-center" style="width: 110px;">TGL SELESAI</th>
                            <th class="text-center" style="width: 95px;">STATUS</th>
                            <th style="min-width: 160px;">KEPERLUAN & CATATAN</th>
                            <th class="text-center no-print" style="width: 210px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnels as $index => $p)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->nama }}</div>
                                    <small class="text-muted font-monospace" style="font-size: 11px;">ID: MIS-{{ $p->company_code }}-{{ str_pad($p->id, 3, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td class="text-center">
                                    @if($p->company_code === 'PKL')
                                        <span class="badge bg-success font-monospace px-2 py-1">PKL / MAGANG</span>
                                    @elseif($p->company_code === 'KLIEN')
                                        <span class="badge bg-warning text-dark font-monospace px-2 py-1">KLIEN</span>
                                    @else
                                        <span class="badge bg-primary font-monospace px-2 py-1">VISITOR</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->asal_instansi ?: ($p->posisi ?: '-') }}</div>
                                </td>
                                <td>
                                    @if($p->company_code === 'PKL')
                                        <div class="d-flex flex-column gap-1">
                                            <div>
                                                <span class="badge bg-primary text-white" style="font-size: 10px;">
                                                    <i class="fa-solid fa-building me-1"></i> Dept: {{ $p->departemen_penempatan ?: 'HSE' }}
                                                </span>
                                            </div>
                                            <div class="fw-bold text-dark small" style="color: #003366 !important;">
                                                {{ $p->posisi ?: 'Staff Magang' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="fw-semibold text-dark">{{ $p->posisi ?: '-' }}</div>
                                        @if($p->departemen_penempatan)
                                            <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Area: {{ $p->departemen_penempatan }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center fw-semibold text-dark" style="font-size: 12px;">
                                    {{ $p->tgl_masuk ? date('d/m/Y', strtotime($p->tgl_masuk)) : '-' }}
                                </td>
                                <td class="text-center font-monospace" style="font-size: 12px;">
                                    @if($p->tgl_keluar)
                                        <span class="fw-semibold text-dark">{{ date('d/m/Y', strtotime($p->tgl_keluar)) }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 11px;">Belum Selesai</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->status === 'Aktif')
                                        <span class="badge-aman">
                                            <i class="fa-solid fa-circle-dot me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted d-inline-block text-truncate" style="max-width: 200px;" title="{{ $p->keterangan ?: '-' }}">
                                        {{ $p->keterangan ?: '-' }}
                                    </small>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-action-view btn-sm" title="Lihat Data Lengkap" onclick="showDetailVisitor({{ json_encode($p) }})">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn btn-action-edit btn-sm" title="Edit Data" onclick="editVisitor({{ json_encode($p) }})">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        @if($p->status === 'Aktif')
                                            <button type="button" class="btn btn-action-finish btn-sm fw-bold" title="Selesaikan Kunjungan / Magang" onclick="bukaModalSelesaiVisitor({{ json_encode($p) }})">
                                                <i class="fa-solid fa-circle-check text-success me-1"></i> Selesai
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users-slash fa-2x mb-2 d-block text-secondary"></i>
                                    Belum ada catatan data kunjungan untuk <strong>{{ $activeCompany }}</strong>.
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
<!-- MODAL FORMULIR MONITORING KHUSUS (PKL / VISITOR / KLIEN) -->
<!-- ======================================================== -->
<div class="modal fade" id="modalTambahVisitor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-shield text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-white mb-0">
                        Formulir Catat Tamu & Monitoring Khusus
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('subcont.visitor.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap: <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-custom" required placeholder="Nama lengkap tamu / siswa...">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kategori: <span class="text-danger">*</span></label>
                            <select name="company_code" id="tambah_company_code" class="form-select form-select-custom" required onchange="toggleTambahKategori(this.value)">
                                <option value="PKL" {{ $activeCompany === 'PKL' ? 'selected' : '' }}>PKL (Siswa SMK / Mahasiswa Magang)</option>
                                <option value="VISITOR" {{ $activeCompany === 'VISITOR' ? 'selected' : '' }}>VISITOR (Tamu Pabrik / Auditor / Vendor)</option>
                                <option value="KLIEN" {{ $activeCompany === 'KLIEN' ? 'selected' : '' }}>KLIEN (Owner Representative / Project Client)</option>
                            </select>
                        </div>
                        
                        <!-- Asal Instansi / Sekolah -->
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" id="label_asal_instansi" style="font-size: 13px;">
                                Asal Sekolah / Universitas / Perusahaan: <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="asal_instansi" id="tambah_asal_instansi" class="form-control form-control-custom" required placeholder="Contoh: SMKN 1 Subang / PT Petrosea Tbk">
                        </div>

                        <!-- Departemen Penempatan Magang / Kunjungan (Bisa di mana saja) -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" id="label_departemen_penempatan" style="font-size: 13px;">
                                Penempatan Bagian / Dept: <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="departemen_penempatan" id="tambah_departemen_penempatan" class="form-control form-control-custom" list="list_penempatan_dept" placeholder="Ketik atau pilih penempatan..." value="HSE">
                            <datalist id="list_penempatan_dept">
                                @foreach($plantDepartments as $deptKey => $deptVal)
                                    <option value="{{ $deptKey }}">{{ $deptVal }}</option>
                                @endforeach
                                <option value="ENGINEERING">ENGINEERING</option>
                                <option value="IT / SISTEM INFORMASI">IT / SISTEM INFORMASI</option>
                                <option value="FINANCE & ACCOUNTING">FINANCE & ACCOUNTING</option>
                                <option value="DOKUMEN KONTROL">DOKUMEN KONTROL</option>
                                <option value="PROYEK LAPANGAN">PROYEK LAPANGAN</option>
                                <option value="SECURITY & PENGAMANAN">SECURITY & PENGAMANAN</option>
                            </datalist>
                            <small class="text-muted" id="help_dept_penempatan" style="font-size: 11px;">Bisa ditempatkan di bagian mana saja</small>
                        </div>

                        <!-- Posisi / Jadi Apa Selama Magang -->
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" id="label_posisi" style="font-size: 13px;">
                                Peran / Jadi Apa Selama Magang: <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="posisi" id="tambah_posisi" class="form-control form-control-custom" required placeholder="Contoh: Staff Magang HSE / Operator Magang / Drafter Magang">
                            <small class="text-muted" id="help_posisi" style="font-size: 11px;">Tugas atau posisi spesifik yang dijalankan</small>
                        </div>

                        <!-- Status: Aktif / Non-Aktif -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status: <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-custom" required>
                                <option value="Aktif" selected>Aktif (Sedang Berlangsung)</option>
                                <option value="Non-Aktif">Non-Aktif (Selesai)</option>
                            </select>
                        </div>

                        <!-- Tanggal Masuk (Required) & Tanggal Keluar (Kosong saat pertama masuk) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk / Mulai: <span class="text-danger">*</span></label>
                            <input type="text" name="tgl_masuk" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" required placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted" style="font-size: 13px;">Tanggal Selesai (Dikosongkan saat awal masuk):</label>
                            <input type="text" name="tgl_keluar" class="form-control form-control-custom flatpickr-date bg-light" placeholder="Diisi otomatis saat klik tombol Selesai" readonly>
                            <small class="text-muted" style="font-size: 11px;">Tanggal selesai akan diinput saat menekan tombol "Selesai"</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Keperluan & PIC Murinda: <span class="text-danger">*</span></label>
                            <input type="text" name="keperluan" class="form-control form-control-custom" required placeholder="Contoh: Praktik Kerja Lapangan / Bertemu Bpk. Hendra HSE">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Catatan APD / Lokasi Kunjungan:</label>
                            <input type="text" name="keterangan" class="form-control form-control-custom" placeholder="Contoh: Diberikan Visitor Helmet & Rompi K3, penempatan Workshop Bay 3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Data Tamu / PKL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL SELESAIKAN KUNJUNGAN / MAGANG (INPUT TGL SELESAI)  -->
<!-- ======================================================== -->
<div class="modal fade" id="modalSelesaiVisitor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-white mb-0">
                        Selesaikan Kunjungan / Periode Magang
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSelesaiVisitor" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3 p-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 10px;">Data Personel / Tamu</div>
                        <h6 class="fw-bold mb-1 text-dark" id="selesai_nama_disp">-</h6>
                        <div class="small text-muted" id="selesai_instansi_disp">-</div>
                        <div class="small text-muted mt-1">
                            Tanggal Masuk: <span class="fw-semibold text-dark" id="selesai_tgl_masuk_disp">-</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                            Tanggal Selesai Kunjungan / Magang: <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="tgl_keluar" id="selesai_tgl_keluar" class="form-control form-control-custom flatpickr-date" value="{{ date('Y-m-d') }}" required placeholder="DD/MM/YYYY">
                        <small class="text-muted" style="font-size: 11px;">Masukkan tanggal resmi selesainya masa magang atau kunjungan.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark" style="font-size: 13px;">
                            Catatan Penutupan / Hasil Kunjungan:
                        </label>
                        <input type="text" name="catatan_selesai" class="form-control form-control-custom" placeholder="Contoh: Praktik magang selesai lengkap dengan laporan / Kunjungan site selesai">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Simpan & Selesaikan Kunjungan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DETAIL TAMU & KHUSUS                               -->
<!-- ======================================================== -->
<div class="modal fade" id="modalDetailVisitor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-id-badge text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-white mb-0">
                        Detail Kunjungan: <span id="detail_visitor_nama"></span>
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered table-sm mb-0">
                    <tr>
                        <th style="width: 40%; background: #f8fafc;">ID Registrasi</th>
                        <td id="detail_visitor_id" class="fw-bold text-navy"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Kategori</th>
                        <td id="detail_visitor_company"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Asal Instansi / Sekolah</th>
                        <td id="detail_visitor_instansi" class="fw-semibold"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Departemen Penempatan</th>
                        <td id="detail_visitor_dept" class="fw-bold text-primary"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Peran / Posisi</th>
                        <td id="detail_visitor_posisi" class="fw-semibold"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Status Kunjungan</th>
                        <td><span id="detail_visitor_status_badge"></span></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Tanggal Mulai / Masuk</th>
                        <td id="detail_visitor_tgl_masuk"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Tanggal Selesai</th>
                        <td id="detail_visitor_tgl_keluar"></td>
                    </tr>
                    <tr>
                        <th style="background: #f8fafc;">Keperluan / Catatan</th>
                        <td id="detail_visitor_keterangan"></td>
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
<!-- MODAL EDIT DATA TAMU & KHUSUS                            -->
<!-- ======================================================== -->
<div class="modal fade" id="modalEditVisitor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #001f3f, #003366); color: #ffffff; border-bottom: 3px solid #dc2626;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-pen text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-white mb-0">
                        Edit Data Kunjungan / Magang
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditVisitor" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Nama Lengkap: <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_visitor_nama" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Kategori: <span class="text-danger">*</span></label>
                            <select name="company_code" id="edit_visitor_company" class="form-select form-select-custom" required>
                                <option value="PKL">PKL (Siswa / Mahasiswa Magang)</option>
                                <option value="VISITOR">VISITOR (Tamu / Vendor)</option>
                                <option value="KLIEN">KLIEN (Owner Representative)</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Asal Instansi / Sekolah: <span class="text-danger">*</span></label>
                            <input type="text" name="asal_instansi" id="edit_visitor_instansi" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Penempatan Bagian / Dept:</label>
                            <input type="text" name="departemen_penempatan" id="edit_visitor_dept" class="form-control form-control-custom" list="list_penempatan_dept_edit" placeholder="Ketik atau pilih penempatan...">
                            <datalist id="list_penempatan_dept_edit">
                                @foreach($plantDepartments as $deptKey => $deptVal)
                                    <option value="{{ $deptKey }}">{{ $deptVal }}</option>
                                @endforeach
                                <option value="ENGINEERING">ENGINEERING</option>
                                <option value="IT / SISTEM INFORMASI">IT / SISTEM INFORMASI</option>
                                <option value="FINANCE & ACCOUNTING">FINANCE & ACCOUNTING</option>
                                <option value="DOKUMEN KONTROL">DOKUMEN KONTROL</option>
                                <option value="PROYEK LAPANGAN">PROYEK LAPANGAN</option>
                                <option value="SECURITY & PENGAMANAN">SECURITY & PENGAMANAN</option>
                            </datalist>
                            <small class="text-muted" style="font-size: 11px;">Bisa ditempatkan di bagian mana saja</small>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Posisi / Jadi Apa: <span class="text-danger">*</span></label>
                            <input type="text" name="posisi" id="edit_visitor_posisi" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Status: <span class="text-danger">*</span></label>
                            <select name="status" id="edit_visitor_status" class="form-select form-select-custom" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Masuk: <span class="text-danger">*</span></label>
                            <input type="text" name="tgl_masuk" id="edit_visitor_tgl_masuk" class="form-control form-control-custom flatpickr-date" required placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Tanggal Selesai:</label>
                            <input type="text" name="tgl_keluar" id="edit_visitor_tgl_keluar" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" style="font-size: 13px;">Keperluan & Catatan K3:</label>
                            <input type="text" name="catatan_induction" id="edit_visitor_keterangan" class="form-control form-control-custom">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
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

        const activeCat = document.getElementById('tambah_company_code')?.value || 'PKL';
        toggleTambahKategori(activeCat);
    });

    function toggleTambahKategori(cat) {
        const labelInstansi = document.getElementById('label_asal_instansi');
        const inputInstansi = document.getElementById('tambah_asal_instansi');
        const labelDept = document.getElementById('label_departemen_penempatan');
        const helpDept = document.getElementById('help_dept_penempatan');
        const labelPosisi = document.getElementById('label_posisi');
        const inputPosisi = document.getElementById('tambah_posisi');
        const helpPosisi = document.getElementById('help_posisi');

        if (cat === 'PKL') {
            if (labelInstansi) labelInstansi.innerHTML = 'Asal Sekolah / Universitas: <span class="text-danger">*</span>';
            if (inputInstansi) inputInstansi.placeholder = 'Contoh: SMKN 1 Subang / Politeknik Negeri Bandung';
            if (labelDept) labelDept.innerHTML = 'Departemen Penempatan Magang: <span class="text-danger">*</span>';
            if (helpDept) helpDept.innerText = 'Divisi tempat siswa/mahasiswa bertugas magang';
            if (labelPosisi) labelPosisi.innerHTML = 'Peran / Menjadi Apa Selama Magang: <span class="text-danger">*</span>';
            if (inputPosisi) inputPosisi.placeholder = 'Contoh: Staff Magang HSE / Operator Magang QC';
            if (helpPosisi) helpPosisi.innerText = 'Tugas atau peran spesifik siswa magang';
        } else if (cat === 'KLIEN') {
            if (labelInstansi) labelInstansi.innerHTML = 'Asal Perusahaan / Instansi Klien: <span class="text-danger">*</span>';
            if (inputInstansi) inputInstansi.placeholder = 'Contoh: PT Petrosea Tbk / PT Freeport Indonesia';
            if (labelDept) labelDept.innerHTML = 'Departemen / Bagian Terkait:';
            if (helpDept) helpDept.innerText = 'Departemen Murinda yang mendampingi';
            if (labelPosisi) labelPosisi.innerHTML = 'Jabatan / Posisi Klien: <span class="text-danger">*</span>';
            if (inputPosisi) inputPosisi.placeholder = 'Contoh: Owner Representative / Project Director';
            if (helpPosisi) helpPosisi.innerText = 'Jabatan perwakilan klien';
        } else {
            if (labelInstansi) labelInstansi.innerHTML = 'Asal Perusahaan / Instansi Tamu: <span class="text-danger">*</span>';
            if (inputInstansi) inputInstansi.placeholder = 'Contoh: PT Sucofindo / Vendor Mesin';
            if (labelDept) labelDept.innerHTML = 'Departemen / Area Kunjungan:';
            if (helpDept) helpDept.innerText = 'Area pabrik atau departemen yang dikunjungi';
            if (labelPosisi) labelPosisi.innerHTML = 'Jabatan / Posisi Tamu: <span class="text-danger">*</span>';
            if (inputPosisi) inputPosisi.placeholder = 'Contoh: Lead Auditor / Service Engineer';
            if (helpPosisi) helpPosisi.innerText = 'Jabatan atau kapasitas tamu';
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }

    function bukaModalSelesaiVisitor(p) {
        document.getElementById('formSelesaiVisitor').action = '/subcont/visitor/' + p.id + '/finish';
        document.getElementById('selesai_nama_disp').innerText = p.nama || '-';
        document.getElementById('selesai_instansi_disp').innerText = (p.asal_instansi || p.posisi || '-') + ' (' + p.company_code + ')';
        document.getElementById('selesai_tgl_masuk_disp').innerText = p.tgl_masuk ? formatDate(p.tgl_masuk) : '-';

        const fp = document.getElementById('selesai_tgl_keluar')._flatpickr;
        if (fp) {
            const today = new Date();
            const y = today.getFullYear();
            const m = ('0' + (today.getMonth() + 1)).slice(-2);
            const d = ('0' + today.getDate()).slice(-2);
            fp.setDate(y + '-' + m + '-' + d, true);
        }

        const modal = new bootstrap.Modal(document.getElementById('modalSelesaiVisitor'));
        modal.show();
    }

    function showDetailVisitor(p) {
        document.getElementById('detail_visitor_nama').innerText = p.nama || '-';
        document.getElementById('detail_visitor_id').innerText = 'MIS-' + p.company_code + '-' + String(p.id).padStart(3, '0');
        document.getElementById('detail_visitor_company').innerText = p.company_code || '-';
        document.getElementById('detail_visitor_instansi').innerText = p.asal_instansi || p.posisi || '-';
        document.getElementById('detail_visitor_dept').innerText = p.departemen_penempatan || '-';
        document.getElementById('detail_visitor_posisi').innerText = p.posisi || '-';

        const badge = document.getElementById('detail_visitor_status_badge');
        if (p.status === 'Aktif') {
            badge.className = 'badge bg-success';
            badge.innerText = 'Aktif';
        } else {
            badge.className = 'badge bg-secondary';
            badge.innerText = 'Non-Aktif';
        }

        document.getElementById('detail_visitor_tgl_masuk').innerText = p.tgl_masuk ? formatDate(p.tgl_masuk) : '-';
        document.getElementById('detail_visitor_tgl_keluar').innerText = p.tgl_keluar ? formatDate(p.tgl_keluar) : 'Belum Selesai';
        document.getElementById('detail_visitor_keterangan').innerText = p.keterangan || '-';

        const modal = new bootstrap.Modal(document.getElementById('modalDetailVisitor'));
        modal.show();
    }

    function editVisitor(p) {
        document.getElementById('formEditVisitor').action = '/subcont/' + p.id;
        document.getElementById('edit_visitor_nama').value = p.nama || '';
        document.getElementById('edit_visitor_company').value = p.company_code || 'VISITOR';
        document.getElementById('edit_visitor_instansi').value = p.asal_instansi || p.posisi || '';
        document.getElementById('edit_visitor_dept').value = p.departemen_penempatan || '';
        document.getElementById('edit_visitor_posisi').value = p.posisi || '';
        document.getElementById('edit_visitor_status').value = p.status === 'Aktif' ? 'Aktif' : 'Non-Aktif';
        document.getElementById('edit_visitor_keterangan').value = p.keterangan || '';

        if (p.tgl_masuk) {
            const fp = document.getElementById('edit_visitor_tgl_masuk')._flatpickr;
            if (fp) fp.setDate(p.tgl_masuk, true);
        }
        if (p.tgl_keluar) {
            const fp = document.getElementById('edit_visitor_tgl_keluar')._flatpickr;
            if (fp) fp.setDate(p.tgl_keluar, true);
        } else {
            const fp = document.getElementById('edit_visitor_tgl_keluar')._flatpickr;
            if (fp) fp.clear();
        }

        const modal = new bootstrap.Modal(document.getElementById('modalEditVisitor'));
        modal.show();
    }

    // Instant Live Filter: Otomatis memfilter saat diketik tanpa perlu enter / klik tombol
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        searchInput?.addEventListener('input', function() {
            const q = (this.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#tabel-visitor-khusus tbody tr');
            rows.forEach(row => {
                if (row.cells.length < 5) return;
                row.style.display = (!q || row.innerText.toLowerCase().includes(q)) ? '' : 'none';
            });
        });
    });
</script>
@endpush
@endsection
