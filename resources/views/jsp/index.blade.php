@extends('layouts.app')

@section('title', 'Form Join Safety Patrol (JSP)')
@section('page_heading', 'Form Join Safety Patrol (JSP)')

@section('content')
<div class="container-fluid px-0">

    <!-- Action & Filter Bar -->
    <div class="card-custom">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                
                <form action="{{ route('jsp.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Tanggal Patroli:</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm" style="width: 150px;" onchange="this.form.submit()">
                    </div>
                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Cari Area Kerja:</label>
                        <input type="text" name="area" value="{{ request('area') }}" class="form-control form-control-sm" placeholder="Misal: Fabrikasi, Painting" onblur="this.form.submit()">
                    </div>
                    @if(request()->anyFilled(['tanggal', 'area']))
                        <a href="{{ route('jsp.index') }}" class="btn btn-outline-secondary btn-sm mt-3" style="font-size: 11px;">Reset Filter</a>
                    @endif
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-gradient-blue btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJsp">
                        <i class="fa-solid fa-plus me-1"></i> Buat Form Patroli JSP Baru
                    </button>
                    <button type="button" class="btn btn-emerald-excel btn-sm" onclick="exportTableToExcel('tableJsp', 'Join_Safety_Patrol_Murinda')">
                        <i class="fa-solid fa-file-excel me-1"></i> Ekspor Excel
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Cetak Dokumen
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- 6 Kategori Kriteria Penilaian JSP -->
    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body p-3">
            <h6 class="fw-bold text-navy mb-2" style="font-size: 13px;"><i class="fa-solid fa-clipboard-check me-2 text-primary"></i> 6 KATEGORI ACUAN PENILAIAN JOIN SAFETY PATROL (JSP):</h6>
            <div class="row g-2 text-dark" style="font-size: 11px;">
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 1. Kondisi umum area kerja</div>
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 2. Peralatan kerja dan mesin</div>
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 3. Penataan material</div>
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 4. Instalasi listrik dan kabel</div>
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 5. Sarana keadaan darurat</div>
                <div class="col-md-4"><i class="fa-solid fa-check-circle text-success me-1"></i> 6. Alat angkat dan alat bantu angkat</div>
            </div>
        </div>
    </div>

    <!-- Tabel Data JSP -->
    <div class="card-custom">
        <div class="card-body p-0 table-responsive">
            <table class="table table-custom align-middle mb-0" id="tableJsp">
                <thead class="align-middle">
                    <tr>
                        <th rowspan="2" style="width: 40px;">No</th>
                        <th rowspan="2" style="width: 110px;">Tanggal</th>
                        <th rowspan="2" style="min-width: 140px;">Area Patroli</th>
                        <th rowspan="2" style="min-width: 150px;">Tim Patroli</th>
                        <th colspan="6" class="text-uppercase">Status 6 Kategori Penilaian Patroli (OK / Tidak OK & Keterangan)</th>
                        <th rowspan="2" style="width: 100px;">Status</th>
                        <th rowspan="2" style="width: 80px;" class="text-center">Aksi</th>
                    </tr>
                    <tr style="font-size: 10px;">
                        <th>1. Kondisi Umum</th>
                        <th>2. Peralatan/Mesin</th>
                        <th>3. Penataan Material</th>
                        <th>4. Listrik & Kabel</th>
                        <th>5. Sarana Darurat</th>
                        <th>6. Alat Angkat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patrols as $index => $jsp)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td class="text-center fw-bold">{{ date('d/m/Y', strtotime($jsp->tanggal)) }}</td>
                        <td class="fw-bold text-navy"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $jsp->area_patrol }}</td>
                        <td style="font-size: 11px;">{{ $jsp->tim_patroli }}</td>
                        
                        <!-- 6 Kategori Status & Keterangan -->
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat1_kondisi_umum == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat1_kondisi_umum }}</span>
                            @if($jsp->catatan1)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan1 }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat2_peralatan_mesin == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat2_peralatan_mesin }}</span>
                            @if($jsp->catatan2)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan2 }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat3_penataan_material == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat3_penataan_material }}</span>
                            @if($jsp->catatan3)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan3 }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat4_instalasi_listrik == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat4_instalasi_listrik }}</span>
                            @if($jsp->catatan4)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan4 }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat5_sarana_darurat == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat5_sarana_darurat }}</span>
                            @if($jsp->catatan5)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan5 }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jsp->kat6_alat_angkat == 'OK' ? 'bg-success' : 'bg-danger' }}">{{ $jsp->kat6_alat_angkat }}</span>
                            @if($jsp->catatan6)<div class="text-muted text-start" style="font-size: 10px;">{{ $jsp->catatan6 }}</div>@endif
                        </td>

                        <td class="text-center">
                            @if($jsp->status == 'Selesai')
                                <span class="badge bg-success-subtle text-success border border-success px-2">Selesai</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning px-2">Follow-up</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalEditJsp{{ $jsp->id }}">
                                Edit
                            </button>
                            <form action="{{ route('jsp.destroy', $jsp->id) }}" method="POST" class="d-inline form-delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 11px; border-radius: 6px;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit JSP -->
                    <div class="modal fade" id="modalEditJsp{{ $jsp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('jsp.update', $jsp->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Patroli JSP {{ $jsp->area_patrol }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Tanggal Patroli</label>
                                                <input type="date" name="tanggal" value="{{ $jsp->tanggal }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Area Patroli</label>
                                                <input type="text" name="area_patrol" value="{{ $jsp->area_patrol }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Tim Patroli</label>
                                                <input type="text" name="tim_patroli" value="{{ $jsp->tim_patroli }}" class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-list-check me-1"></i> Penilaian 6 Kategori (OK / Tidak OK & Keterangan)</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">1. Kondisi umum area kerja</label>
                                                <select name="kat1_kondisi_umum" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat1_kondisi_umum == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat1_kondisi_umum == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan1" value="{{ $jsp->catatan1 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">2. Peralatan kerja dan mesin</label>
                                                <select name="kat2_peralatan_mesin" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat2_peralatan_mesin == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat2_peralatan_mesin == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan2" value="{{ $jsp->catatan2 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">3. Penataan material</label>
                                                <select name="kat3_penataan_material" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat3_penataan_material == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat3_penataan_material == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan3" value="{{ $jsp->catatan3 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">4. Instalasi listrik dan kabel</label>
                                                <select name="kat4_instalasi_listrik" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat4_instalasi_listrik == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat4_instalasi_listrik == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan4" value="{{ $jsp->catatan4 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">5. Sarana keadaan darurat</label>
                                                <select name="kat5_sarana_darurat" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat5_sarana_darurat == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat5_sarana_darurat == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan5" value="{{ $jsp->catatan5 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">6. Alat angkat dan alat bantu angkat</label>
                                                <select name="kat6_alat_angkat" class="form-select form-select-sm mb-1">
                                                    <option value="OK" {{ $jsp->kat6_alat_angkat == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ $jsp->kat6_alat_angkat == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                                <input type="text" name="catatan6" value="{{ $jsp->catatan6 }}" class="form-control form-control-sm" placeholder="Keterangan / Temuan">
                                            </div>
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
                        <td colspan="12" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-clipboard-question fa-2x mb-2 d-block text-secondary"></i>
                            Belum ada laporan Join Safety Patrol (JSP). Klik "Buat Form Patroli JSP Baru".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah JSP Baru -->
<div class="modal fade" id="modalTambahJsp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('jsp.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-user-shield me-2"></i> Form Input Join Safety Patrol (JSP) Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Patroli</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Area Patroli</label>
                            <input type="text" name="area_patrol" class="form-control form-control-sm" placeholder="Misal: Plant Fabrikasi B9" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tim Patroli</label>
                            <input type="text" name="tim_patroli" class="form-control form-control-sm" placeholder="Tim HSE & Reps Subcont" required>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-navy mb-3"><i class="fa-solid fa-clipboard-check me-1 text-primary"></i> Penilaian Status 6 Kategori (OK / Tidak OK & Kolom Keterangan)</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">1. Kondisi umum area kerja</label>
                            <select name="kat1_kondisi_umum" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan1" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">2. Peralatan kerja dan mesin</label>
                            <select name="kat2_peralatan_mesin" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan2" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">3. Penataan material</label>
                            <select name="kat3_penataan_material" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan3" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">4. Instalasi listrik dan kabel</label>
                            <select name="kat4_instalasi_listrik" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan4" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 4">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">5. Sarana keadaan darurat</label>
                            <select name="kat5_sarana_darurat" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan5" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">6. Alat angkat dan alat bantu angkat</label>
                            <select name="kat6_alat_angkat" class="form-select form-select-sm mb-1">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                            <input type="text" name="catatan6" class="form-control form-control-sm" placeholder="Kolom Keterangan / Catatan 6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check me-1"></i> Simpan Form JSP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
