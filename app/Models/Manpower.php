<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manpower extends Model
{
    use HasFactory;

    protected $table = 'manpowers';

    protected $fillable = [
        'no_id',
        'nama',
        'tgl_lahir',
        'tgl_masuk',
        'tgl_induction',
        'tgl_induction_ulang',
        'tgl_keluar',
        'departemen',
        'plant_location',
        'jabatan',
        'status',
        'apd_helmet_issued'
    ];

    public function periodStatuses()
    {
        return $this->hasMany(ManpowerPeriodStatus::class, 'manpower_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'manpower_id');
    }

    public function logistikApd()
    {
        return $this->hasMany(LogistikApd::class, 'manpower_id');
    }
}
