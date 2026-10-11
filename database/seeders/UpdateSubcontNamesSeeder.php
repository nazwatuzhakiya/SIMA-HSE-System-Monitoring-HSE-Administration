<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubcontCompany;
use App\Services\HseDepartmentService;

class UpdateSubcontNamesSeeder extends Seeder
{
    public function run()
    {
        $entities = HseDepartmentService::getAllOfficialEntities();
        foreach ($entities as $code => $name) {
            SubcontCompany::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'plant_location' => 'Plant Kalijati Subang',
                    'status' => 'Aktif',
                    'description' => HseDepartmentService::isSpecialNonWorker($code) ? 'Monitoring Khusus Non-Pekerja' : 'Pekerja Subcont/Dept'
                ]
            );
        }
    }
}
