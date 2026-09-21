@props([
    'title' => 'DOKUMEN MANAJEMEN K3LH & HSE',
    'subhead' => 'PT MURINDA IRON STEEL - PLANT KALIJATI SUBANG',
    'docNo' => 'DOC-HSE-MIS-2026',
    'showMeta' => true
])

<!-- Official Header Document (PRINT ONLY - Hidden in Web View) -->
<div class="murinda-official-header d-none d-print-block mb-4 p-3 bg-white border rounded shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <!-- Company Name -->
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('gambar/logo-murinda.png') }}" alt="Logo PT Murinda Iron Steel" style="height: 50px; width: auto; object-fit: contain;">
            <div>
                <h5 class="fw-bold text-dark mb-0 style-brand-heading" style="letter-spacing: 0.5px; color: #004080;">PT MURINDA IRON STEEL</h5>
                <div class="fw-bold" style="font-size: 11px; text-transform: uppercase; color: #004080;">
                    PLANT KALIJATI SUBANG - DEPARTEMEN HSE & K3LH
                </div>
                <small class="text-muted" style="font-size: 11px;">
                    Jl. Raya Kalijati Km. 2.5, Desa Kalijati Timur, Subang, Jawa Barat
                </small>
            </div>
        </div>

        <!-- Dynamic Title Banner -->
        <div class="text-center text-md-end flex-grow-1 px-3">
            <h4 class="fw-bold text-dark mb-1 text-uppercase" style="font-size: 16px;">
                {{ $title }}
            </h4>
            <div class="fw-bold text-secondary" style="font-size: 11px;">
                {{ $subhead }}
            </div>
        </div>

        <!-- Document Meta / No -->
        @if($showMeta)
        <div class="text-end border-start ps-3" style="min-width: 170px;">
            <div class="text-muted" style="font-size: 10px; font-weight: 700;">NO. DOKUMEN</div>
            <div class="fw-bold text-dark" style="font-size: 12px;">{{ $docNo }}</div>
            <div class="text-muted" style="font-size: 10px;">TANGGAL: {{ date('d/m/Y') }}</div>
        </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .murinda-official-header {
            display: block !important;
            border: 2px solid #004080 !important;
            box-shadow: none !important;
            margin-bottom: 15px !important;
        }
    }
</style>
