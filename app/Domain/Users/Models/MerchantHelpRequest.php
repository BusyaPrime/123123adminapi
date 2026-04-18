<?php

namespace App\Domain\Users\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Users\Models\User
 *
 * @property int $id
 * @property string|null $role
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int $active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User actives()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User notActives()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\User whereUsername($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Users\Models\UserCode[] $code
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Users\Models\UserCode[] $codes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Users\Models\UserToken[] $tokens
 * @property-read \App\Domain\Users\Models\UserProfile $profile
 * @property-read \App\Domain\Cars\Models\Car $car
 * @property-read \App\Domain\Cars\Models\Car $tcar
 */
class MerchantHelpRequest extends Model
{

    const ROLE_ADMIN = 'admin';
    const ROLE_DRIVER = 'driver';
    const ROLE_CLIENT = 'client';
    const ROLE_MERCHANT = 'merchant';

    const SMS_TIMEOUT = 60; //seconds

    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

    protected $guarded = [
        'id'
    ];

    public static function roles()
    {
        return [
            static::ROLE_ADMIN,
            static::ROLE_DRIVER,
            static::ROLE_CLIENT,
            static::ROLE_MERCHANT,
        ];
    }


    public static function apiRoles()
    {
        return [
            static::ROLE_DRIVER,
            static::ROLE_CLIENT,
        ];
    }

    public static function statuses()
    {
        return [
            static::STATUS_ACTIVE,
            static::STATUS_NOT_ACTIVE,
        ];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role === static::ROLE_ADMIN;
    }


    public function isActive()
    {
        return $this->active === static::STATUS_ACTIVE;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', static::ROLE_ADMIN);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActives($query)
    {
        return $query->where('active', static::STATUS_ACTIVE);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotActives($query)
    {
        return $query->where('active', static::STATUS_NOT_ACTIVE);
    }

    /**
     * Check role
     *
     * @param array|string $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        $myRoles = self::roles();

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (in_array($role, $myRoles) && $this->role == $role) {
                    return true;
                }
            }

            return false;
        }

        return in_array($roles, $myRoles) && $this->role == $roles;
    }

   
}
