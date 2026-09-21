<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcontCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'plant_location',
        'status',
        'description',
    ];
}
