<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubcontPersonnel;
use App\Models\Manpower;

class DummyBirthDateSeeder extends Seeder
{
    public function run(): void
    {
        $personnels = SubcontPersonnel::all();
        $validNoIds = [];

        foreach ($personnels as $p) {
            if (!$p->tgl_lahir) {
                $y = rand(1982, 2002);
                $m = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
                $d = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                $p->update(['tgl_lahir' => "{$y}-{$m}-{$d}"]);
            }

            $noId = 'EMP-' . $p->company_code . '-' . str_pad($p->id, 3, '0', STR_PAD_LEFT);
            $validNoIds[] = $noId;

            Manpower::updateOrCreate(
                ['no_id' => $noId],
                [
                    'nama' => $p->nama,
                    'tgl_lahir' => $p->tgl_lahir,
                    'tgl_induction' => $p->tgl_induction ?: $p->tgl_masuk,
                    'departemen' => $p->company_code,
                    'plant_location' => 'Plant Kalijati Subang',
                    'jabatan' => $p->posisi,
                    'status' => $p->status,
                    'apd_helmet_issued' => true
                ]
            );
        }

        // Delete orphan manpower records that don't match Monitoring Pekerja
        if (!empty($validNoIds)) {
            Manpower::whereNotIn('no_id', $validNoIds)->delete();
        }
    }
}
