<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingErp extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pekerja',
        'subcont_dept',
        'jenis_team',
        'struktur',
        'pernyataan',
        'tgl_training',
    ];
}
