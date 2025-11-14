<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Pegawai; // <-- Pastikan ini ada

class Sts extends Model
{
    use HasFactory;

    /**
     * Tentukan kolom yang boleh diisi (sesuai DB Anda).
     */
    protected $fillable = [
        'periode_awal',
        'periode_akhir',
        'no_sts',
        'tanggal_setor',
        'bank',
        'no_rekening',
        'pengguna_anggaran_id',
        'pengguna_anggaran_nip',
        // 'pengguna_anggaran_pangkat', // <-- Hapus
        'bendahara_id',
        'bendahara_nip',
        // 'bendahara_pangkat', // <-- Hapus
        'total_disetor', // <-- Sesuaikan
        // 'rincian', // <-- Hapus
    ];

    /**
     * Hapus $casts untuk 'rincian' jika ada
     */
    // protected $casts = [
    //     'rincian' => 'array', // <-- Hapus atau komentari baris ini
    // ];

    /**
     * Relasi ke Pegawai (sebagai Pengguna Anggaran).
     */
    public function penggunaAnggaran(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pengguna_anggaran_id');
    }

    /**
     * Relasi ke Pegawai (sebagai Bendahara).
     */
    public function bendahara(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'bendahara_id');
    }
}