<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit5R extends Model
{
    use HasFactory;

    protected $table = 'audit_5rs';

    protected $fillable = [
        'auditor_name',
        'departemen',
        'tanggal',
        'score_ringkas',
        'score_rapi',
        'score_resik',
        'score_rawat',
        'score_rajin',
        'total_percentage',
        'catatan'
    ];
}
