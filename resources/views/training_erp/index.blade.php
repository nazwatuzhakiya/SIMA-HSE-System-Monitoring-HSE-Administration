@extends('layouts.app')

@section('title', 'Monitoring Training ERP')
@section('page_heading', 'Monitoring Training ERP')

@section('content')
<div class="container-fluid px-0">

    <!-- Action & Filter Bar -->
    <div class="card-custom">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                
                <!-- Filter Form -->
                <form action="{{ route('training-erp.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Filter Jenis Team ERP:</label>
                        <select name="jenis_team" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                            <option value="">-- Semua Team ERP --</option>
                            @foreach($teams as $t)
                                <option value="{{ $t }}" {{ request('jenis_team') == $t ? 'selected' : '' }}>Team {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Status Struktur:</label>
                        <select name="struktur" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="Ya" {{ request('struktur') == 'Ya' ? 'selected' : '' }}>Struktur: Ya</option>
                            <option value="Tidak" {{ request('struktur') == 'Tidak' ? 'selected' : '' }}>Struktur: Tidak</option>
                        </select>
                    </div>

                    @if(request()->anyFilled(['jenis_team', 'struktur', 'subcont']))
                        <a href="{{ route('training-erp.index') }}" class="btn btn-outline-secondary btn-sm mt-3" style="font-size: 11px;">Reset Filter</a>
                    @endif
                </form>

                <!-- Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-gradient-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahErp">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Peserta Training
                    </button>
                    <button type="button" class="btn btn-emerald-excel btn-sm" onclick="exportTableToExcel('tableTrainingErp', 'Monitoring_Training_ERP_Murinda')">
                        <i class="fa-solid fa-file-excel me-1"></i> Ekspor Excel
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Cetak Dokumen
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Summary Badges -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">TOTAL PESERTA ERP</div>
                        <h4 class="fw-bold mb-0">{{ $trainings->count() }} Orang</h4>
                    </div>
                    <i class="fa-solid fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-danger text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">TEAM KEBAKARAN</div>
                        <h4 class="fw-bold mb-0">{{ $trainings->where('jenis_team', 'Kebakaran')->count() }} Orang</h4>
                    </div>
                    <i class="fa-solid fa-fire-extinguisher fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-success text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 fw-bold" style="font-size: 11px;">TEAM P3K / MEDIS</div>
                        <h4 class="fw-bold mb-0">{{ $trainings->where('jenis_team', 'P3K')->count() }} Orang</h4>
                    </div>
                    <i class="fa-solid fa-kit-medical fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-warning text-dark rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-dark-50 fw-bold" style="font-size: 11px;">TEAM EVAKUASI & UMUM</div>
                        <h4 class="fw-bold mb-0">{{ $trainings->whereIn('jenis_team', ['Evakuasi', 'Umum'])->count() }} Orang</h4>
                    </div>
                    <i class="fa-solid fa-person-running fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Monitoring Training ERP -->
    <div class="card-custom">
        <div class="card-body p-0 table-responsive">
            <table class="table table-custom align-middle mb-0" id="tableTrainingErp">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="min-width: 180px;">Nama Pekerja</th>
                        <th style="min-width: 150px;">Subcont / Departemen</th>
                        <th style="min-width: 150px;">Jenis Team ERP</th>
                        <th style="width: 110px;">Struktur</th>
                        <th>Kolom Pernyataan / Catatan Sertifikasi</th>
                        <th style="width: 90px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $index => $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->nama_pekerja }}</td>
                        <td>{{ $item->subcont_dept }}</td>
                        <td class="text-center">Team {{ $item->jenis_team }}</td>
                        <td class="text-center">{{ $item->struktur == 'Ya' ? 'Ya' : 'Tidak' }}</td>
                        <td class="text-center">
                            {{ str_contains(strtolower($item->pernyataan), 'belum') ? 'Belum Menandatangani' : 'Sudah Menandatangani' }}
                            @if($item->pernyataan && !in_array($item->pernyataan, ['Sudah Menandatangani', 'Belum Menandatangani']))
                                <div class="small text-muted mt-1">{{ $item->pernyataan }}</div>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalEditErp{{ $item->id }}">
                                Edit
                            </button>
                            <form action="{{ route('training-erp.destroy', $item->id) }}" method="POST" class="d-inline form-delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Erp -->
                    <div class="modal fade" id="modalEditErp{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('training-erp.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Data ERP: {{ $item->nama_pekerja }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nama Pekerja</label>
                                            <input type="text" name="nama_pekerja" value="{{ $item->nama_pekerja }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subcont / Departemen</label>
                                            <input type="text" name="subcont_dept" value="{{ $item->subcont_dept }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Jenis Team ERP</label>
                                            <select name="jenis_team" class="form-select form-select-sm" required>
                                                <option value="Kebakaran" {{ $item->jenis_team == 'Kebakaran' ? 'selected' : '' }}>Team Kebakaran</option>
                                                <option value="P3K" {{ $item->jenis_team == 'P3K' ? 'selected' : '' }}>Team P3K</option>
                                                <option value="Evakuasi" {{ $item->jenis_team == 'Evakuasi' ? 'selected' : '' }}>Team Evakuasi</option>
                                                <option value="Umum" {{ $item->jenis_team == 'Umum' ? 'selected' : '' }}>Team Umum</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Struktur Organisasi Tanggap Darurat</label>
                                            <select name="struktur" class="form-select form-select-sm" required>
                                                <option value="Ya" {{ $item->struktur == 'Ya' ? 'selected' : '' }}>Ya (Masuk Dalam Struktur Resmi)</option>
                                                <option value="Tidak" {{ $item->struktur == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status Kolom Pernyataan ERP</label>
                                            <select name="pernyataan" class="form-select form-select-sm" required>
                                                <option value="Sudah Menandatangani" {{ (!str_contains(strtolower($item->pernyataan), 'belum')) ? 'selected' : '' }}>Sudah Menandatangani</option>
                                                <option value="Belum Menandatangani" {{ str_contains(strtolower($item->pernyataan), 'belum') ? 'selected' : '' }}>Belum Menandatangani</option>
                                            </select>
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-user-slash fa-2x mb-2 d-block text-secondary"></i>
                            Belum ada data monitoring training ERP. Klik tombol "Tambah Peserta Training".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Participant Training ERP -->
<div class="modal fade" id="modalTambahErp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('training-erp.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-shield-cat me-2"></i> Tambah Peserta Training ERP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pekerja</label>
                        <input type="text" name="nama_pekerja" class="form-control form-control-sm" placeholder="Nama Lengkap Pekerja" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subcont / Departemen</label>
                        <input type="text" name="subcont_dept" class="form-control form-control-sm" placeholder="Misal: DKP, SUM, Produksi, Maintenance" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Team ERP (Bisa Difilter)</label>
                        <select name="jenis_team" class="form-select form-select-sm" required>
                            <option value="Kebakaran">Team Kebakaran</option>
                            <option value="P3K">Team P3K / Medis</option>
                            <option value="Evakuasi">Team Evakuasi</option>
                            <option value="Umum" selected>Team Umum</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kolom Struktur (Opsi Ya/Tidak)</label>
                        <select name="struktur" class="form-select form-select-sm" required>
                            <option value="Ya" selected>Ya (Masuk Dalam Struktur)</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kolom Pernyataan / Status Penandatanganan</label>
                        <select name="pernyataan" class="form-select form-select-sm" required>
                            <option value="Sudah Menandatangani" selected>Sudah Menandatangani</option>
                            <option value="Belum Menandatangani">Belum Menandatangani</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check me-1"></i> Simpan Data ERP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
