<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LaporanBahaya;
use App\Models\SubcontPersonnel;
use App\Models\Manpower;
use App\Models\Absensi;
use App\Models\LogistikApd;
use App\Models\KlinikObat;
use App\Models\Audit5R;
use App\Models\InsidenK3;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Account
        User::updateOrCreate(
            ['email' => 'hse.kalijati@murinda.com'],
            [
                'name' => 'Petugas HSE Kalijati',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Laporan Bahaya Publik
        LaporanBahaya::create([
            'ticket_code' => 'HZ-2026-001',
            'nama_pelapor' => 'Budi Santoso (Visitor)',
            'area_lokasi' => 'Area Fabrikasi Bay 3',
            'deskripsi' => 'Kabel las terkelupas dekat genangan air di dekat mesin cutting CNC.',
            'tingkat_risiko' => 'Tinggi',
            'status' => 'Diproses',
            'tindak_lanjut' => 'Tim Maintenance dipanggil untuk isolasi kabel dan pengeringan area.'
        ]);

        LaporanBahaya::create([
            'ticket_code' => 'HZ-2026-002',
            'nama_pelapor' => 'Anonim',
            'area_lokasi' => 'Gudang Material Subang',
            'deskripsi' => 'Penataan pelat baja kurang stabil, ada potensi bergeser.',
            'tingkat_risiko' => 'Sedang',
            'status' => 'Menunggu',
            'tindak_lanjut' => 'Menunggu verifikasi HSE Inspector shift siang.'
        ]);

        LaporanBahaya::create([
            'ticket_code' => 'HZ-2026-003',
            'nama_pelapor' => 'Ahmad Risyad',
            'area_lokasi' => 'Area Blasting & Painting',
            'deskripsi' => 'Exhaust fan ruangan painting berbunyi bising berlebih dan perlu pembersihan filter.',
            'tingkat_risiko' => 'Rendah',
            'status' => 'Selesai',
            'tindak_lanjut' => 'Filter exhaust fan telah diganti oleh MNT pada 07/08/2026.'
        ]);

        // 3. Subcont Personnel (DKP, SUM, AGS, PKN, CM, NC, BM, SBL)
        $subconts = [
            ['nama' => 'Randi Suherman', 'company' => 'DKP', 'posisi' => 'Foreman', 'tgl' => '2026-01-10', 'dom' => 'Kalijati, Subang'],
            ['nama' => 'Deden Kurnia', 'company' => 'DKP', 'posisi' => 'Painter', 'tgl' => '2026-02-01', 'dom' => 'Dawuan, Subang'],
            ['nama' => 'Hendra Setiawan', 'company' => 'SUM', 'posisi' => 'Blaster', 'tgl' => '2026-01-15', 'dom' => 'Cipeundeuy, Subang'],
            ['nama' => 'Bambang Sukma', 'company' => 'AGS', 'posisi' => 'Painter', 'tgl' => '2026-01-18', 'dom' => 'Subang Kota'],
            ['nama' => 'Asep Saepulloh', 'company' => 'PKN', 'posisi' => 'Helper', 'tgl' => '2026-03-05', 'dom' => 'Purwakarta'],
            ['nama' => 'Bambang Tri', 'company' => 'CM', 'posisi' => 'Foreman', 'tgl' => '2026-01-20', 'dom' => 'Subang Kota'],
            ['nama' => 'Agus Gunawan', 'company' => 'NC', 'posisi' => 'Admin', 'tgl' => '2026-02-12', 'dom' => 'Kalijati, Subang'],
            ['nama' => 'Syarif Hidayat', 'company' => 'BM', 'posisi' => 'Helper', 'tgl' => '2026-03-10', 'dom' => 'Pagaden, Subang'],
            ['nama' => 'Lukman Hakim', 'company' => 'SBL', 'posisi' => 'Painter', 'tgl' => '2026-02-22', 'dom' => 'Subang Kota'],
        ];

        foreach ($subconts as $s) {
            $sc = SubcontPersonnel::create([
                'nama' => $s['nama'],
                'company_code' => $s['company'],
                'tgl_masuk' => $s['tgl'],
                'tgl_induction' => $s['tgl'],
                'posisi' => $s['posisi'],
                'status' => 'Aktif',
                'domisili_alamat' => $s['dom'],
                'domisili_desa' => 'Kalijati Timur',
                'domisili_kec' => 'Kalijati',
                'domisili_kota' => 'Subang',
                'keterangan' => 'Personel Subcont Terverifikasi Safety Induction'
            ]);

            // Sync with Manpower list so subcont data auto-appears in Manpower
            Manpower::updateOrCreate(
                ['no_id' => 'SUB-' . $s['company'] . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT)],
                [
                    'nama' => $s['nama'],
                    'tgl_induction' => $s['tgl'],
                    'departemen' => $s['company'],
                    'jabatan' => $s['posisi'] . ' (Subcont ' . $s['company'] . ')',
                    'status' => 'Aktif',
                    'apd_helmet_issued' => true
                ]
            );
        }

        // 4. Master List of Organic Manpower
        $sampleNames = [
            ['nama' => 'Joko Widodo', 'dept' => 'Produksi', 'jab' => 'Welder Senior'],
            ['nama' => 'Sutrisno', 'dept' => 'Built Up 9', 'jab' => 'Assembler'],
            ['nama' => 'Andi Wijaya', 'dept' => 'Cutting Drilling', 'jab' => 'Operator CNC'],
            ['nama' => 'Herman Prasetyo', 'dept' => 'QC', 'jab' => 'Inspector QC'],
            ['nama' => 'Agung Nugroho', 'dept' => 'Warehouse', 'jab' => 'Logistik Officer'],
            ['nama' => 'Budi Mulyana', 'dept' => 'MNT', 'jab' => 'Teknisi Elektrikal'],
            ['nama' => 'Dedi Dahlan', 'dept' => 'PPC', 'jab' => 'Planner PPC'],
            ['nama' => 'Eko Raharjo', 'dept' => 'HR/GA', 'jab' => 'Staff GA Plant'],
            ['nama' => 'Fajar Sidik', 'dept' => 'MSB', 'jab' => 'Operator Crane'],
            ['nama' => 'Gilang Ramadhan', 'dept' => 'Projek', 'jab' => 'Erector Subang'],
        ];

        $mpModels = [];
        $index = 101;
        foreach ($sampleNames as $m) {
            $mp = Manpower::create([
                'no_id' => 'MIS-KLJ-' . $index++,
                'nama' => $m['nama'],
                'tgl_induction' => '2026-07-26',
                'departemen' => $m['dept'],
                'jabatan' => $m['jab'],
                'status' => 'Aktif',
                'apd_helmet_issued' => true
            ]);
            $mpModels[] = $mp;
        }

        // 5. Absensi SMT / TBM (Fast Entry Defaults)
        $today = date('Y-m-d');
        foreach ($mpModels as $key => $mp) {
            $status = 'Hadir';
            if ($key == 2) $status = 'Izin';
            if ($key == 5) $status = 'Sakit';

            Absensi::create([
                'jenis_kegiatan' => 'SMT',
                'tanggal' => $today,
                'departemen_subcont' => $mp->departemen,
                'manpower_id' => $mp->id,
                'nama_personel' => $mp->nama,
                'status_kehadiran' => $status,
                'keterangan' => $status == 'Hadir' ? 'Hadir SMT Tepat Waktu' : 'Surat Terlampir'
            ]);
        }

        // 6. Logistik APD
        foreach ($mpModels as $key => $mp) {
            if ($key < 5) {
                LogistikApd::create([
                    'manpower_id' => $mp->id,
                    'nama_personel' => $mp->nama,
                    'departemen_subcont' => $mp->departemen,
                    'tanggal_pengambilan' => date('Y-m-d', strtotime('-' . ($key * 2) . ' days')),
                    'jenis_apd' => 'Safety Helmet, Kacamata Safety, Sepatu Safety',
                    'jumlah' => 3,
                    'keterangan' => 'Pengambilan APD Rutin Induction'
                ]);
            }
        }

        // 7. Klinik & Obat
        KlinikObat::create([
            'nama_pegawai' => 'Joko Widodo',
            'nip' => 'MIS-KLJ-101',
            'keluhan' => 'Pusing ringan setelah kerja',
            'nama_obat' => 'Paracetamol 500mg',
            'jumlah_obat' => '1 Strip (10 Tablet)',
            'tanggal_pengambilan' => $today
        ]);

        KlinikObat::create([
            'nama_pegawai' => 'Sutrisno',
            'nip' => 'MIS-KLJ-102',
            'keluhan' => 'Luka gores ringan di jari tangan',
            'nama_obat' => 'Betadine & Perban Steril',
            'jumlah_obat' => '1 Botol Kecil & 2 Lembar',
            'tanggal_pengambilan' => $today
        ]);

        // 8. Audit 5R
        Audit5R::create([
            'auditor_name' => 'HSE Inspector',
            'departemen' => 'Produksi',
            'tanggal' => $today,
            'score_ringkas' => 9,
            'score_rapi' => 8,
            'score_resik' => 9,
            'score_rawat' => 8,
            'score_rajin' => 9,
            'total_percentage' => 86.00,
            'catatan' => 'Area jalur hijau bersih, tempat alat teratur rapi.'
        ]);

        Audit5R::create([
            'auditor_name' => 'HSE Inspector',
            'departemen' => 'Warehouse',
            'tanggal' => date('Y-m-d', strtotime('-3 days')),
            'score_ringkas' => 8,
            'score_rapi' => 7,
            'score_resik' => 8,
            'score_rawat' => 8,
            'score_rajin' => 8,
            'total_percentage' => 78.00,
            'catatan' => 'Penataan pallet perlu ditingkatkan di zona B.'
        ]);

        // 9. Insiden K3
        InsidenK3::create([
            'kode_insiden' => 'INC-2026-001',
            'tanggal' => date('Y-m-d', strtotime('-5 days')),
            'lokasi' => 'Area Built Up 9',
            'deskripsi_kejadian' => 'Near-Miss: Material sling crane tersangkut baut pelat saat lifting.',
            'kategori' => 'Near-Miss',
            'nama_korban' => null, // Opsional (tidak ada korban)
            'nama_saksi' => 'Sutrisno',
            'pj_departemen' => 'Fajar Sidik (Built Up 9)',
            'tindak_lanjut' => 'Toolbox Meeting darurat dilakukan untuk pengawasan lifting supervisor.',
            'status' => 'Closed'
        ]);
    }
}
