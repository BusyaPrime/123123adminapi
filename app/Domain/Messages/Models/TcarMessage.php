<?php


namespace App\Domain\Messages\Models;


use App\Domain\Users\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Messages\Models\TcarMessage
 *
 * @property int $id
 * @property int|null $tcar_booking_id
 * @property int|null $user_id
 * @property string|null $message
 * @property int|null $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Users\Models\UserProfile|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereTcarBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\TcarMessage whereUserId($value)
 * @mixin \Eloquent
 */
class TcarMessage extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
