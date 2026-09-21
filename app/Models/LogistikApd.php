<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogistikApd extends Model
{
    use HasFactory;

    protected $table = 'logistik_apds';

    protected $fillable = [
        'manpower_id',
        'nama_personel',
        'departemen_subcont',
        'tanggal_pengambilan',
        'jenis_apd',
        'jumlah',
        'keterangan'
    ];

    public function manpower()
    {
        return $this->belongsTo(Manpower::class, 'manpower_id');
    }
}
