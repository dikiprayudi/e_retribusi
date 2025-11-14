<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penerimaan extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     */
    protected $fillable = [
        'tanggal',
        'no_rm',
        'nama_pasien',
        'jenis_kelamin',
        'alamat',
        'jenis_kunjungan',
        'status_pasien',
        'poli_id',
        'tindakan',     // <-- Sangat penting
        'total_tarif',  // <-- Sangat penting
        'diskon',
    ];
    // --- TAMBAHKAN KODE INI ---
    /**
     * The attributes that should be cast.
     *
     */
    protected $casts = [
        'tindakan' => 'array', // Ini akan otomatis mengubah array -> JSON saat simpan
        'tanggal'  => 'date',
    ];
    // ---
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
