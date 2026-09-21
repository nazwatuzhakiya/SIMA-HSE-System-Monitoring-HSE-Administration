@extends('layouts.app')

@section('title', 'Modul Absensi SMT & TBM')
@section('page_heading', 'Modul Absensi SMT & TBM')
@section('page_subheading', 'Sistem Input Absensi Cepat & Data Fiks Terkunci')

@section('content')
<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="{{ route('absensi.print', ['jenis_kegiatan' => $jenisKegiatan, 'tanggal' => $tanggal, 'departemen' => $departemen]) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 py-2 fw-bold" style="border-radius: 6px;">
        <i class="fa-solid fa-print me-1"></i> Cetak Form TTD (PDF)
    </a>
    <button onclick="exportTableToExcel('table-absensi', 'Rekap_Absensi_SMT_TBM')" class="btn btn-emerald-excel btn-sm">
        <i class="fa-solid fa-file-excel me-1"></i> Export Excel (.xlsx)
    </button>
</div>

@php
    $activeTab = request('tab', 'history');
@endphp

<!-- Nav Tabs: Riwayat Histori Absensi (Default First) vs Form Absensi Harian -->
<ul class="nav nav-tabs border-bottom-0 mb-3" id="absensiTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab == 'history' ? 'active' : '' }} fw-bold text-navy py-2 px-3" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
            <i class="fa-solid fa-clock-rotate-left me-1 text-warning"></i> Riwayat Absensi (Histori Rekap Lengkap)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab == 'input' ? 'active' : '' }} fw-bold text-navy py-2 px-3" id="input-tab" data-bs-toggle="tab" data-bs-target="#input-pane" type="button" role="tab">
            <i class="fa-solid fa-clipboard-check me-1"></i> Form Absensi Harian ({{ $jenisKegiatan }} - {{ date('d/m/Y', strtotime($tanggal)) }})
        </button>
    </li>
</ul>

<div class="tab-content" id="absensiTabContent">
    <!-- TAB 1: RIWAYAT ABSENSI HISTORI REKAP LENGKAP (DEFAULT FIRST) -->
    <div class="tab-pane fade {{ $activeTab == 'history' ? 'show active' : '' }}" id="history-pane" role="tabpanel">
        <div class="card-custom">
            <div class="card-body p-3">
                <form action="{{ route('absensi.index') }}" method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="history">
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-bold" style="font-size: 11px;">Mulai Tanggal:</label>
                        <input type="date" name="hist_start_date" value="{{ request('hist_start_date') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-bold" style="font-size: 11px;">Sampai Tanggal:</label>
                        <input type="date" name="hist_end_date" value="{{ request('hist_end_date') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-bold" style="font-size: 11px;">Filter Subcont / Dept:</label>
                        <select name="hist_dept" class="form-select form-select-sm">
                            <option value="SEMUA">-- Semua Subcont & Dept --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d }}" {{ request('hist_dept') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            <i class="fa-solid fa-filter me-1"></i> Filter Histori
                        </button>
                        <a href="{{ route('absensi.index', ['tab' => 'history']) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="card-title-custom mb-0">
                    <i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> Log Histori Rekap Absensi Terdaftar ({{ $historyLogs->count() }} Record)
                </h5>
                <button onclick="exportTableToExcel('table-history-absensi', 'Riwayat_Absensi_HSE')" class="btn btn-emerald-excel btn-sm">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel Histori
                </button>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-custom align-middle mb-0" id="table-history-absensi">
                    <thead class="align-middle">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 80px;">Kegiatan</th>
                            <th style="min-width: 170px;">Nama Personel</th>
                            <th style="min-width: 140px;">Subcont / Dept</th>
                            <th style="width: 110px;">Kehadiran</th>
                            <th>Keterangan</th>
                            <th>Alasan Perubahan</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyLogs as $idx => $hl)
                        <tr>
                            <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                            <td class="text-center fw-bold text-navy" style="font-size: 11px;">{{ date('d/m/Y', strtotime($hl->tanggal)) }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $hl->jenis_kegiatan }}</span></td>
                            <td class="fw-bold text-dark">{{ $hl->nama_personel }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $hl->departemen_subcont }}</span></td>
                            <td class="text-center">
                                @if($hl->status_kehadiran == 'Hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($hl->status_kehadiran == 'Sakit')
                                    <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($hl->status_kehadiran == 'Izin')
                                    <span class="badge bg-warning text-dark">Izin</span>
                                @elseif($hl->status_kehadiran == 'Telat')
                                    <span class="badge bg-info text-dark">Telat</span>
                                @else
                                    <span class="badge bg-danger">Alpha</span>
                                @endif
                            </td>
                            <td class="small">{{ $hl->keterangan ?: '-' }}</td>
                            <td class="small text-muted italic">{{ $hl->alasan_perubahan ?: '-' }}</td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold"
                                        style="font-size: 11px; border-radius: 6px;"
                                        title="Edit Status Kehadiran Pekerja"
                                        onclick="openEditHistoryModal(
                                            {{ $hl->id }},
                                            '{{ addslashes($hl->nama_personel) }}',
                                            '{{ $hl->status_kehadiran }}',
                                            '{{ addslashes($hl->keterangan ?: '') }}',
                                            '{{ addslashes($hl->alasan_perubahan ?: '') }}'
                                        )">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data riwayat absensi tersimpan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: FORM ABSENSI HARIAN -->
    <div class="tab-pane fade {{ $activeTab == 'input' ? 'show active' : '' }}" id="input-pane" role="tabpanel">
        <!-- Header Filter Activity, Date, & Dept (Filter Otomatis) -->
        <div class="card-custom p-3 mb-4">
            <form action="{{ route('absensi.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Pilih Jenis Kegiatan:</label>
                    <select name="jenis_kegiatan" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="SMT" {{ $jenisKegiatan == 'SMT' ? 'selected' : '' }}>SMT (Safety Morning Talk)</option>
                        <option value="TBM" {{ $jenisKegiatan == 'TBM' ? 'selected' : '' }}>TBM (Toolbox Meeting)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Tanggal Kegiatan:</label>
                    <input type="date" name="tanggal" class="form-control form-control-custom" value="{{ $tanggal }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Pilih Departemen / Subcont:</label>
                    <select name="departemen" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="SEMUA">-- Semua Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $departemen == $dept ? 'selected' : '' }}>Departemen / Subcont: {{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

@if($viewMode == 'fiks')
    <!-- VIEW MODE: DATA FIKS ABSENSI TERKUNCI -->
    <div class="card-custom mb-4 border-start border-4 border-success">
        <div class="card-header-custom bg-success-subtle">
            <h5 class="card-title-custom text-success">
                <i class="fa-solid fa-lock text-success me-2"></i> DATA FIKS ABSENSI TERKUNCI — {{ $jenisKegiatan }} ({{ date('d F Y', strtotime($tanggal)) }})
            </h5>
            <span class="badge bg-success px-3 py-2 fs-6">
                <i class="fa-solid fa-circle-check me-1"></i> Data Terkunci (Fiks)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom align-middle" id="table-absensi">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. ID</th>
                            <th>Nama Pekerja</th>
                            <th>Departemen</th>
                            <th class="text-center">Status Kehadiran Fiks</th>
                            <th>Keterangan</th>
                            <th>Alasan Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manpowers as $index => $mp)
                            @php
                                $rec = $existing->get($mp->id);
                                $currStatus = $rec ? $rec->status_kehadiran : 'Hadir';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $mp->no_id }}</td>
                                <td class="fw-bold text-dark">{{ $mp->nama }}</td>
                                <td><span class="badge bg-secondary">{{ $mp->departemen }}</span></td>
                                <td class="text-center">
                                    <span class="badge {{ $currStatus == 'Hadir' ? 'bg-success' : ($currStatus == 'Telat' ? 'bg-info text-dark' : ($currStatus == 'Alpha' ? 'bg-danger' : 'bg-warning text-dark')) }} px-3 py-1 fs-6">
                                        <i class="fa-solid fa-check me-1"></i> {{ $currStatus }}
                                    </span>
                                </td>
                                <td class="small">{{ $rec ? ($rec->keterangan ?: '-') : '-' }}</td>
                                <td class="small text-muted italic">{{ $rec && $rec->alasan_perubahan ? $rec->alasan_perubahan : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada data pekerja terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-3">
            <div class="small text-muted">
                <i class="fa-solid fa-info-circle text-primary me-1"></i> Data absensi harian ini telah dikunci sebagai Data Fiks. Untuk mengubah status kehadiran atau keterangan pekerja, silakan lakukan di tab <strong>Riwayat Absensi</strong>.
            </div>
        </div>
    </div>
@else
    <!-- VIEW MODE: INPUT FORM FAST ATTENDANCE -->
    <form action="{{ route('absensi.store') }}" method="POST" id="formInputAbsensi">
        @csrf
        <input type="hidden" name="jenis_kegiatan" value="{{ $jenisKegiatan }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="departemen_subcont" value="{{ $departemen }}">
        <input type="hidden" name="alasan_perubahan" id="alasan_perubahan_hidden" value="">

        <div class="card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fa-solid fa-pen-to-square text-primary"></i> Fast Attendance Entry: {{ $jenisKegiatan }} ({{ date('d F Y', strtotime($tanggal)) }})
                </h5>
                <div class="badge bg-primary-subtle text-primary border border-primary px-3 py-2">
                    <i class="fa-solid fa-user-check me-1"></i> Pilih Opsi Kehadiran
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle" id="table-absensi">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No. ID</th>
                                <th>Nama Pekerja</th>
                                <th>Departemen</th>
                                <th class="text-center" style="width: 380px;">Status Kehadiran</th>
                                <th>Keterangan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($manpowers as $index => $mp)
                                @php
                                    $rec = $existing->get($mp->id);
                                    $currStatus = $rec ? $rec->status_kehadiran : 'Hadir';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-primary">{{ $mp->no_id }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ $mp->nama }}
                                        <input type="hidden" name="attendance[{{ $mp->id }}][nama]" value="{{ $mp->nama }}">
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $mp->departemen }}</span></td>
                                    <td class="text-center">
                                        <!-- Dynamic Checkmark Option Group -->
                                        <div class="btn-group w-100 attendance-btn-group" role="group">
                                            @foreach(['Hadir' => 'btn-outline-success', 'Izin' => 'btn-outline-warning', 'Sakit' => 'btn-outline-warning', 'Telat' => 'btn-outline-info', 'Alpha' => 'btn-outline-danger'] as $st => $btnClass)
                                                <input type="radio"
                                                       class="btn-check attendance-radio"
                                                       name="attendance[{{ $mp->id }}][status]"
                                                       id="st_{{ $mp->id }}_{{ $st }}"
                                                       value="{{ $st }}"
                                                       {{ $currStatus == $st ? 'checked' : '' }}
                                                       onchange="updateCheckmarkIcons({{ $mp->id }})">
                                                <label class="btn {{ $btnClass }} btn-sm font-weight-bold d-flex align-items-center justify-content-center gap-1"
                                                       for="st_{{ $mp->id }}_{{ $st }}"
                                                       id="label_{{ $mp->id }}_{{ $st }}">
                                                    <i class="fa-solid fa-check icon-check {{ $currStatus == $st ? '' : 'd-none' }}"></i>
                                                    {{ $st }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $mp->id }}][keterangan]" class="form-control form-control-sm form-control-custom" value="{{ $rec ? $rec->keterangan : '' }}" placeholder="Catatan izin / sakit / telat...">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data pekerja terdaftar di departemen ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                @if($isSaved)
                    <a href="{{ route('absensi.index', ['jenis_kegiatan' => $jenisKegiatan, 'tanggal' => $tanggal, 'departemen' => $departemen, 'mode' => 'fiks']) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal & Kembali ke Data Fiks
                    </a>
                @else
                    <div></div>
                @endif
                <button type="submit" class="btn btn-gradient-blue btn-lg px-4 fs-6">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan & Kunci Sebagai Data Fiks
                </button>
            </div>
        </div>
    </form>
@endif
    </div>
</div>

<!-- Modal Edit Status Kehadiran khusus Riwayat Absensi -->
<div class="modal fade" id="modalEditHistoryAbsensi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #004080, #001f3f); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Status Absensi Pekerja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('absensi.updateSingle') }}" method="POST">
                @csrf
                <input type="hidden" name="absensi_id" id="hist-absensi-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pekerja</label>
                        <input type="text" id="hist-nama-pekerja" class="form-control form-control-custom bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Kehadiran Baru <span class="text-danger">*</span></label>
                        <select name="status_kehadiran" id="hist-status-kehadiran" class="form-select form-select-custom" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                            <option value="Telat">Telat</option>
                            <option value="Alpha">Alpha / Tidak Masuk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan (Surat Dokter / Catatan)</label>
                        <input type="text" name="keterangan" id="hist-keterangan" class="form-control form-control-custom" placeholder="Contoh: Sakit Demam / Izin Keluarga">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Perubahan Data <span class="text-danger">*</span></label>
                        <textarea name="alasan_perubahan" id="hist-alasan" class="form-control form-control-custom" rows="2" placeholder="Tuliskan alasan perbaikan status..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan Status</button>
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

    function updateCheckmarkIcons(mpId) {
        const statuses = ['Hadir', 'Izin', 'Sakit', 'Telat', 'Alpha'];
        statuses.forEach(st => {
            const radio = document.getElementById('st_' + mpId + '_' + st);
            const label = document.getElementById('label_' + mpId + '_' + st);
            if (label) {
                const icon = label.querySelector('.icon-check');
                if (icon) {
                    if (radio && radio.checked) {
                        icon.classList.remove('d-none');
                    } else {
                        icon.classList.add('d-none');
                    }
                }
            }
        });
    }
</script>
@endpush
