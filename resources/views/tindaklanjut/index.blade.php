@extends('layouts.app')

@section('title', 'Tindak Lanjut Laporan Bahaya')
@section('page_heading', 'Tindak Lanjut Laporan Bahaya')
@section('page_subheading', 'Pengelolaan & Pembaruan Status Laporan Hazard dari Pekerja / Portal Publik')

@section('content')
<!-- Card Filter Status Laporan & Inline Export -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('tindaklanjut.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5">
            <label class="form-label small fw-bold">Filter Status Laporan:</label>
            <select name="status" class="form-select form-select-custom" onchange="this.form.submit()">
                <option value="">-- Semua Status (Menunggu, Diproses, Selesai) --</option>
                <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu (Baru Masuk)</option>
                <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses (Sedang Ditangani)</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai (Sudah Diperbaiki)</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Tingkat Risiko:</label>
            <select name="tingkat_risiko" class="form-select form-select-custom" onchange="this.form.submit()">
                <option value="">-- Semua Tingkat Risiko --</option>
                <option value="Tinggi" {{ request('tingkat_risiko') == 'Tinggi' ? 'selected' : '' }}>Tinggi (Gawat)</option>
                <option value="Sedang" {{ request('tingkat_risiko') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Rendah" {{ request('tingkat_risiko') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" onclick="exportTableToExcel('table-tindaklanjut', 'Data_Tindak_Lanjut_Laporan_Bahaya')" class="btn btn-emerald-excel w-100 btn-sm py-2 fw-bold">
                <i class="fa-solid fa-file-excel me-1"></i> Export Excel (.xlsx)
            </button>
        </div>
    </form>
</div>

<!-- Table Tindak Lanjut -->
<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle" id="table-tindaklanjut">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Tiket</th>
                        <th>Pelapor</th>
                        <th>Area / Lokasi</th>
                        <th>Deskripsi Bahaya</th>
                        <th>Risiko</th>
                        <th>Status Progress</th>
                        <th>Catatan Tindak Lanjut HSE</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-primary">{{ $r->ticket_code }}</td>
                            <td class="fw-semibold text-dark">{{ $r->nama_pelapor ?: 'Anonim' }}</td>
                            <td class="small">{{ $r->area_lokasi }}</td>
                            <td class="small" style="max-width: 250px;">{{ $r->deskripsi }}</td>
                            <td>
                                <span class="badge {{ $r->tingkat_risiko == 'Tinggi' ? 'badge-bahaya' : ($r->tingkat_risiko == 'Sedang' ? 'badge-warning' : 'badge-aman') }}">
                                    {{ $r->tingkat_risiko }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $r->status == 'Selesai' ? 'bg-success' : ($r->status == 'Diproses' ? 'bg-warning text-dark' : 'bg-secondary') }} px-3 py-1 fs-6">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td>
                                @if($r->tindak_lanjut)
                                    <span class="small text-dark">{{ $r->tindak_lanjut }}</span>
                                @else
                                    <span class="small text-muted fst-italic" style="color: #9ca3af !important;">(Klik 'Tindak Lanjut' untuk mengisi catatan perbaikan secara manual)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary fw-bold"
                                        style="padding: 4px 10px; border-radius: 6px;"
                                        onclick="openTindakLanjutModal(
                                            {{ $r->id }},
                                            '{{ addslashes($r->ticket_code) }}',
                                            '{{ addslashes($r->nama_pelapor ?: 'Anonim') }}',
                                            '{{ addslashes($r->area_lokasi) }}',
                                            '{{ addslashes($r->deskripsi) }}',
                                            '{{ $r->status }}',
                                            '{{ addslashes($r->tindak_lanjut ?: '') }}'
                                        )">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Tindak Lanjut
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Belum ada laporan potensi bahaya yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tindak Lanjut / Edit Status Laporan -->
<div class="modal fade" id="modalTindakLanjut" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header text-white" style="background: var(--gradient-header); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-shield-halved me-2"></i> Tindak Lanjut Laporan Bahaya HSE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTindakLanjut" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold text-primary" id="modal-ticket-code"></span>
                            <span class="text-muted small" id="modal-pelapor"></span>
                        </div>
                        <div class="fw-semibold text-dark mb-1"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Lokasi: <span id="modal-lokasi"></span></div>
                        <div class="small text-muted" id="modal-deskripsi"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Tindak Lanjut HSE <span class="text-danger">*</span></label>
                        <select name="status" id="modal-status" class="form-select form-select-custom" required>
                            <option value="Menunggu">Menunggu (Laporan baru masuk & belum ditangani)</option>
                            <option value="Diproses">Diproses (Sedang ditindaklanjuti tim HSE)</option>
                            <option value="Selesai">Selesai (Sudah dilakukan perbaikan / penanganan lengkap)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Tindak Lanjut & Perbaikan HSE <span class="text-danger">*</span></label>
                        <textarea name="tindak_lanjut" id="modal-tindak-lanjut-text" class="form-control form-control-custom" rows="4" placeholder="Tuliskan tindakan perbaikan yang dilakukan HSE (contoh: Memasang safety sign, perbaikan fasilitas, briefing K3 area)..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-blue btn-sm font-weight-bold">Simpan Perbaruan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openTindakLanjutModal(id, ticket, pelapor, lokasi, deskripsi, status, tindakLanjut) {
    document.getElementById('formTindakLanjut').action = '/tindak-lanjut/' + id;
    document.getElementById('modal-ticket-code').textContent = ticket;
    document.getElementById('modal-pelapor').textContent = 'Pelapor: ' + pelapor;
    document.getElementById('modal-lokasi').textContent = lokasi;
    document.getElementById('modal-deskripsi').textContent = 'Deskripsi: ' + deskripsi;
    document.getElementById('modal-status').value = status;
    document.getElementById('modal-tindak-lanjut-text').value = tindakLanjut || '';
    new bootstrap.Modal(document.getElementById('modalTindakLanjut')).show();
}
</script>
@endpush
