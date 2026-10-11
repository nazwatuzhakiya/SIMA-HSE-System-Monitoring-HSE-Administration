<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubcontPersonnel;
use App\Models\SubcontCompany;
use App\Models\Manpower;
use App\Models\LogistikApd;
use App\Services\HseDepartmentService;
use Carbon\Carbon;

class HseDummyDataSeeder extends Seeder
{
    public function run()
    {
        // 0. Bersihkan data dummy lama agar rapi dan tidak duplikat
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Absensi::truncate();
        LogistikApd::truncate();
        Manpower::truncate();
        SubcontPersonnel::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Pastikan entitas resmi terdaftar di subcont_companies
        $allOfficial = HseDepartmentService::getAllOfficialEntities();
        foreach ($allOfficial as $code => $name) {
            SubcontCompany::updateOrCreate(
                ['code' => $code],
                ['name' => $name]
            );
        }

        $names = [
            'Budi Santoso', 'Agus Setiawan', 'Bambang Suryono', 'Dedi Haryanto', 'Eko Prasetyo',
            'Fajar Nugroho', 'Gunawan Wibowo', 'Hendra Kusuma', 'Indra Pratama', 'Joko Susilo',
            'Kurniawan Adi', 'Lukman Hakim', 'Mulyadi Rahman', 'Nanang Sukardi', 'Oki Firmansyah',
            'Panji Saputra', 'Rian Hidayat', 'Suryadi Pratama', 'Teguh Wicaksono', 'Wahyu Hidayat',
            'Yusuf Maulana', 'Zainal Arifin', 'Ahmad Fauzi', 'Dimas Anggoro', 'Gandi Prabowo',
            'Heru Purnomo', 'Irfan Hakim', 'Kuswanto', 'Mahmud Yunus', 'Nur Hidayat',
            'Rahmat Hidayat', 'Slamet Riyadi', 'Tri Wahyudi', 'Untung Surapati', 'Wawan Kurniawan',
            'Zaenal Muttaqin', 'Ade Suherman', 'Bayu Anggara', 'Candra Wijaya', 'Deden Sopian',
            'Eka Ramadhan', 'Ferdiansyah', 'Gilang Ramadhan', 'Hadi Sucipto', 'Imam Safei',
            'Junaedi', 'Kiki Firmansyah', 'Lutfi Gunawan', 'Muhamad Ridwan', 'Novaldi Pratama',
            'Pandu Dewabrata', 'Rudi Hermawan', 'Sigit Purnomo', 'Taufik Hidayat', 'Umar Syarif',
            'Vicky Prasetya', 'Wildan Fauzi', 'Yayan Ruhian', 'Zulfikar', 'Ari Wibowo',
            'Bintang Pratama', 'Cecep Supriatna', 'Deni Setiawan', 'Edi Supriyadi', 'Fahmi Idris',
            'Galih Rakasiwi', 'Hariyanto', 'Ilyas Saputra', 'Jamaludin', 'Kosasih',
            'Leo Saputra', 'Maman Suratman', 'Nanang Qosim', 'Opik Taofik', 'Pupu Saepudin',
            'Rizal Fadhilah', 'Saeful Bahri', 'Toni Suhartono', 'Ujang Mulyana', 'Warisman',
            'Yanto Heryanto', 'Zaki Mubarok', 'Asep Saepuloh', 'Bondan Prakoso', 'Cahyono',
            'Dadang Hermawan', 'Endang Sujana', 'Farhan Ramadhan', 'Gerry Iskandar', 'Hamdan Yuwafi',
            'Ismail Marzuki', 'Jafar Sidik', 'Khaerul Anam', 'Lukman Sardi', 'Misbahul Munir',
            'Nana Suryana', 'Otong Kosasih', 'Pendi Priatna', 'Restu Gumilar', 'Subhan Kamil',
            'Tatank Sutarna', 'Uus Kuswara', 'Vikri Haikal', 'Wandi Rustandi', 'Yogi Pramana'
        ];

        $deptPositions = [
            'HRD' => ['HR Officer', 'Talent Specialist', 'Admin Payroll', 'Trainer HSE', 'HR Generalist'],
            'GA' => ['GA Supervisor', 'Facility Maintenance', 'Asset Controller', 'Admin GA', 'Driver Operasional'],
            'HSE' => ['HSE Officer', 'Safety Inspector', 'Environment Specialist', 'Safety Auditor', 'Emergency Respon Team'],
            'PRODUKSI' => ['Supervisor Fabrikasi', 'Foreman Assembly', 'Fitter Grade 1', 'Welder 3G/4G', 'Operator Mesin'],
            'JT9' => ['Foreman JT9', 'Operator Crane JT9', 'Fitter JT9', 'Welder JT9', 'Helper Fabrikasi'],
            'EXP' => ['Expediter', 'Logistics Planner', 'Shipping Coordinator', 'Admin Expediting', 'Material Controller'],
            'MSB' => ['MSB Engineer', 'Supervisor Mesin', 'Operator CNC', 'Teknisi Hidrolik', 'Helper Mesin'],
            'PPC' => ['Production Planner', 'PPC Officer', 'Capacity Planner', 'Admin PPC', 'Material Scheduler'],
            'PJC' => ['Project Cost Controller', 'Project Estimator', 'PJC Specialist', 'Admin Billing', 'Cost Engineer'],
            'MAINTENANCE' => ['Supervisor Maintenance', 'Teknisi Listrik Pabrik', 'Mekanik Mesin Berat', 'Maintenance Planner', 'Helper Maintenance'],
            'STEEL DETAILING' => ['Lead Detailer', 'Tekla Modeler', 'Draftsman Baja', 'Checker Gambar', 'Admin Engineering'],
            'WAREHOUSE' => ['Warehouse Supervisor', 'Toolkeeper', 'Material Handler', 'Admin Gudang Baja', 'Operator Forklift'],
            'QC' => ['QC Lead Inspector', 'NDT Technician', 'Dimensional Inspector', 'Weld Inspector', 'Documentation QC'],
            'CUTTING DRILLING' => ['Operator CNC Drilling', 'Operator Band Saw', 'Foreman Cutting', 'Fitter Pemotongan', 'Helper Drilling'],
        ];

        $subPositions = [
            'SUB.SBL' => ['Supervisor SBL', 'Foreman Fabrikasi', 'Fitter 1', 'Welder FCAW', 'Helper SBL'],
            'SUB.DKP' => ['Supervisor DKP', 'Foreman Assembly', 'Fitter Konstruksi', 'Welder SMAW', 'Helper DKP'],
            'SUB.SUM' => ['Supervisor SUM', 'Foreman Erection', 'Welder 6G', 'Scaffolder', 'Helper SUM'],
            'SUB.CM'  => ['Supervisor CM', 'Foreman Blasting', 'Blaster Lead', 'Painter Airless', 'Helper CM'],
            'SUB.PKN' => ['Supervisor PKN', 'Foreman Erection', 'Rigger Baja', 'Fitter Erection', 'Helper PKN'],
            'SUB.NAGA CIPTA' => ['Supervisor Naga Cipta', 'Foreman Las', 'Welder SAW', 'Fitter Las', 'Helper Las'],
            'SUB.BM'  => ['Supervisor BM', 'Foreman Grinding', 'Grinder Man', 'Fitter Pembersihan', 'Helper BM'],
            'SUB.AGS' => ['Supervisor AGS', 'Foreman Finishing', 'Quality Checker Sub', 'Painter Finishing', 'Helper AGS'],
        ];

        $nameIndex = 0;

        // 2. SEED DEPARTEMEN INTERNAL (14 Dept x 5 Pekerja = 70 Pekerja)
        foreach ($deptPositions as $deptCode => $positions) {
            for ($i = 0; $i < 5; $i++) {
                $nama = $names[$nameIndex % count($names)];
                $nameIndex++;
                $posisi = $positions[$i % count($positions)];
                $tglMasuk = Carbon::now()->subMonths(rand(1, 18))->subDays(rand(1, 28))->format('Y-m-d');
                $tglInduction = Carbon::parse($tglMasuk)->addDays(rand(0, 2))->format('Y-m-d');
                $tglLahir = Carbon::now()->subYears(rand(22, 48))->subDays(rand(1, 300))->format('Y-m-d');
                $status = ($i === 4 && in_array($deptCode, ['PRODUKSI', 'GA'])) ? 'Non-Aktif' : 'Aktif';
                $tglKeluar = ($status === 'Non-Aktif') ? Carbon::now()->subDays(rand(5, 20))->format('Y-m-d') : null;

                $sc = SubcontPersonnel::create([
                    'nama' => $nama,
                    'tgl_lahir' => $tglLahir,
                    'company_code' => $deptCode,
                    'plant_location' => 'Plant Kalijati Subang',
                    'tgl_masuk' => $tglMasuk,
                    'tgl_keluar' => $tglKeluar,
                    'tgl_induction' => $tglInduction,
                    'posisi' => $posisi,
                    'status' => $status,
                    'domisili_alamat' => 'Dusun Kalijati RT ' . sprintf('%02d', rand(1, 10)) . ' RW ' . sprintf('%02d', rand(1, 5)),
                    'domisili_desa' => 'Kalijati Timur',
                    'domisili_kec' => 'Kalijati',
                    'domisili_kota' => 'Subang',
                    'keterangan' => 'Safety Induction Lengkap - Standar MIS Plant',
                ]);

                // OTOMATIS KE MANPOWER
                $autoNoId = 'EMP-' . $deptCode . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
                $mp = Manpower::updateOrCreate(
                    ['no_id' => $autoNoId],
                    [
                        'nama' => $sc->nama,
                        'tgl_lahir' => $sc->tgl_lahir,
                        'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                        'departemen' => $deptCode,
                        'plant_location' => 'Plant Kalijati Subang',
                        'jabatan' => $sc->posisi,
                        'status' => $sc->status,
                        'apd_helmet_issued' => true,
                    ]
                );

                // OTOMATIS DATA PENGAMBILAN APD SAFETY HELMET
                LogistikApd::create([
                    'manpower_id' => $mp->id,
                    'nama_personel' => $sc->nama,
                    'departemen_subcont' => $deptCode,
                    'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'jenis_apd' => 'Safety Helmet',
                    'jumlah' => 1,
                    'keterangan' => 'Penyerahan Safety Helmet Putih MIS (Induction Pertama)'
                ]);

                // Tambahan variasi APD untuk logistik (Rompi & Kacamata untuk beberapa pekerja)
                if ($i % 2 === 0) {
                    LogistikApd::create([
                        'manpower_id' => $mp->id,
                        'nama_personel' => $sc->nama,
                        'departemen_subcont' => $deptCode,
                        'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                        'jenis_apd' => 'Rompi Reflektor',
                        'jumlah' => 1,
                        'keterangan' => 'Rompi K3 Standar Plant'
                    ]);
                }
            }
        }

        // 3. SEED SUBCONT RESMI (8 Subcont x 5 Pekerja = 40 Pekerja)
        foreach ($subPositions as $subCode => $positions) {
            for ($i = 0; $i < 5; $i++) {
                $nama = $names[$nameIndex % count($names)];
                $nameIndex++;
                $posisi = $positions[$i % count($positions)];
                $tglMasuk = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 28))->format('Y-m-d');
                $tglInduction = Carbon::parse($tglMasuk)->addDays(rand(0, 1))->format('Y-m-d');
                $tglLahir = Carbon::now()->subYears(rand(20, 45))->subDays(rand(1, 300))->format('Y-m-d');
                $status = ($i === 4 && in_array($subCode, ['SUB.SBL', 'SUB.DKP'])) ? 'Non-Aktif' : 'Aktif';
                $tglKeluar = ($status === 'Non-Aktif') ? Carbon::now()->subDays(rand(3, 15))->format('Y-m-d') : null;

                $sc = SubcontPersonnel::create([
                    'nama' => $nama,
                    'tgl_lahir' => $tglLahir,
                    'company_code' => $subCode,
                    'plant_location' => 'Plant Kalijati Subang',
                    'tgl_masuk' => $tglMasuk,
                    'tgl_keluar' => $tglKeluar,
                    'tgl_induction' => $tglInduction,
                    'posisi' => $posisi,
                    'status' => $status,
                    'domisili_alamat' => 'Mess Subcont Kalijati Barat Blok ' . chr(65 + $i),
                    'domisili_desa' => 'Kalijati Barat',
                    'domisili_kec' => 'Kalijati',
                    'domisili_kota' => 'Subang',
                    'keterangan' => 'Safety Induction Kontraktor - Lulus Uji K3',
                ]);

                // OTOMATIS KE MANPOWER
                $autoNoId = 'EMP-' . $subCode . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
                $mp = Manpower::updateOrCreate(
                    ['no_id' => $autoNoId],
                    [
                        'nama' => $sc->nama,
                        'tgl_lahir' => $sc->tgl_lahir,
                        'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                        'departemen' => $subCode,
                        'plant_location' => 'Plant Kalijati Subang',
                        'jabatan' => $sc->posisi,
                        'status' => $sc->status,
                        'apd_helmet_issued' => true,
                    ]
                );

                // OTOMATIS PENGAMBILAN APD SAFETY HELMET
                LogistikApd::create([
                    'manpower_id' => $mp->id,
                    'nama_personel' => $sc->nama,
                    'departemen_subcont' => $subCode,
                    'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'jenis_apd' => 'Safety Helmet',
                    'jumlah' => 1,
                    'keterangan' => 'Penyerahan Safety Helmet Biru Subcont MIS'
                ]);

                if ($i % 2 === 1) {
                    LogistikApd::create([
                        'manpower_id' => $mp->id,
                        'nama_personel' => $sc->nama,
                        'departemen_subcont' => $subCode,
                        'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                        'jenis_apd' => 'Kacamata Safety',
                        'jumlah' => 1,
                        'keterangan' => 'Kacamata Perlindungan Gerinda & Las'
                    ]);
                }
            }
        }

        // 4. SEED PROYEK (ENT & FA/PROJECT masing-masing 5 Pekerja)
        $entPositions = ['Project Manager Lapangan', 'Site Engineer ENT', 'Erector Lead', 'Rigger Specialist', 'Safety Officer Site'];
        for ($i = 0; $i < 5; $i++) {
            $nama = $names[$nameIndex % count($names)];
            $nameIndex++;
            $tglMasuk = Carbon::now()->subMonths(rand(2, 10))->subDays(rand(1, 20))->format('Y-m-d');
            $tglInduction = Carbon::parse($tglMasuk)->format('Y-m-d');
            $tglLahir = Carbon::now()->subYears(rand(24, 45))->format('Y-m-d');

            $sc = SubcontPersonnel::create([
                'nama' => $nama,
                'tgl_lahir' => $tglLahir,
                'company_code' => 'ENT',
                'plant_location' => 'Plant Kalijati Subang',
                'tgl_masuk' => $tglMasuk,
                'tgl_induction' => $tglInduction,
                'posisi' => $entPositions[$i],
                'status' => 'Aktif',
                'domisili_alamat' => 'Plant Kalijati Subang',
                'domisili_kota' => 'Subang',
                'keterangan' => 'Plant Kalijati Subang',
            ]);

            // ENT MASUK KE MANPOWER PROYEK TERSENDIRI
            $autoNoId = 'EMP-ENT-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
            $mp = Manpower::updateOrCreate(
                ['no_id' => $autoNoId],
                [
                    'nama' => $sc->nama,
                    'tgl_lahir' => $sc->tgl_lahir,
                    'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'departemen' => 'ENT',
                    'plant_location' => 'Plant Kalijati Subang',
                    'jabatan' => $sc->posisi,
                    'status' => $sc->status,
                    'apd_helmet_issued' => true,
                ]
            );

            // APD HELMET UNTUK PEKERJA ENT
            LogistikApd::create([
                'manpower_id' => $mp->id,
                'nama_personel' => $sc->nama,
                'departemen_subcont' => 'ENT',
                'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                'jenis_apd' => 'Safety Helmet',
                'jumlah' => 1,
                'keterangan' => 'Safety Helmet Lapangan ENT Proyek'
            ]);
        }

        $faPositions = ['Site Coordinator FA', 'Quality Representative', 'Erection Lead FA', 'Surveyor Proyek', 'Safety Officer FA'];
        for ($i = 0; $i < 5; $i++) {
            $nama = $names[$nameIndex % count($names)];
            $nameIndex++;
            $tglMasuk = Carbon::now()->subMonths(rand(1, 8))->subDays(rand(1, 20))->format('Y-m-d');
            $tglInduction = Carbon::parse($tglMasuk)->format('Y-m-d');
            $tglLahir = Carbon::now()->subYears(rand(25, 48))->format('Y-m-d');

            SubcontPersonnel::create([
                'nama' => $nama,
                'tgl_lahir' => $tglLahir,
                'company_code' => 'FA/PROJECT',
                'plant_location' => null,
                'tgl_masuk' => $tglMasuk,
                'tgl_induction' => $tglInduction,
                'posisi' => $faPositions[$i],
                'status' => 'Aktif',
                'domisili_alamat' => null,
                'domisili_kota' => null,
                'keterangan' => null,
            ]);
        }

        // 5. SEED TAMU KHUSUS (PKL, VISITOR, KLIEN masing-masing 5 Orang)
        $pklList = [
            ['nama' => 'Aditya Pratama', 'instansi' => 'SMKN 1 Subang (Teknik Pengelasan)', 'keperluan' => 'Praktek Kerja Lapangan Fabrikasi'],
            ['nama' => 'Bagus Setiadi', 'instansi' => 'SMKN 2 Subang (Teknik Mesin Industri)', 'keperluan' => 'Praktek Kerja Lapangan Workshop'],
            ['nama' => 'Cahya Ramadhani', 'instansi' => 'Politeknik Negeri Bandung', 'keperluan' => 'Magang Inspeksi K3 & HSE Plant'],
            ['nama' => 'Dwi Handoko', 'instansi' => 'Universitas Subang (Teknik Sipil)', 'keperluan' => 'Penelitian Detailing Struktur Baja'],
            ['nama' => 'Erwin Ardiansyah', 'instansi' => 'SMKN 1 Kalijati (Teknik Otomasi)', 'keperluan' => 'Praktek Kerja Lapangan Maintenance']
        ];
        foreach ($pklList as $pkl) {
            SubcontPersonnel::create([
                'nama' => $pkl['nama'],
                'tgl_lahir' => Carbon::now()->subYears(18)->format('Y-m-d'),
                'company_code' => 'PKL',
                'plant_location' => 'Plant Kalijati Subang',
                'tgl_masuk' => Carbon::now()->subDays(rand(10, 30))->format('Y-m-d'),
                'tgl_keluar' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'tgl_induction' => Carbon::now()->subDays(rand(10, 30))->format('Y-m-d'),
                'posisi' => $pkl['instansi'],
                'status' => 'Aktif',
                'domisili_alamat' => 'Subang',
                'domisili_kota' => 'Subang',
                'keterangan' => $pkl['keperluan'],
            ]);
        }

        $visitorList = [
            ['nama' => 'Ir. Hendro Wijoyo', 'instansi' => 'PT Krakatau Steel Tbk', 'keperluan' => 'Audit Mutu Raw Material Pelat Baja'],
            ['nama' => 'Dr. Maya Kartika', 'instansi' => 'Balai K3 Jawa Barat', 'keperluan' => 'Inspeksi & Pengujian Higiene Lingkungan Kerja'],
            ['nama' => 'Rudy Hartono, ST', 'instansi' => 'PT Surveyor Indonesia', 'keperluan' => 'Audit Sertifikasi ISO 45001 & SMK3'],
            ['nama' => 'Bambang Irawan', 'instansi' => 'Vendor Kalibrasi Mesin CNC', 'keperluan' => 'Kalibrasi Tahunan Alat Ukur & Mesin Cutting'],
            ['nama' => 'Ferry Susanto', 'instansi' => 'PT Asuransi Keselamatan Kerja', 'keperluan' => 'Risk Assessment Fasilitas Fabrikasi Baja']
        ];
        foreach ($visitorList as $vis) {
            SubcontPersonnel::create([
                'nama' => $vis['nama'],
                'company_code' => 'VISITOR',
                'plant_location' => 'Plant Kalijati Subang',
                'tgl_masuk' => Carbon::now()->format('Y-m-d'),
                'tgl_keluar' => Carbon::now()->format('Y-m-d'),
                'tgl_induction' => Carbon::now()->format('Y-m-d'),
                'posisi' => $vis['instansi'],
                'status' => 'Aktif',
                'domisili_alamat' => 'Jakarta / Bandung',
                'domisili_kota' => 'Jakarta',
                'keterangan' => $vis['keperluan'],
            ]);
        }

        $klienList = [
            ['nama' => 'Ahmad Soebardjo, MT', 'instansi' => 'PT Pertamina Hulu Rokan', 'keperluan' => 'Factory Acceptance Test (FAT) Struktur Pipe Rack'],
            ['nama' => 'Eng. Kevin Chandra', 'instansi' => 'Samsung C&T Corporation', 'keperluan' => 'Pemeriksaan Visual Welder Pre-qualification'],
            ['nama' => 'Bambang Trihatmodjo', 'instansi' => 'PT Waskita Karya (Persero) Tbk', 'keperluan' => 'Koordinasi Schedule Pengiriman Balok Jembatan'],
            ['nama' => 'Danang Wibowo, ST', 'instansi' => 'PT PLN Nusantara Power', 'keperluan' => 'Inspeksi Fabrikasi Menara Boiler & Cerobong'],
            ['nama' => 'Steven Lim', 'instansi' => 'Hyundai Engineering & Construction', 'keperluan' => 'Kick-off Meeting Fabrikasi Proyek Kilang Minyak']
        ];
        foreach ($klienList as $kli) {
            SubcontPersonnel::create([
                'nama' => $kli['nama'],
                'company_code' => 'KLIEN',
                'plant_location' => 'Plant Kalijati Subang',
                'tgl_masuk' => Carbon::now()->format('Y-m-d'),
                'tgl_keluar' => Carbon::now()->format('Y-m-d'),
                'tgl_induction' => Carbon::now()->format('Y-m-d'),
                'posisi' => $kli['instansi'],
                'status' => 'Aktif',
                'domisili_alamat' => 'Jakarta',
                'domisili_kota' => 'Jakarta',
                'keterangan' => $kli['keperluan'],
            ]);
        }
    }
}
