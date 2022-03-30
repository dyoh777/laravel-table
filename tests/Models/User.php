<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tests\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;

    /** @var string */
    protected $table = 'users';

    /** @var array */
    protected $fillable = ['name', 'email', 'password', 'active', 'email_verified_at'];

    protected $casts = ['active' => 'boolean', 'email_verified_at' => 'datetime'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'owner_id');
    }
}
