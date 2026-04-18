<?php


namespace App\Domain\PushNotifications\Models;


use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
