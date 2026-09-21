@extends('layouts.app')

@section('title', 'Cetak Daftar Hadir K3')
@section('page_heading', 'Cetak Form Daftar Hadir')
@section('page_subheading', 'Konfigurasi Jenis Kegiatan & Bagian yang Ingin Dicetak Presensinya')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h6 class="card-title-custom mb-0" style="color: #003366;">
                        Pengaturan Dokumen Daftar Hadir
                    </h6>
                    <span class="badge bg-primary px-3 py-1.5 font-monospace">
                        Total Pekerja Aktif: {{ $totalActiveWorkers }}
                    </span>
                </div>

                <form action="{{ route('absensi.print') }}" method="GET" target="_blank" class="p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            Jenis Kegiatan Presensi <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_kegiatan" class="form-select form-select-custom" required>
                            <option value="Safety Morning Talk (SMT)" selected>Safety Morning Talk (SMT)</option>
                            <option value="Tool Box Meeting (TBM)">Tool Box Meeting (TBM)</option>
                        </select>
                        <div class="form-text text-muted small">Pilih jenis agenda keselamatan yang akan dicetak di kop dokumen.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            Pilih Bagian / Subcont yang Ingin Dicetak <span class="text-danger">*</span>
                        </label>
                        <select name="subcont" class="form-select form-select-custom" required>
                            <option value="SEMUA" selected>-- Seluruh Departemen & Subcont (Semua Pekerja Aktif) --</option>
                            <optgroup label="Subkontraktor Mitra (SUB):">
                                @foreach($subconts as $sc)
                                    <option value="{{ $sc->code }}">{{ $sc->code }} - {{ $sc->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Departemen Internal Pabrikasi:">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <div class="form-text text-muted small">Data presensi otomatis diisi dengan seluruh personel aktif pada bagian tersebut tanpa perlu input manual.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control form-control-custom flatpickr-date" placeholder="DD/MM/YYYY" required>
                    </div>

                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="small text-muted">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i>
                            Setelah Anda menekan tombol di bawah, sistem akan membuka halaman cetak dokumen resmi yang telah terisi lengkap dengan nama-nama pekerja dan kolom tanda tangan siap cetak atau ekspor PDF.
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gradient-blue px-4 py-2.5 fw-bold">
                            <i class="fa-solid fa-print me-2"></i> Buka & Cetak Dokumen Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
