<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlinikObat extends Model
{
    use HasFactory;

    protected $table = 'klinik_obats';

    protected $fillable = [
        'nama_pegawai',
        'nip',
        'keluhan',
        'nama_obat',
        'jumlah_obat',
        'tanggal_pengambilan'
    ];
}
