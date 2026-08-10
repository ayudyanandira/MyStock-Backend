<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerimaan extends Model
{
    use HasFactory;

    protected $table = 'penerimaan';

    protected $fillable = [
        'nomor_transaksi',
        'supplier_id',
        'tanggal',
        'tanggal_terima',
        'status', // 'pending', 'completed', 'cancelled'
    ];

    protected $casts = [

        'tanggal' => 'date',
        'tanggal_terima' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailPenerimaan::class,
            'penerimaan_id'
        );
    }

    public function fotoPenerimaan(): HasMany
    {
        return $this->hasMany(
            FotoPenerimaan::class,
            'penerimaan_id'
        );
    }
}
