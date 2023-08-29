<?php

namespace App;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin QueryBuilder
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string|null $registered_via
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function revokeOldTokens(): void
    {
        $this->tokens()->where('last_used_at', '<', now()->subYear())->delete();
    }

    public function instance(int $user_id): ?User
    {
        return $this->where('id', '=', $user_id)
            ->first();
    }
}
