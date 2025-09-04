<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecelakaan_id',
        'title',
        'description',
        'priority',
        'date',
        'image',
    ];

    // Relasi ke Kecelakaan
    public function kecelakaan()
    {
        return $this->belongsTo(Kecelakaan::class);
    }
}
