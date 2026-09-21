<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsidenK3 extends Model
{
    use HasFactory;

    protected $table = 'insiden_k3s';

    protected $fillable = [
        'kode_insiden',
        'tanggal',
        'lokasi',
        'deskripsi_kejadian',
        'kategori',
        'nama_korban',
        'nama_saksi',
        'pj_departemen',
        'tindak_lanjut',
        'status'
    ];
}
