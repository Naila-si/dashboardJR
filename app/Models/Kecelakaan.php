<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecelakaan extends Model
{
    protected $fillable = [
        'nama', 'lokasi', 'tanggal_waktu', 'laporan', 'cidera', 'sifat_laka', 'status_lp'
    ];

    // Hubungan One-to-Many dengan Activity
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
