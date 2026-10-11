@extends('layouts.app')

@section('title', 'Schedule Audit Departemen')
@section('page_heading', 'Schedule Audit Departemen')
@section('page_subheading', 'Penjadwalan & Realisasi Audit Internal Departemen')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Action Bar -->
    <div class="row mb-3 align-items-center">
        <div class="col-12 text-end">
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                <form action="{{ route('audit-schedule.index') }}" method="GET" class="d-inline-flex align-items-center gap-1 me-2">
                    <label class="fw-bold small text-muted mb-0">Tahun:</label>
                    <select name="tahun" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
                        @for($y = date('Y')-1; $y <= date('Y')+3; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>

                <button type="button" class="btn btn-gradient-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Jadwal Audit
                </button>
                <button type="button" class="btn btn-emerald-excel btn-sm" onclick="exportTableToExcel('tableAuditSchedule', 'Schedule_Audit_Murinda_{{ $tahun }}')">
                    <i class="fa-solid fa-file-excel me-1"></i> Ekspor Excel
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Cetak Dokumen
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Report Badges (Audit Report Insights) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">DEPARTEMEN TERJADWAL AUDIT</div>
                        <h4 class="fw-bold mb-0">{{ $totalDeptCount }} Departemen</h4>
                    </div>
                    <i class="fa-solid fa-building-circle-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-info text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">TOTAL PROGRAM AUDIT TERDENGAR</div>
                        <h4 class="fw-bold mb-0">{{ $totalScheduledAudits }} Sesi Audit</h4>
                    </div>
                    <i class="fa-solid fa-clipboard-list fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-success text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">AUDIT TEREALISASI / SELESAI</div>
                        <h4 class="fw-bold mb-0">{{ $totalCompletedAudits }} Sesi ({{ $totalScheduledAudits > 0 ? number_format(($totalCompletedAudits / $totalScheduledAudits) * 100, 1) : 0 }}%)</h4>
                    </div>
                    <i class="fa-solid fa-circle-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Schedule Audit -->
    <div class="card-custom">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="card-title-custom m-0">
                <i class="fa-solid fa-calendar-check text-primary me-2"></i> Schedule Pelaksanaan Audit Departemen Tahun {{ $tahun }}
            </h5>
            <span class="badge bg-primary text-white px-3 py-1">Total Schedule: {{ $schedules->count() }} Departemen</span>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-custom align-middle mb-0" id="tableAuditSchedule">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="min-width: 140px;">Departemen</th>
                        <th style="min-width: 140px;">Lead Auditor</th>
                        <th style="min-width: 130px;">Auditor</th>
                        <th style="min-width: 130px;">Wakil Auditor</th>
                        <th class="text-center" style="font-size: 11px;">Jan</th>
                        <th class="text-center" style="font-size: 11px;">Feb</th>
                        <th class="text-center" style="font-size: 11px;">Mar</th>
                        <th class="text-center" style="font-size: 11px;">Apr</th>
                        <th class="text-center" style="font-size: 11px;">Mei</th>
                        <th class="text-center" style="font-size: 11px;">Jun</th>
                        <th class="text-center" style="font-size: 11px;">Jul</th>
                        <th class="text-center" style="font-size: 11px;">Agu</th>
                        <th class="text-center" style="font-size: 11px;">Sep</th>
                        <th class="text-center" style="font-size: 11px;">Okt</th>
                        <th class="text-center" style="font-size: 11px;">Nov</th>
                        <th class="text-center" style="font-size: 11px;">Des</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $index => $s)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $s->departemen }}</td>
                        <td>{{ $s->lead_auditor }}</td>
                        <td>{{ $s->auditor }}</td>
                        <td>{{ $s->wakil_auditor ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->jan_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->feb_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->mar_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->apr_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->may_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->jun_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->jul_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->aug_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->sep_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->oct_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->nov_date ?: '-' }}</td>
                        <td class="text-center text-nowrap" style="font-size: 11px;">{{ $s->dec_date ?: '-' }}</td>
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalEditAudit{{ $s->id }}">
                                Edit
                            </button>
                            <form action="{{ route('audit-schedule.destroy', $s->id) }}" method="POST" class="d-inline form-delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Audit -->
                    <div class="modal fade" id="modalEditAudit{{ $s->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('audit-schedule.update', $s->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Schedule Audit {{ $s->departemen }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Tahun</label>
                                                <input type="number" name="tahun" value="{{ $s->tahun }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-bold">Departemen</label>
                                                <input type="text" name="departemen" value="{{ $s->departemen }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Lead Auditor</label>
                                                <input type="text" name="lead_auditor" value="{{ $s->lead_auditor }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Auditor</label>
                                                <input type="text" name="auditor" value="{{ $s->auditor }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Wakil Auditor</label>
                                                <input type="text" name="wakil_auditor" value="{{ $s->wakil_auditor }}" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="fw-bold mb-2 text-primary" style="font-size: 13px;"><i class="fa-solid fa-calendar-days me-1"></i> Tanggal Pelaksanaan per Bulan (Format Fleksibel: Manual / Picker Kalender)</div>
                                        <div class="row g-2">
                                            @php
                                                $months = [
                                                    'jan_date' => 'Januari', 'feb_date' => 'Februari', 'mar_date' => 'Maret',
                                                    'apr_date' => 'April', 'may_date' => 'Mei', 'jun_date' => 'Juni',
                                                    'jul_date' => 'Juli', 'aug_date' => 'Agustus', 'sep_date' => 'September',
                                                    'oct_date' => 'Oktober', 'nov_date' => 'November', 'dec_date' => 'Desember'
                                                ];
                                            @endphp
                                            @foreach($months as $field => $mName)
                                            <div class="col-md-3">
                                                <label class="form-label mb-1" style="font-size: 11px;">{{ $mName }}</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" name="{{ $field }}" value="{{ $s->$field }}" class="form-control" placeholder="Tgl (misal: 15/01)">
                                                    <input type="date" class="form-control px-1" onchange="this.previousElementSibling.value = new Date(this.value).toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit'})">
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-2">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                            Belum ada jadwal pelaksanaan audit untuk tahun {{ $tahun }}. Klik tombol "Tambah Jadwal Audit".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal Audit -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('audit-schedule.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-calendar-plus me-2"></i> Tambah Schedule Audit Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tahun Audit</label>
                            <input type="number" name="tahun" value="{{ $tahun }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Departemen</label>
                            <input type="text" name="departemen" class="form-control form-control-sm" placeholder="Misal: Produksi, QA/QC, Fabrikasi" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Lead Auditor</label>
                            <input type="text" name="lead_auditor" class="form-control form-control-sm" placeholder="Nama Lead Auditor" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Auditor</label>
                            <input type="text" name="auditor" class="form-control form-control-sm" placeholder="Nama Auditor 1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Wakil Auditor</label>
                            <input type="text" name="wakil_auditor" class="form-control form-control-sm" placeholder="Nama Wakil Auditor">
                        </div>
                    </div>
                    <hr>
                    <div class="fw-bold mb-2 text-primary" style="font-size: 13px;">
                        <i class="fa-solid fa-calendar-days me-1"></i> Penjadwalan Tanggal Audit per Bulan (Pilih Bulan via Dropdown & Isi Tanggal)
                    </div>

                    <!-- Dropdown Bulan & Input Tanggal (Simplified) -->
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label mb-1 fw-bold text-navy small">Pilih Bulan Target Audit <span class="text-danger">*</span></label>
                                <select id="select-target-month-add" class="form-select form-select-sm" onchange="syncAuditMonthField('add')" required>
                                    <option value="jan_date">Januari</option>
                                    <option value="feb_date">Februari</option>
                                    <option value="mar_date">Maret</option>
                                    <option value="apr_date">April</option>
                                    <option value="may_date">Mei</option>
                                    <option value="jun_date">Juni</option>
                                    <option value="jul_date">Juli</option>
                                    <option value="aug_date" selected>Agustus</option>
                                    <option value="sep_date">September</option>
                                    <option value="oct_date">Oktober</option>
                                    <option value="nov_date">November</option>
                                    <option value="dec_date">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 fw-bold text-navy small">Tgl Pelaksanaan Audit <span class="text-danger">*</span></label>
                                <input type="text" id="input-target-date-add" class="form-control form-control-sm" placeholder="Contoh: Tgl 15 / 18-08-2026" oninput="syncAuditMonthField('add')" required>
                            </div>
                        </div>

                        <!-- Hidden Month Inputs for DB Storage -->
                        <input type="hidden" name="jan_date" id="add_jan_date">
                        <input type="hidden" name="feb_date" id="add_feb_date">
                        <input type="hidden" name="mar_date" id="add_mar_date">
                        <input type="hidden" name="apr_date" id="add_apr_date">
                        <input type="hidden" name="may_date" id="add_may_date">
                        <input type="hidden" name="jun_date" id="add_jun_date">
                        <input type="hidden" name="jul_date" id="add_jul_date">
                        <input type="hidden" name="aug_date" id="add_aug_date">
                        <input type="hidden" name="sep_date" id="add_sep_date">
                        <input type="hidden" name="oct_date" id="add_oct_date">
                        <input type="hidden" name="nov_date" id="add_nov_date">
                        <input type="hidden" name="dec_date" id="add_dec_date">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check me-1"></i> Simpan Schedule Audit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function syncAuditMonthField(prefix) {
    const monthSelect = document.getElementById('select-target-month-' + prefix);
    const dateInput = document.getElementById('input-target-date-' + prefix);
    if (monthSelect && dateInput) {
        const fieldName = monthSelect.value;
        const targetField = document.getElementById(prefix + '_' + fieldName);
        if (targetField) {
            targetField.value = dateInput.value;
        }
    }
}
function applyQuickAuditDate(prefix) {
    syncAuditMonthField(prefix);
}
</script>
@endsection
