<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenggunaan extends Model
{
    use HasFactory;

    protected $table = 'detail_penggunaan';

    protected $fillable = [

        'penggunaan_id',

        'barang_id',

        'jumlah',

        'catatan',
    ];

    protected $casts = [

        'jumlah' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function penggunaan(): BelongsTo
    {
        return $this->belongsTo(Penggunaan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}