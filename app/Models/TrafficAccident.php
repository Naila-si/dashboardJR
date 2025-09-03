<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficAccident extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_laka','bulan_laka','tanggal_laka','kota','kecamatan','lokasi',
        'latitude','longitude','korban_total','korban_md','ahli_waris_total',
        'santunan','waktu','action_plan'
    ];
}
