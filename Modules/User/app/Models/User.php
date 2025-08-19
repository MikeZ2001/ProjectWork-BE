<?php

namespace Modules\User\Models;

use AllowDynamicProperties;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\User\Database\Factories\UserFactoryFeatureTest;

#[AllowDynamicProperties]
class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    public static string $factory = User::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Override the model factory resolution so Laravel uses your module factory.
     */
    protected static function newFactory(): UserFactoryFeatureTest
    {
        return UserFactoryFeatureTest::new();
    }
}
