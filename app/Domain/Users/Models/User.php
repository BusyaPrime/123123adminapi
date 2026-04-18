<?php

namespace App\Domain\Users\Models;

use App\Domain\AdminMessages\Models\AdminMessage;
use App\Domain\AdminRoles\Models\AdminRole;
use App\Domain\Cars\Models\Car;
use App\Domain\Companies\Models\Company;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\Regions\Models\Region;
use App\Domain\Tcars\Models\Tcar;
use App\Domain\Users\Notifications\VerifyCode;
use App\Services\FilterService\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
class User extends Authenticatable
{
    use Notifiable, Filterable;

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
        'last_seen_at' => 'datetime',
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

    public function codes()
    {
        return $this->hasMany(UserCode::class);
    }

    public function tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function adminRole()
    {
        return $this->belongsTo(AdminRole::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(TruckBooking::class);
    }

    public function sendVerifyCodeNotification($code)
    {
        if (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $this->notify(new VerifyCode($code));
        }
    }

    public function smsTimeOut()
    {
        $code = $this->codes()->first();
        if (!$code) {
            return 0;
        }
        $difference = $code->created_at->timestamp + static::SMS_TIMEOUT - time();
        return ($difference) > 0 ? $difference: 0;
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function ownedCompany()
    {
        return $this->hasOne(Company::class);
    }

    public function car()
    {
        return $this->hasOne(Car::class);
    }

    public function tcar()
    {
        return $this->hasOne(Tcar::class);
    }

    public function adminMessages()
    {
        return $this->hasMany(AdminMessage::class, 'chat_id', 'id');
    }

    public function pushNotifications()
    {
        return $this->hasMany(PushNotification::class);
    }

    public function getFcmTokens()
    {
        $tokens = [];

        foreach($this->tokens->toArray() as $token) {
            if(!empty($token['fcm_token'])) {
                $tokens[] = $token['fcm_token'];
            }
        }

        return $tokens;
    }

    public function resolveClientCompany(array $with = []): ?Company
    {
        $with = array_values(array_unique($with));

        if ((int) $this->is_external === 1) {
            $ownedCompanyQuery = Company::query()->where('user_id', $this->id);

            if (!empty($with)) {
                $ownedCompanyQuery->with($with);
            }

            return $ownedCompanyQuery->first();
        }

        $profileCompanyId = optional($this->profile)->company_id;

        if (!$profileCompanyId) {
            return null;
        }

        $profileCompanyQuery = Company::query();

        if (!empty($with)) {
            $profileCompanyQuery->with($with);
        }

        return $profileCompanyQuery->find($profileCompanyId);
    }

    public function resolveClientCompanyId(): ?int
    {
        $company = $this->resolveClientCompany();

        return $company ? (int) $company->id : null;
    }
}


?>
