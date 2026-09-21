<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManpowerPeriodStatus extends Model
{
    use HasFactory;

    protected $table = 'manpower_period_statuses';

    protected $fillable = [
        'manpower_id',
        'period_key',
        'period_year',
        'period_num',
        'status',
        'tgl_masuk',
        'tgl_keluar',
        'tgl_induction_ulang',
        'keterangan',
    ];

    public function manpower()
    {
        return $this->belongsTo(Manpower::class, 'manpower_id');
    }
}
