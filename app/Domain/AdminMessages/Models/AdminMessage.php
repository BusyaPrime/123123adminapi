<?php


namespace App\Domain\AdminMessages\Models;


use App\Domain\Users\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;

class AdminMessage extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }
}
