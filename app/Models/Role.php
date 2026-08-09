<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN = 'Admin';

    public const USER = 'User';

    protected $table = 'roles';

    protected $fillable = [

        'name',
    ];

    public static function allowedNames(): array
    {
        return [self::ADMIN, self::USER];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
