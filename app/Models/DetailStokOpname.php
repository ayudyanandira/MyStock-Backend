<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailStokOpname extends Model
{
    use HasFactory;

    protected $table = 'detail_stok_opname';

    public $timestamps = false;

    protected $fillable = [
        'stok_opname_id',
        'barang_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
    ];

    protected $casts = [
        'stok_sistem' => 'float',
        'stok_fisik'  => 'float',
        'selisih'      => 'float',
    ];

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(StokOpname::class, 'stok_opname_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}