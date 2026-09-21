<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcontHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcont_personnel_id',
        'nama_pekerja',
        'change_type',
        'old_value',
        'new_value',
        'notes',
    ];

    public function personnel()
    {
        return $this->belongsTo(SubcontPersonnel::class, 'subcont_personnel_id');
    }
}
