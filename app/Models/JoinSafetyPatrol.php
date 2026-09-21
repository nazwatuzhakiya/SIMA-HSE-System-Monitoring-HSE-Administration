<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinSafetyPatrol extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'area_patrol',
        'tim_patroli',
        'kat1_kondisi_umum',
        'catatan1',
        'kat2_peralatan_mesin',
        'catatan2',
        'kat3_penataan_material',
        'catatan3',
        'kat4_instalasi_listrik',
        'catatan4',
        'kat5_sarana_darurat',
        'catatan5',
        'kat6_alat_angkat',
        'catatan6',
        'status',
    ];
}
