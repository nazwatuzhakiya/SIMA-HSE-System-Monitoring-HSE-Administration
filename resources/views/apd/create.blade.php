@extends('layouts.app')

@section('title', 'Form Input Pengambilan APD Baru')
@section('page_heading', 'Form Input Pengambilan APD')
@section('page_subheading', 'Pencatatan Penyerahan Alat Pelindung Diri (APD) Tenaga Kerja PT Murinda Iron Steel')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="card-title-custom mb-0" style="color: #003366;">
                        <i class="fa-solid fa-cart-plus me-1 text-primary"></i> Formulir Baru Pengambilan APD
                    </h6>
                    <a href="{{ route('apd.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rekap APD
                    </a>
                </div>

                <form action="{{ route('apd.store') }}" method="POST" class="p-4">
                    @csrf

                    <!-- 1. Pilih Pekerja (Ketik Dulu Baru Ada Rekomendasi Nama Mirip) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            Pilih Pekerja Penerima APD <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="hidden" name="manpower_id" id="select_manpower_id" required>
                            
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text"
                                       id="create_worker_search"
                                       class="form-control form-control-custom border-start-0"
                                       placeholder="Ketik nama / NIP pekerja penerima APD di sini..."
                                       autocomplete="off"
                                       oninput="filterCreateWorkers(this.value)"
                                       onfocus="filterCreateWorkers(this.value)">
                                <button type="button" class="btn btn-outline-secondary" id="create_btn_clear_worker" style="display: none;" onclick="clearCreateWorker()">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            
                            <!-- Live Recommendation Dropdown -->
                            <div id="create_worker_dropdown"
                                 class="list-group position-absolute w-100 shadow-lg border rounded mt-1"
                                 style="max-height: 250px; overflow-y: auto; z-index: 1060; display: none; background: #ffffff;">
                            </div>
                        </div>

                        <div id="worker-selected-badge" class="mt-2 p-2.5 rounded bg-light border" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-success fa-lg"></i>
                                    <div>
                                        <div class="fw-bold text-dark" id="disp_nama">-</div>
                                        <small class="text-muted">NIP: <span class="fw-semibold text-dark" id="disp_nip">-</span> | Dept: <span class="badge bg-primary font-monospace" id="disp_dept">-</span> | Jabatan: <span class="fw-semibold text-dark" id="disp_jabatan">-</span></small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="editCreateWorkerSelection()">
                                    Ganti
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Tanggal Pengambilan & Keterangan -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">
                                Tanggal Pengambilan APD <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_pengambilan" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">
                                Keterangan / Alasan Pengambilan
                            </label>
                            <input type="text" name="keterangan" class="form-control form-control-custom" placeholder="Contoh: Penggantian Rutin / Rusak / Personel Baru">
                        </div>
                    </div>

                    <!-- 3. Checklist APD Yang Diberikan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            Centang Alat Pelindung Diri (APD) Yang Diberikan: <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3 p-3 bg-light rounded border">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Safety Helmet" id="create_apd1" checked>
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd1">
                                        <i class="fa-solid fa-hard-hat text-warning me-1"></i> Safety Helmet
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Sepatu Safety" id="create_apd2">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd2">
                                        <i class="fa-solid fa-shoe-prints text-secondary me-1"></i> Sepatu Safety
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Rompi Reflective K3" id="create_apd3" checked>
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd3">
                                        <i class="fa-solid fa-vest text-success me-1"></i> Rompi Reflective K3
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Kacamata Pelindung (Safety Glasses)" id="create_apd4">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd4">
                                        <i class="fa-solid fa-glasses text-info me-1"></i> Kacamata Pelindung
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Sarung Tangan Kerja" id="create_apd5">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd5">
                                        <i class="fa-solid fa-mitten text-primary me-1"></i> Sarung Tangan
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Body Harness Double Lanyard" id="create_apd6">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd6">
                                        <i class="fa-solid fa-person-falling text-danger me-1"></i> Body Harness
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Masker Debu & Kimia" id="create_apd7">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd7">
                                        <i class="fa-solid fa-head-side-mask text-secondary me-1"></i> Masker K3
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="jenis_apd[]" value="Ear Plug / Pelindung Telinga" id="create_apd8">
                                    <label class="form-check-label fw-semibold text-dark" for="create_apd8">
                                        <i class="fa-solid fa-ear-listen text-warning me-1"></i> Ear Plug / Ear Muff
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('apd.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-gradient-blue px-4 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-save me-1"></i> Simpan Transaksi Pengambilan APD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const createApdWorkers = [
        @foreach($allManpower as $mp)
        {
            id: {{ $mp->id }},
            nama: {!! json_encode($mp->nama) !!},
            nip: {!! json_encode($mp->no_id) !!},
            dept: {!! json_encode($mp->departemen) !!},
            jabatan: {!! json_encode($mp->jabatan ?: '-') !!}
        },
        @endforeach
    ];

    function filterCreateWorkers(query) {
        const dropdown = document.getElementById('create_worker_dropdown');
        const q = (query || '').toLowerCase().trim();

        let matches = createApdWorkers;
        if (q.length > 0) {
            matches = createApdWorkers.filter(w =>
                w.nama.toLowerCase().includes(q) ||
                w.nip.toLowerCase().includes(q) ||
                w.dept.toLowerCase().includes(q) ||
                w.jabatan.toLowerCase().includes(q)
            );
        }

        if (matches.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-muted text-center small"><i class="fa-solid fa-user-slash me-1"></i> Tidak ditemukan pekerja dengan nama/kata kunci "' + escapeCreateHtml(q) + '"</div>';
            dropdown.style.display = 'block';
            return;
        }

        const displayMatches = matches.slice(0, 25);
        let html = '';
        displayMatches.forEach(w => {
            html += `
                <a href="javascript:void(0)" class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center" onclick="selectCreateWorker(${w.id})">
                    <div>
                        <div class="fw-bold text-dark">${escapeCreateHtml(w.nama)}</div>
                        <small class="text-muted font-monospace" style="font-size: 11px;">
                            ${escapeCreateHtml(w.nip)} &bull; <span class="badge bg-light text-dark border">${escapeCreateHtml(w.dept)}</span> - ${escapeCreateHtml(w.jabatan)}
                        </small>
                    </div>
                    <span class="badge bg-primary text-white">Pilih</span>
                </a>
            `;
        });

        if (matches.length > 25) {
            html += `<div class="p-2 text-center text-muted small bg-light font-italic">Menampilkan 25 rekomendasi teratas dari ${matches.length} pekerja. Ketik lebih spesifik...</div>`;
        }

        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
    }

    function selectCreateWorker(id) {
        const worker = createApdWorkers.find(w => w.id === id);
        if (!worker) return;

        document.getElementById('select_manpower_id').value = worker.id;
        document.getElementById('create_worker_search').value = worker.nama;
        document.getElementById('disp_nama').innerText = worker.nama;
        document.getElementById('disp_nip').innerText = worker.nip;
        document.getElementById('disp_dept').innerText = worker.dept;
        document.getElementById('disp_jabatan').innerText = worker.jabatan;

        document.getElementById('create_worker_dropdown').style.display = 'none';
        document.getElementById('worker-selected-badge').style.display = 'block';
        document.getElementById('create_btn_clear_worker').style.display = 'inline-block';
    }

    function clearCreateWorker() {
        document.getElementById('select_manpower_id').value = '';
        document.getElementById('create_worker_search').value = '';
        document.getElementById('worker-selected-badge').style.display = 'none';
        document.getElementById('create_btn_clear_worker').style.display = 'none';
        document.getElementById('create_worker_dropdown').style.display = 'none';
    }

    function editCreateWorkerSelection() {
        document.getElementById('worker-selected-badge').style.display = 'none';
        const input = document.getElementById('create_worker_search');
        input.focus();
        filterCreateWorkers(input.value);
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('create_worker_dropdown');
        const searchInput = document.getElementById('create_worker_search');
        if (dropdown && searchInput && !dropdown.contains(e.target) && e.target !== searchInput) {
            dropdown.style.display = 'none';
        }
    });

    function escapeCreateHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
@endsection
