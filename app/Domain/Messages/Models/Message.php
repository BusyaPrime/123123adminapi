<?php


namespace App\Domain\Messages\Models;


use App\Domain\Users\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Messages\Models\Message
 *
 * @property int $id
 * @property int|null $truck_booking_id
 * @property int|null $user_id
 * @property string|null $message
 * @property int|null $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Users\Models\UserProfile|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereTruckBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Messages\Models\Message whereUserId($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }
}
