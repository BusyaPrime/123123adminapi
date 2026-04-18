<?php

namespace App\Domain\UserDeleteRequests\Model;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\User;

class UserDeleteRequest extends Model
{


    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACCEPTED = 'accepted';
    
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
