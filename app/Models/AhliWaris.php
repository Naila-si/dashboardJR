<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhliWaris extends Model
{
    use HasFactory;

    protected $table = 'ahliwaris';

    protected $fillable = [
        'tanggal',
        'no_berkas',
        'cedera',
        'nama_pemohon',
        'alamat',
        'penyelesaian',
        'status',
    ];
}

