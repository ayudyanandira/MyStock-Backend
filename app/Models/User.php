<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [

        'role_id',

        'name',

        'email',

        'password',

        'is_active',
    ];

    protected $hidden = [

        'password',

        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'password' => 'hashed',

            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function uploadedPhotos(): HasMany
    {
        return $this->hasMany(
            FotoPenerimaan::class,
            'uploaded_by'
        );
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(
            StockMovement::class,
            'created_by'
        );
    }

    public function penggunaan(): HasMany
    {
        return $this->hasMany(
            Penggunaan::class,
            'user_id'
        );
    }

    public function stokOpname(): HasMany
    {
        return $this->hasMany(
            StokOpname::class,
            'created_by'
        );
    }

    public function detailStokOpname()
    {
        return $this->hasManyThrough(
            DetailStokOpname::class,
            StokOpname::class,
            'created_by',
            'stok_opname_id'
        );
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(
            AuditLog::class,
            'user_id'
        );
    }
}
