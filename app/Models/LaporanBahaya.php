<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBahaya extends Model
{
    use HasFactory;

    protected $table = 'laporan_bahayas';

    protected $fillable = [
        'ticket_code',
        'nama_pelapor',
        'area_lokasi',
        'foto_url',
        'deskripsi',
        'tingkat_risiko',
        'status',
        'tindak_lanjut'
    ];
}
