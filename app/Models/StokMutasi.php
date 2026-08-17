<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'jenis_transaksi',
        'jumlah',
        'stok_awal',
        'stok_akhir',
        'referensi',
        'keterangan'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}