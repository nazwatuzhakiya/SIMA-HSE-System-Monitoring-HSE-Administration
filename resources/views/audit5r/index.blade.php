@extends('layouts.app')

@section('title', 'Penilaian Audit 5R Departemen')
@section('page_heading', 'Penilaian Audit 5R Departemen')

@section('content')
<div class="container-fluid px-0">

    <!-- Info Rules & Ketentuan Pengumpulan 5R -->
    <div class="alert alert-warning border-0 shadow-sm mb-4 p-3" style="border-radius: 8px;">
        <div class="d-flex align-items-start gap-3">
            <i class="fa-solid fa-trophy fa-2x text-warning me-1 mt-1"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-info text-primary me-1"></i> KETENTUAN PENGUMPULAN & PENGHARGAAN AUDIT 5R:</h6>
                <ul class="mb-0 small text-dark ps-3" style="line-height: 1.5;">
                    <li><strong>Batas Pengumpulan:</strong> Form Penilaian 5R harus dikumpulkan oleh Pemimpin Unit Kerja per Departemen <strong>sebelum tanggal 25</strong> di setiap bulannya untuk direkap oleh HSE Committee.</li>
                    <li><strong>Penentuan Pemenang:</strong> Departemen dengan nilai tertinggi akan ditentukan & disahkan oleh <strong>HSE Committee</strong>.</li>
                    <li><strong>Pengumuman & Hadiah:</strong> Pengumuman pemenang dan pemberian apresiasi/hadiah dilaksanakan pada <strong>kegiatan SMT Minggu ke-1 di setiap bulannya</strong>.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filter & Export Controls -->
    <div class="card-custom mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <form action="{{ route('audit5r.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Departemen:</label>
                        <input type="text" name="departemen" value="{{ request('departemen') }}" class="form-control form-control-sm" placeholder="Nama Departemen" style="width: 160px;" onblur="this.form.submit()">
                    </div>
                    <div>
                        <label class="form-label mb-0 fw-bold" style="font-size: 11px;">Bulan / Tahun:</label>
                        <input type="month" name="bulan" value="{{ request('bulan', date('Y-m')) }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>
                    @if(request()->anyFilled(['departemen', 'bulan']))
                        <a href="{{ route('audit5r.index') }}" class="btn btn-outline-secondary btn-sm mt-3" style="font-size: 11px;">Reset Filter</a>
                    @endif
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-gradient-blue btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahAudit5R">
                        <i class="fa-solid fa-plus-circle me-1"></i> Catat Penilaian 5R Baru
                    </button>
                    <button type="button" class="btn btn-emerald-excel btn-sm" onclick="exportTableToExcel('table-5r-export', 'Penilaian_5R_Murinda')">
                        <i class="fa-solid fa-file-excel me-1"></i> Ekspor Format Dokumen Excel
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Cetak PDF / Form Resmi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FORMAT CETAK DOKUMEN PENILAIAN 5R RESMI (10 KRITERIA & PENILAIAN MINGGUAN) -->
    <div class="card-custom p-4" id="table-5r-export">
        
        <!-- Header Dokumen Cetak 5R -->
        <div class="border p-3 rounded mb-4 bg-light">
            <div class="row g-2" style="font-size: 12px;">
                <div class="col-md-4">
                    <div class="text-muted fw-bold">NAMA DEPARTEMEN YANG DINILAI:</div>
                    <div class="fw-bold fs-6 text-primary">{{ request('departemen') ?: 'PRODUKSI / FABRIKASI' }}</div>
                </div>
                <div class="col-md-4 text-md-center">
                    <div class="text-muted fw-bold">PERIODE BULAN & TAHUN:</div>
                    <div class="fw-bold fs-6 text-navy">{{ date('F Y', strtotime(request('bulan', date('Y-m')))) }}</div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="text-muted fw-bold">NAMA PENILAI (LEADER/PETUGAS):</div>
                    <div class="fw-bold fs-6 text-dark">{{ session('user_name', 'HSE Committee Leader') }}</div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-navy mb-2" style="font-size: 13px;">
            <i class="fa-solid fa-list-check text-primary me-2"></i> ACUAN 10 KRITERIA PENILAIAN 5R & SKOR MINGGUAN (SKALA 1 - 10):
        </h6>

        <!-- Tabel Matrix Penilaian 10 Kriteria x 4 Minggu -->
        <div class="table-responsive mb-4">
            <table class="table table-custom align-middle mb-0" style="font-size: 12px;">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="min-width: 250px;">Acuan Kriteria Penilaian 5R</th>
                        <th style="width: 70px;">Poin</th>
                        <th style="width: 90px;">Minggu 1<br><small>(Skala 1-10)</small></th>
                        <th style="width: 90px;">Minggu 2<br><small>(Skala 1-10)</small></th>
                        <th style="width: 90px;">Minggu 3<br><small>(Skala 1-10)</small></th>
                        <th style="width: 90px;">Minggu 4<br><small>(Skala 1-10)</small></th>
                        <th style="width: 100px;">Rata-Rata</th>
                        <th>Keterangan / Catatan Inspeksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $kriteria = [
                            ['no' => 1, 'nama' => 'Kerapihan bantalan material', 'poin' => '1a'],
                            ['no' => 2, 'nama' => 'Kerapihan pemasangan arde las', 'poin' => '1b'],
                            ['no' => 3, 'nama' => 'Penempatan mesin trafo las', 'poin' => '2a'],
                            ['no' => 4, 'nama' => 'Penempatan jalur kabel dan selang', 'poin' => '2b'],
                            ['no' => 5, 'nama' => 'Ketiadaan lemari atau loker liar di area kerja', 'poin' => '3a'],
                            ['no' => 6, 'nama' => 'Penataan dan penempatan material, komponen, atau profil', 'poin' => '3b'],
                            ['no' => 7, 'nama' => 'Kebersihan area kerja', 'poin' => '4'],
                            ['no' => 8, 'nama' => 'Penempatan barang pribadi di area kerja', 'poin' => '5'],
                            ['no' => 9, 'nama' => 'Pengelolaan sampah sesuai jenisnya', 'poin' => '8'],
                            ['no' => 10, 'nama' => 'Tingkat kehadiran TBM', 'poin' => '9'],
                        ];
                    @endphp

                    @foreach($kriteria as $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $item['no'] }}</td>
                        <td class="fw-bold text-dark">{{ $item['nama'] }}</td>
                        <td class="text-center">{{ $item['poin'] }}</td>
                        <td class="text-center fw-bold">8</td>
                        <td class="text-center fw-bold">9</td>
                        <td class="text-center fw-bold">8</td>
                        <td class="text-center fw-bold">9</td>
                        <td class="text-center fw-bold text-dark">8.5 / 10</td>
                        <td class="small text-muted">Bantalan material & arde tertata rapi, sampah terpilah dengan baik.</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-uppercase">Total Persentase Nilai Akhir:</td>
                        <td class="text-center text-dark">85%</td>
                        <td class="text-center text-dark">88%</td>
                        <td class="text-center text-dark">84%</td>
                        <td class="text-center text-dark">89%</td>
                        <td class="text-center text-dark">86.5%</td>
                        <td class="text-center text-dark">STATUS: MEMENUHI STANDAR (KANDIDAT JUARA)</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section Rekap Hasil Inspeksi Tersimpan -->
        <h6 class="fw-bold text-navy mb-2" style="font-size: 13px;">
            <i class="fa-solid fa-history me-1 text-primary"></i> RIWAYAT LOG AUDIT 5R TERSIMPAN SISTEM
        </h6>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead class="align-middle">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Departemen</th>
                        <th>Auditor</th>
                        <th>Ringkas</th>
                        <th>Rapi</th>
                        <th>Resik</th>
                        <th>Rawat</th>
                        <th>Rajin</th>
                        <th>Total Persentase (%)</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $idx => $a)
                        <tr>
                            <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($a->tanggal)) }}</td>
                            <td class="fw-bold text-dark">{{ $a->departemen }}</td>
                            <td>{{ $a->auditor_name }}</td>
                            <td class="text-center fw-bold">{{ $a->score_ringkas }}</td>
                            <td class="text-center fw-bold">{{ $a->score_rapi }}</td>
                            <td class="text-center fw-bold">{{ $a->score_resik }}</td>
                            <td class="text-center fw-bold">{{ $a->score_rawat }}</td>
                            <td class="text-center fw-bold">{{ $a->score_rajin }}</td>
                            <td class="text-center font-monospace">
                                {{ number_format($a->total_percentage, 1) }}%
                            </td>
                            <td class="small text-muted">{{ $a->catatan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">Belum ada riwayat audit 5R tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Form Penilaian Audit 5R Pop-up -->
<div class="modal fade" id="modalTambahAudit5R" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #004080, #001f3f); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-star-half-stroke text-warning me-2"></i> Form Input Penilaian Audit 5R Departemen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('audit5r.store') }}" method="POST" id="form-audit-5r">
                @csrf
                <div class="modal-body p-4">
                    <!-- Data Inspeksi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Auditor / Inspector <span class="text-danger">*</span></label>
                            <input type="text" name="auditor_name" class="form-control form-control-custom" placeholder="Contoh: Joko Widodo (HSE Team)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen yang Diinspeksi <span class="text-danger">*</span></label>
                            <select name="departemen" class="form-select form-select-custom" required>
                                <option value="">-- Pilih Departemen / Subcont --</option>
                                @php
                                    $deptList = ['Produksi', 'MSB', 'Built Up 9', 'Cutting Drilling', 'PPC', 'SD', 'QC', 'MNT', 'Warehouse', 'HR/GA', 'PJC', 'EXP', 'DKP', 'BM', 'SB', 'SUM', 'AGS', 'PKN', 'CM', 'NC', 'ENT'];
                                @endphp
                                @foreach($deptList as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tanggal Inspeksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Input 5 Kriteria (1-10) -->
                    <h6 class="fw-bold text-navy mb-2"><i class="fa-solid fa-list-check me-1 text-primary"></i> Score 5 Kriteria 5R (Skala 1 - 10):</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">1. Ringkas (Seiri)</label>
                            <input type="number" name="score_ringkas" id="score_ringkas" class="form-control form-control-custom text-center fw-bold" min="1" max="10" value="8" oninput="updateScore()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">2. Rapi (Seiton)</label>
                            <input type="number" name="score_rapi" id="score_rapi" class="form-control form-control-custom text-center fw-bold" min="1" max="10" value="8" oninput="updateScore()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">3. Resik (Seiso)</label>
                            <input type="number" name="score_resik" id="score_resik" class="form-control form-control-custom text-center fw-bold" min="1" max="10" value="8" oninput="updateScore()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">4. Rawat (Seiketsu)</label>
                            <input type="number" name="score_rawat" id="score_rawat" class="form-control form-control-custom text-center fw-bold" min="1" max="10" value="8" oninput="updateScore()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">5. Rajin (Shitsuke)</label>
                            <input type="number" name="score_rajin" id="score_rajin" class="form-control form-control-custom text-center fw-bold" min="1" max="10" value="8" oninput="updateScore()" required>
                        </div>
                    </div>

                    <!-- Total Score Display -->
                    <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-dark">Estimasi Skor Total Audit:</span>
                        <span class="badge bg-success fs-6 px-3 py-2" id="total-score-badge">80.0% (40/50 Poin)</span>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Catatan / Temuan Lapangan</label>
                        <textarea name="catatan" class="form-control form-control-custom" rows="3" placeholder="Tuliskan temuan atau rekomendasi perbaikan 5R..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-blue btn-sm px-4 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penilaian Audit 5R
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateScore() {
        const r1 = parseInt(document.getElementById('score_ringkas').value) || 0;
        const r2 = parseInt(document.getElementById('score_rapi').value) || 0;
        const r3 = parseInt(document.getElementById('score_resik').value) || 0;
        const r4 = parseInt(document.getElementById('score_rawat').value) || 0;
        const r5 = parseInt(document.getElementById('score_rajin').value) || 0;

        const clamp = (v) => Math.min(10, Math.max(1, v || 1));
        const sum = clamp(r1) + clamp(r2) + clamp(r3) + clamp(r4) + clamp(r5);
        const pct = (sum / 50) * 100;

        const badge = document.getElementById('total-score-badge');
        if (badge) {
            badge.innerText = pct.toFixed(1) + '% (' + sum + '/50 Poin)';
            if (pct >= 80) badge.className = 'badge bg-success fs-6 px-3 py-2';
            else if (pct >= 60) badge.className = 'badge bg-warning text-dark fs-6 px-3 py-2';
            else badge.className = 'badge bg-danger fs-6 px-3 py-2';
        }
    }
</script>
@endpush
@endsection
