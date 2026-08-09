<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'no_telepon',
        'email',
        'jenis_barang',
    ];

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class,'supplier_id');
    }
}