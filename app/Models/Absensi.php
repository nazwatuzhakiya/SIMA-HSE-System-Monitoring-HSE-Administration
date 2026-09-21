<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'jenis_kegiatan',
        'tanggal',
        'departemen_subcont',
        'manpower_id',
        'nama_personel',
        'status_kehadiran',
        'keterangan',
        'alasan_perubahan',
        'is_fixed'
    ];

    public function manpower()
    {
        return $this->belongsTo(Manpower::class, 'manpower_id');
    }
}
