@extends('layouts.app')

@section('title', 'Input Nilai Audit 5R - SIMA-HSE: System Monitoring & HSE Administration')

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-blue);">Form Input Nilai Audit 5R</h4>
            <p class="text-muted small m-0">Input Nilai Inspeksi Kebersihan & Kerapihan 5R per Departemen (Skala 1 - 10)</p>
        </div>
        <div>
            <a href="{{ route('audit5r.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rekapitulasi
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('audit5r.store') }}" method="POST" id="form-audit-5r">
            @csrf

            <!-- Info Card -->
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="fa-solid fa-clipboard-list text-primary"></i> Data Inspeksi
                    </h5>
                    <!-- Live Score -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-semibold">Total Score:</span>
                        <span class="badge bg-success px-3 py-2 fs-6" id="display-score-percent">80.0%</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Auditor / Inspector <span class="text-danger">*</span></label>
                            <input type="text" name="auditor_name" class="form-control form-control-custom"
                                   placeholder="Nama petugas yang melakukan audit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen yang Diinspeksi <span class="text-danger">*</span></label>
                            <select name="departemen" class="form-select form-select-custom" required>
                                <option value="">-- Pilih Departemen / Subcont --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tanggal Inspeksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nilai 5R -->
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="fa-solid fa-star-half-stroke text-warning"></i> Penilaian 5R (Input Nilai Angka 1 - 10)
                    </h5>
                    <span class="text-muted small">Ketik angka nilai 1 (sangat buruk) hingga 10 (sempurna)</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- 1. Ringkas -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #3b82f6 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0">
                                        <i class="fa-solid fa-box-open text-primary me-1"></i> 1. Ringkas (Seiri)
                                    </label>
                                    <span class="badge bg-primary fs-6 px-3" id="label-ringkas">8</span>
                                </div>
                                <div class="text-muted small mb-3">Memilah barang yang diperlukan dan tidak diperlukan di area kerja.</div>
                                <input type="number"
                                       name="score_ringkas"
                                       id="score_ringkas"
                                       class="form-control form-control-custom score-input fs-6 fw-bold text-center"
                                       min="1" max="10" value="8"
                                       onchange="updateScore()"
                                       oninput="updateScore()"
                                       required>
                            </div>
                        </div>

                        <!-- 2. Rapi -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #10b981 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0">
                                        <i class="fa-solid fa-layer-group text-success me-1"></i> 2. Rapi (Seiton)
                                    </label>
                                    <span class="badge bg-success fs-6 px-3" id="label-rapi">8</span>
                                </div>
                                <div class="text-muted small mb-3">Menata barang sesuai tempat yang ditentukan & siap dipakai.</div>
                                <input type="number"
                                       name="score_rapi"
                                       id="score_rapi"
                                       class="form-control form-control-custom score-input fs-6 fw-bold text-center"
                                       min="1" max="10" value="8"
                                       onchange="updateScore()"
                                       oninput="updateScore()"
                                       required>
                            </div>
                        </div>

                        <!-- 3. Resik -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #f59e0b !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0">
                                        <i class="fa-solid fa-sparkles text-warning me-1"></i> 3. Resik (Seiso)
                                    </label>
                                    <span class="badge bg-warning text-dark fs-6 px-3" id="label-resik">8</span>
                                </div>
                                <div class="text-muted small mb-3">Membersihkan tempat kerja dari debu, kotoran & sampah.</div>
                                <input type="number"
                                       name="score_resik"
                                       id="score_resik"
                                       class="form-control form-control-custom score-input fs-6 fw-bold text-center"
                                       min="1" max="10" value="8"
                                       onchange="updateScore()"
                                       oninput="updateScore()"
                                       required>
                            </div>
                        </div>

                        <!-- 4. Rawat -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #8b5cf6 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0">
                                        <i class="fa-solid fa-gears me-1" style="color:#8b5cf6;"></i> 4. Rawat (Seiketsu)
                                    </label>
                                    <span class="badge fs-6 px-3" style="background:#8b5cf6;" id="label-rawat">8</span>
                                </div>
                                <div class="text-muted small mb-3">Mempertahankan standar 3R awal secara konsisten & rutin.</div>
                                <input type="number"
                                       name="score_rawat"
                                       id="score_rawat"
                                       class="form-control form-control-custom score-input fs-6 fw-bold text-center"
                                       min="1" max="10" value="8"
                                       onchange="updateScore()"
                                       oninput="updateScore()"
                                       required>
                            </div>
                        </div>

                        <!-- 5. Rajin -->
                        <div class="col-md-12">
                            <div class="p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #ef4444 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0">
                                        <i class="fa-solid fa-heart text-danger me-1"></i> 5. Rajin (Shitsuke)
                                    </label>
                                    <span class="badge bg-danger fs-6 px-3" id="label-rajin">8</span>
                                </div>
                                <div class="text-muted small mb-3">Kedisiplinan diri, kesadaran K3, dan kepatuhan terhadap aturan keselamatan kerja.</div>
                                <input type="number"
                                       name="score_rajin"
                                       id="score_rajin"
                                       class="form-control form-control-custom score-input fs-6 fw-bold text-center"
                                       min="1" max="10" value="8"
                                       onchange="updateScore()"
                                       oninput="updateScore()"
                                       required>
                            </div>
                        </div>

                    </div>

                    <!-- Progress Bar Total -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">Total Score Audit 5R:</span>
                            <span class="fw-bold fs-5 text-success" id="total-score-text">80.0%</span>
                        </div>
                        <div class="progress" style="height: 14px; border-radius: 8px;">
                            <div class="progress-bar bg-success" id="progress-bar" role="progressbar" style="width: 80%; border-radius: 8px;"></div>
                        </div>
                        <div class="small text-muted mt-2">Nilai Total: <span id="raw-total">40</span>/50 poin</div>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="fa-solid fa-pen-to-square text-secondary"></i> Catatan Inspeksi Lapangan
                    </h5>
                </div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control form-control-custom" rows="4"
                               placeholder="Tuliskan temuan, catatan perbaikan, atau rekomendasi dari hasil inspeksi lapangan..."></textarea>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('audit5r.index') }}" class="btn btn-outline-secondary px-4 py-2">
                    <i class="fa-solid fa-times me-2"></i> Batal
                </a>
                <button type="submit" class="btn btn-gradient-blue flex-grow-1 py-2 fs-6">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Nilai Audit 5R
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateScore() {
        const r1 = parseInt(document.getElementById('score_ringkas').value) || 0;
        const r2 = parseInt(document.getElementById('score_rapi').value) || 0;
        const r3 = parseInt(document.getElementById('score_resik').value) || 0;
        const r4 = parseInt(document.getElementById('score_rawat').value) || 0;
        const r5 = parseInt(document.getElementById('score_rajin').value) || 0;

        // Clamp values 1 - 10
        const clamp = (v) => Math.min(10, Math.max(1, v || 1));
        const v1 = clamp(r1), v2 = clamp(r2), v3 = clamp(r3), v4 = clamp(r4), v5 = clamp(r5);

        document.getElementById('label-ringkas').innerText = v1;
        document.getElementById('label-rapi').innerText = v2;
        document.getElementById('label-resik').innerText = v3;
        document.getElementById('label-rawat').innerText = v4;
        document.getElementById('label-rajin').innerText = v5;

        const sum = v1 + v2 + v3 + v4 + v5;
        const pct = (sum / 50) * 100;
        const pctStr = pct.toFixed(1) + '%';

        document.getElementById('display-score-percent').innerText = pctStr;
        document.getElementById('total-score-text').innerText = pctStr;
        document.getElementById('raw-total').innerText = sum;
        document.getElementById('progress-bar').style.width = pct + '%';

        // Change bar color based on score
        const bar = document.getElementById('progress-bar');
        if (pct >= 80) {
            bar.className = 'progress-bar bg-success';
        } else if (pct >= 60) {
            bar.className = 'progress-bar bg-warning';
        } else {
            bar.className = 'progress-bar bg-danger';
        }
    }

    // Initialize on load
    updateScore();
</script>
@endpush
