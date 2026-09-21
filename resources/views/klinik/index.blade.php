@extends('layouts.app')

@section('title', 'Modul Klinik & Pengambilan Obat')
@section('page_heading', 'Modul Klinik & Pengambilan Obat')
@section('page_subheading', 'Pencatatan Keluhan Kesehatan Pekerja & Log Penyerahan Obat Klinik Plant')

@section('content')
<!-- Top Toolbar Action Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-gradient-blue btn-sm px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalInputObat">
            <i class="fa-solid fa-plus-circle me-1"></i> Input Pengambilan Obat
        </button>
        <a href="#tabel-riwayat-klinik" class="btn btn-outline-primary btn-sm px-3 py-2 fw-bold">
            <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat Log Obat
        </a>
    </div>
    <div>
        <button onclick="exportTableToExcel('table-klinik', 'Data_Pengambilan_Obat_Klinik_Murinda')" class="btn btn-emerald-excel btn-sm px-3 py-2 fw-bold">
            <i class="fa-solid fa-file-excel me-1"></i> Export Excel (.xlsx)
        </button>
    </div>
</div>

<!-- Modal Form Input Pengambilan Obat -->
<div class="modal fade" id="modalInputObat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #004080, #001f3f); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-prescription-bottle-medical text-danger me-2"></i> Form Input Pengambilan Obat Klinik
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('klinik.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pegawai / NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" class="form-control form-control-custom" placeholder="Contoh: Joko Widodo (NIP101)" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Keluhan / Sakit Apa <span class="text-danger">*</span></label>
                            <input type="text" name="keluhan" class="form-control form-control-custom" placeholder="Contoh: Pusing, Flu, Luka Ringan, Asam Lambung" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Obat Yang Diambil <span class="text-danger">*</span></label>
                            <input type="text" name="nama_obat" class="form-control form-control-custom" placeholder="Contoh: Paracetamol, Antasida Doen" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Obat <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah_obat" class="form-control form-control-custom" placeholder="Contoh: 1 Strip / 2 Tablet" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Pengambilan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pengambilan" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-blue btn-sm px-4 fw-bold">
                        <i class="fa-solid fa-plus-circle me-1"></i> Simpan Catatan Obat Klinik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabel Rekapitulasi Pengambilan Obat (Full-Width) -->
<div class="card-custom" id="tabel-riwayat-klinik">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="card-title-custom m-0">
            <i class="fa-solid fa-list-check text-primary me-2"></i> Tabel Riwayat Rekapitulasi Pengambilan Obat
        </h5>
        <span class="badge bg-primary text-white px-3 py-1">Total Records: {{ $records->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0" id="table-klinik">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama Pegawai / NIP</th>
                        <th>Keluhan</th>
                        <th>Obat Yang Diambil</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $idx => $r)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ date('d/m/Y', strtotime($r->tanggal_pengambilan)) }}</td>
                            <td class="fw-bold text-dark">{{ $r->nama_pegawai }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $r->keluhan }}</span></td>
                            <td class="fw-semibold text-primary">{{ $r->nama_obat }}</td>
                            <td><span class="badge bg-secondary">{{ $r->jumlah_obat }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada rekapitulasi penyerahan obat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
