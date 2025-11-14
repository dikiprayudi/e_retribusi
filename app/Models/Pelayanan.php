<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelayanan extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'poli_id',
        'jenis_pelayanan',
        'jenis_pemeriksaan',
        'kode_rekening',
        'tarif_layanan',
    ];
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }
}
