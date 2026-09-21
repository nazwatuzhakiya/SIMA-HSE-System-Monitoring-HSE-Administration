<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'update_date',
        'departemen',
        'lead_auditor',
        'auditor',
        'wakil_auditor',
        'jan_date',
        'feb_date',
        'mar_date',
        'apr_date',
        'may_date',
        'jun_date',
        'jul_date',
        'aug_date',
        'sep_date',
        'oct_date',
        'nov_date',
        'dec_date',
        'status',
    ];
}
