<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'detail_penerimaan';

    protected $fillable = [

        'penerimaan_id',

        'barang_id',

        'jumlah_pesanan',
        'jumlah_diterima',
        'selisih',
        'status',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [

        'jumlah_pesanan' => 'decimal:2',
        'jumlah_diterima' => 'decimal:2',
        'selisih' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
