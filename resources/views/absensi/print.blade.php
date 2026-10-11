<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir {{ $jenisKegiatan }} Subcont - PT Murinda Iron Steel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; font-size: 12px; color: #000; padding: 25px; }
        .header-official-murinda { 
            background-color: #003366 !important; 
            color: #ffffff !important; 
            border: 2px solid #002244; 
            border-radius: 6px; 
            padding: 16px 20px; 
            margin-bottom: 20px; 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact; 
        }
        .header-official-murinda h5 { color: #ffffff !important; }
        .subcont-badge-header { 
            background-color: #f59e0b; 
            color: #001f3f; 
            padding: 5px 14px; 
            font-size: 13px; 
            font-weight: 800; 
            border-radius: 4px; 
            display: inline-block; 
        }
        .table-print { border: 1px solid #000; width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-print th, .table-print td { border: 1px solid #000; padding: 10px 14px; text-align: left; vertical-align: middle; }
        .table-print th { background-color: #f1f5f9; font-weight: bold; text-align: center; font-size: 12px; text-transform: uppercase; }
        .table-print tbody tr:nth-child(even) td { background-color: #f0f7ff; }
        .table-print tbody tr:nth-child(odd) td { background-color: #ffffff; }
        .ttd-cell { height: 45px; width: 280px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .table-print tbody tr:nth-child(even) td { 
                background-color: #f0f7ff !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .header-official-murinda { 
                background-color: #003366 !important; 
                color: #ffffff !important; 
                border: 2px solid #002244 !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .header-official-murinda * {
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
        }
    </style>
</head>
<body>

    <!-- Printable Action Toolbar -->
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
            <i class="fa-solid fa-print me-1"></i> Cetak / Export PDF Daftar Hadir
        </button>
    </div>

    <!-- Official Murinda Header Document (Biru Navy Serasi Logo Putih) -->
    <div class="header-official-murinda shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('gambar/logo-murinda.png') }}" alt="Logo PT Murinda Iron Steel" style="height: 48px; width: auto; object-fit: contain;">
                <div>
                    <h5 class="m-0 fw-bold text-uppercase" style="letter-spacing: 0.5px;">PT MURINDA IRON STEEL</h5>
                    <div class="fw-bold" style="font-size: 11px; opacity: 0.9;">PLANT KALIJATI SUBANG — DEPARTEMEN HSE</div>
                    <div style="font-size: 10px; opacity: 0.75;">Jl. Raya Kalijati Km. 2.5, Subang, Jawa Barat 41252</div>
                </div>
            </div>

            <div class="text-end">
                <h5 class="m-0 fw-bold text-uppercase">DAFTAR HADIR {{ $jenisKegiatan }}</h5>
                <div class="subcont-badge-header mt-1">
                    Dept / Sub: {{ $subcontName }}
                </div>
                <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;">
                    Tanggal Kegiatan: <strong style="color: #ffffff;">{{ date('d F Y', strtotime($tanggal)) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Format: No, Nama, Jabatan, Tanda Tangan -->
    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 50px;">NO</th>
                <th style="min-width: 250px;">NAMA PEKERJA</th>
                <th style="min-width: 200px;">JABATAN</th>
                <th class="ttd-cell">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeWorkers as $idx => $worker)
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td class="fw-bold text-dark">{{ $worker->nama }}</td>
                    <td>
                        {{ $worker->jabatan }}
                        @if($subcontCode === 'SEMUA' && !empty($worker->departemen))
                            <span class="badge bg-light text-dark border ms-1 font-monospace">{{ $worker->departemen }}</span>
                        @endif
                    </td>
                    <td class="ttd-cell">
                        <div class="d-flex justify-content-between px-2 text-muted" style="font-size: 10px;">
                            <span>{{ $idx + 1 }}.</span>
                            @if(($idx + 1) % 2 == 0)
                                <span class="ms-auto">........................</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data pekerja aktif untuk Subcont / Departemen {{ $subcontCode }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
