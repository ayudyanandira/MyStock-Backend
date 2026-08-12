<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [

        'kode_barang',

        'nama_barang',

        'kategori_id',

        'satuan_id',

        'stok',

        'stok_minimum',

        'is_active',
    ];

    protected $casts = [

        'stok'=>'float',
        'stok_minimum'=>'float',
        'is_active'=>'boolean'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class,'barang_id');
    }

    public function detailPenggunaan(): HasMany
    {
        return $this->hasMany(DetailPenggunaan::class,'barang_id');
    }

    public function detailStokOpname(): HasMany
    {
        return $this->hasMany(DetailStokOpname::class,'barang_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class,'barang_id');
    }
}