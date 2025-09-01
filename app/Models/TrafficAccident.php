<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficAccident extends Model
{
    use HasFactory;

    protected $fillable = [
        'provinsi',
        'kota',
        'kecamatan',
        'korban_md',
        'korban_ll',
        'korban_total',
        'santunan',
        'bulan_laka',
        'tahun_laka',
        'rencana_penanganan',
        'latitude',
        'longitude',
        'jumlah_berkas',
        'jenis_kelamin',
        'usia_korban',
        'jenis_tabrakan',
        'waktu_kecelakaan',
        'jenis_kendaraan',
        'jenis_pekerjaan',
        'nama_jalan',
        'kelurahan_desa',
    ];
}
