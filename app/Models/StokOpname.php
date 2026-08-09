<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function details(): HasMany
    {
        return $this->hasMany(
            DetailStokOpname::class,
            'stok_opname_id'
        );
    }
}