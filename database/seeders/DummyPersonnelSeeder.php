<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubcontCompany;
use App\Models\SubcontPersonnel;
use App\Models\Manpower;

class DummyPersonnelSeeder extends Seeder
{
    public function run()
    {
        $companies = [
            ['code' => 'DKP', 'name' => 'PT Dwi Kartika Pratama'],
            ['code' => 'BM', 'name' => 'PT Bangun Mandiri'],
            ['code' => 'SB', 'name' => 'PT Sinar Baru Construction'],
            ['code' => 'SUM', 'name' => 'PT Sumber Utama Mandiri'],
            ['code' => 'AGS', 'name' => 'PT Anugerah Gracia Sejahtera'],
            ['code' => 'PKN', 'name' => 'PT Panca Karya Nusantara'],
            ['code' => 'CM', 'name' => 'PT Cipta Mitra Eka'],
            ['code' => 'NC', 'name' => 'PT Nusantara Steel Craft'],
            ['code' => 'ENT', 'name' => 'PT Energy Nusantara Tech'],
            ['code' => 'MURINDA', 'name' => 'PT Murinda Iron Steel Internal']
        ];

        foreach ($companies as $c) {
            SubcontCompany::firstOrCreate(['code' => $c['code']], ['name' => $c['name'], 'status' => 'Aktif']);
        }

        $firstNames = ['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Giri', 'Hendra', 'Irfan', 'Joko', 'Kiki', 'Lukman', 'Maman', 'Nurdin', 'Oki', 'Prasetyo', 'Rahmat', 'Suryadi', 'Taufik', 'Utomo', 'Vicky', 'Wawan', 'Yudi', 'Zainal'];
        $lastNames = ['Santoso', 'Pratama', 'Wibowo', 'Hidayat', 'Kusuma', 'Saputra', 'Setiawan', 'Nugroho', 'Firmansyah', 'Suryana', 'Hutapea', 'Kurniawan', 'Ramadhan', 'Wijaya', 'Subagyo', 'Syahputra', 'Baskoro', 'Triyono'];

        $posisiList = ['Fitter Steel', 'Welder 6G', 'Welder 3G', 'Rigger Heavy', 'Helper Fabricator', 'Operator Crane', 'Safety Inspector', 'Quality Control', 'Foreman Structure', 'Supervisor Site'];

        $departments = [
            'Produksi', 'MSB', 'Built Up 9', 'Cutting Drilling', 'PPC', 'SD', 'QC',
            'MNT', 'Warehouse', 'HR/GA', 'PJC', 'EXP', 'DKP', 'BM', 'SB', 'SUM',
            'AGS', 'PKN', 'CM', 'NC', 'ENT', 'Projek'
        ];

        // Seed SubcontPersonnel (min 10-12 per subcont company)
        foreach ($companies as $comp) {
            $existingCount = SubcontPersonnel::where('company_code', $comp['code'])->count();
            $target = max(12, 12 - $existingCount);

            for ($i = 1; $i <= $target; $i++) {
                $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
                $pos = $posisiList[array_rand($posisiList)];
                
                SubcontPersonnel::create([
                    'nama' => $name,
                    'company_code' => $comp['code'],
                    'tgl_masuk' => date('Y-m-d', strtotime('-' . rand(10, 300) . ' days')),
                    'tgl_induction' => date('Y-m-d', strtotime('-' . rand(10, 300) . ' days')),
                    'posisi' => $pos,
                    'status' => 'Aktif',
                    'domisili_alamat' => 'Jl. Raya Subang No. ' . rand(10, 150),
                    'domisili_desa' => 'Kalijati Timur',
                    'domisili_kec' => 'Kalijati',
                    'domisili_kota' => 'Subang',
                    'keterangan' => 'Pekerja Aktif Field'
                ]);
            }
        }

        // Seed Manpower (min 10-12 per department & subcont code)
        $seq = Manpower::max('id') + 1;
        foreach ($departments as $dept) {
            $existingCount = Manpower::where('departemen', $dept)->count();
            $target = max(10, 10 - $existingCount);

            for ($i = 1; $i <= $target; $i++) {
                $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
                $pos = $posisiList[array_rand($posisiList)];
                $empId = 'EMP-' . strtoupper(substr($dept, 0, 3)) . '-' . str_pad($seq++, 4, '0', STR_PAD_LEFT);

                Manpower::create([
                    'no_id' => $empId,
                    'nama' => $name,
                    'tgl_induction' => date('Y-m-d', strtotime('-' . rand(10, 300) . ' days')),
                    'departemen' => $dept,
                    'jabatan' => $pos,
                    'status' => 'Aktif',
                    'apd_helmet_issued' => 1
                ]);
            }
        }
    }
}
