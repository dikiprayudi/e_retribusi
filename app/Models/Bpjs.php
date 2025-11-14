<?php

namespace App\Models;

use App\Enums\KategoriBpjs; // <-- 1. Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpjs extends Model
{
    use HasFactory;

    // Izinkan pengisian data
    protected $fillable = [
        'tanggal',
        'no_sts',
        'kategori',
        'uraian',
        'jumlah',
        'bank',
        'no_rekening',
        'keterangan',
    ];

    // <-- 2. Penting! Hubungkan Enum ke kolom 'kategori'
    protected $casts = [
        'kategori' => KategoriBpjs::class,
        'tanggal' => 'date',
    ];
}