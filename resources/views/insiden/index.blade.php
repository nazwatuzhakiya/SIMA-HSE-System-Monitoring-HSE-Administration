@extends('layouts.app')

@section('title', 'Laporan Insiden K3')
@section('page_heading', 'Laporan Insiden K3')
@section('page_subheading', 'Pencatatan Kejadian Near-Miss, First Aid, dan LTI (Lost Time Injury)')

@section('content')
<div class="d-flex justify-content-end gap-2 mb-3">
    <button onclick="exportTableToExcel('table-insiden', 'Laporan_Insiden_K3_Murinda')" class="btn btn-emerald-excel btn-sm">
        <i class="fa-solid fa-file-excel me-1"></i> Export Insiden to Excel (.xlsx)
    </button>
    <button class="btn btn-gradient-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahInsiden">
        <i class="fa-solid fa-plus-circle me-1"></i> Catat Insiden K3 Baru
    </button>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle" id="table-insiden">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Insiden</th>
                        <th>Tanggal</th>
                        <th>Lokasi Kejadian</th>
                        <th>Kategori Insiden</th>
                        <th>Korban (Opsional)</th>
                        <th>Saksi</th>
                        <th>PJ / Ka. Dept</th>
                        <th>Deskripsi Kejadian</th>
                        <th>Tindak Lanjut Perbaikan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insidens as $idx => $in)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="fw-bold text-danger">{{ $in->kode_insiden }}</td>
                            <td>{{ date('d/m/Y', strtotime($in->tanggal)) }}</td>
                            <td class="fw-semibold">{{ $in->lokasi }}</td>
                            <td>
                                <span class="badge {{ $in->kategori == 'LTI' ? 'badge-bahaya' : ($in->kategori == 'First Aid' ? 'badge-warning' : 'badge-aman') }}">
                                    {{ $in->kategori }}
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $in->nama_korban ?: 'Tidak Ada Korban' }}</span></td>
                            <td class="small">{{ $in->nama_saksi ?: '-' }}</td>
                            <td class="small fw-semibold">{{ $in->pj_departemen ?: '-' }}</td>
                            <td class="small">{{ $in->deskripsi_kejadian }}</td>
                            <td class="small text-muted">{{ $in->tindak_lanjut ?: '-' }}</td>
                            <td>
                                <span class="badge {{ $in->status == 'Closed' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $in->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Nol Insiden K3 Tercatat (Zero Accident).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Input Insiden K3 -->
<div class="modal fade" id="modalTambahInsiden" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header text-white" style="background: var(--gradient-header); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Form Pencatatan Insiden K3 Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('insiden.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Kejadian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasi Area Kejadian <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control form-control-custom" placeholder="Contoh: Area Built Up 9 / Fabrikasi Bay 2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Insiden K3 <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select form-select-custom" required>
                                <option value="Near-Miss">Near-Miss (Hampir Celaka)</option>
                                <option value="First Aid">First Aid (Pertolongan Pertama)</option>
                                <option value="LTI">LTI (Lost Time Injury / Cedera)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Investigasi</label>
                            <select name="status" class="form-select form-select-custom">
                                <option value="Open">Open (Baru Dilaporkan)</option>
                                <option value="Investigasi">Investigasi Tim HSE</option>
                                <option value="Closed">Closed (Selesai Penanganan)</option>
                            </select>
                        </div>

                        <!-- Fields Barunya: Korban (Opsional), Saksi, PJ/Ketua Dept -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Korban <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" name="nama_korban" class="form-control form-control-custom" placeholder="Nama Korban (Kosongkan jika tidak ada)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Saksi <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" name="nama_saksi" class="form-control form-control-custom" placeholder="Nama Saksi Kejadian">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PJ / Ketua Departemen Terkait</label>
                            <input type="text" name="pj_departemen" class="form-control form-control-custom" placeholder="Contoh: Budi Santoso (Kabag MNT)">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Deskripsi Kejadian Rinci <span class="text-danger">*</span></label>
                            <textarea name="deskripsi_kejadian" class="form-control form-control-custom" rows="3" placeholder="Jelaskan kronologi kejadian insiden secara rinci..." required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tindak Lanjut & Action Plan Perbaikan</label>
                            <textarea name="tindak_lanjut" class="form-control form-control-custom" rows="2" placeholder="Rencana aksi korektif agar kejadian serupa tidak terulang..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-blue btn-sm">Simpan Insiden K3</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
