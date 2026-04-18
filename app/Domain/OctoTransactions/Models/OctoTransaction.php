<?php

namespace App\Domain\OctoTransactions\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class OctoTransaction extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
