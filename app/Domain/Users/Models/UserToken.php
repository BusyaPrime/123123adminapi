<?php


namespace App\Domain\Users\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Users\Models\UserToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $device_info
 * @property string|null $logged_at
 * @property string|null $last_seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereDeviceInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereLoggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Domain\Users\Models\User $user
 * @property string|null $fcm_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserToken whereFcmToken($value)
 */
class UserToken extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'logged_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
