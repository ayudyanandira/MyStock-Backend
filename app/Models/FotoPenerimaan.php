<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'foto_penerimaan';

    protected $fillable = [

        'penerimaan_id',

        'nama_file',

        'path_file',

        'mime_type',

        'ukuran_file',

        'uploaded_by',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
