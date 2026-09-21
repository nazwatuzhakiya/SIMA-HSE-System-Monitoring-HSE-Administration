<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcontPersonnel extends Model
{
    use HasFactory;

    protected $table = 'subcont_personnels';

    protected $fillable = [
        'nama',
        'tgl_lahir',
        'company_code',
        'asal_instansi',
        'departemen_penempatan',
        'tgl_masuk',
        'tgl_keluar',
        'tgl_induction',
        'tgl_induction_ulang',
        'posisi',
        'status',
        'domisili_alamat',
        'domisili_desa',
        'domisili_kec',
        'domisili_kota',
        'keterangan'
    ];
}
