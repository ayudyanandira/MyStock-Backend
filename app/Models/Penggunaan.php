<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penggunaan extends Model
{
    use HasFactory;

    protected $table = 'penggunaan';

    protected $fillable = [

        'nomor_transaksi',

        'tanggal',

        'keterangan',

        'user_id',
    ];

    protected $casts = [

        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function detailPenggunaan(): HasMany
    {
        return $this->hasMany(
            DetailPenggunaan::class,
            'penggunaan_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}