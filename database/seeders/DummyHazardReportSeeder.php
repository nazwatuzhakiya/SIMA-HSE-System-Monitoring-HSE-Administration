<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanBahaya;

class DummyHazardReportSeeder extends Seeder
{
    public function run()
    {
        $hazards = [
            [
                'ticket_code' => 'HZ-2026-001',
                'nama_pelapor' => 'Budi Santoso',
                'area_lokasi' => 'Area Fabrikasi Bay 3 Plant Kalijati',
                'deskripsi' => 'Kabel las terkelupas dan terendam genangan air saat hujan.',
                'tingkat_risiko' => 'Tinggi',
                'status' => 'Menunggu',
                'tindak_lanjut' => 'Perlu isolasi jaringan listrik & perbaikan kabel oleh tim MNT.'
            ],
            [
                'ticket_code' => 'HZ-2026-002',
                'nama_pelapor' => 'Ahmad Hidayat',
                'area_lokasi' => 'Gudang Material Baut & Anchor',
                'deskripsi' => 'Penataan pallet kayu tidak stabil dan melengkung rawan roboh.',
                'tingkat_risiko' => 'Sedang',
                'status' => 'Diproses',
                'tindak_lanjut' => 'Tim Logistik sedang menata ulang penumpukan barang pada pallet standar.'
            ],
            [
                'ticket_code' => 'HZ-2026-003',
                'nama_pelapor' => 'Eko Prasetyo',
                'area_lokasi' => 'Area Blasting & Painting Shop',
                'deskripsi' => 'Exhaust fan ventilasi udara mati dan berbau tiner menyengat.',
                'tingkat_risiko' => 'Tinggi',
                'status' => 'Selesai',
                'tindak_lanjut' => 'Motor exhaust fan diganti baru oleh Maintenance dan tes aliran udara lancar.'
            ],
            [
                'ticket_code' => 'HZ-2026-004',
                'nama_pelapor' => 'Dedi Wijaya',
                'area_lokasi' => 'Jalur Grating Crane Overhead Bay 1',
                'deskripsi' => 'Pengunci pengaman hook crane overhead longgar.',
                'tingkat_risiko' => 'Tinggi',
                'status' => 'Diproses',
                'tindak_lanjut' => 'Pemasangan latch safety hook baru sedang dikerjakan mekanik.'
            ],
            [
                'ticket_code' => 'HZ-2026-005',
                'nama_pelapor' => 'Lukman Hakim',
                'area_lokasi' => 'Area Workshop Subcont DKP',
                'deskripsi' => 'Tabung tabung gas LPG dan Oksigen tidak dirantai.',
                'tingkat_risiko' => 'Sedang',
                'status' => 'Selesai',
                'tindak_lanjut' => 'Rantai pengaman tabung gas bertekanan dipasang dan diberi penandaan status.'
            ],
            [
                'ticket_code' => 'HZ-2026-006',
                'nama_pelapor' => 'Fajar Nugroho',
                'area_lokasi' => 'Area Scaffolding Rangka Atas Gudang 9',
                'deskripsi' => 'Papan platform kerja tidak diikat kawat secara kuat.',
                'tingkat_risiko' => 'Tinggi',
                'status' => 'Menunggu',
                'tindak_lanjut' => 'Inspeksi tim Safety & penataan ulang platform kerja scaffolding.'
            ],
            [
                'ticket_code' => 'HZ-2026-007',
                'nama_pelapor' => 'Joko Suryadi',
                'area_lokasi' => 'Akses Jalan Utama Fabrikasi Subang',
                'deskripsi' => 'Ceceran oli bekas dari mesin hidrolik bending.',
                'tingkat_risiko' => 'Rendah',
                'status' => 'Selesai',
                'tindak_lanjut' => 'Pembersihan oli menggunakan absorbant sawdust & penambalan selang hidrolik.'
            ]
        ];

        foreach ($hazards as $h) {
            LaporanBahaya::updateOrCreate(['ticket_code' => $h['ticket_code']], $h);
        }
    }
}
