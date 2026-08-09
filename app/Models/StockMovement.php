<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    // Tabel ini hanya memakai created_at (tanpa updated_at)
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'reference_type',
        'reference_id',
        'qty_in',
        'qty_out',
        'stock_before',
        'stock_after',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'qty_in'       => 'float',
        'qty_out'      => 'float',
        'stock_before' => 'float',
        'stock_after'  => 'float',
        'created_at'   => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}