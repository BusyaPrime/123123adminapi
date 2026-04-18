<?php

namespace App\Domain\Transactions\Models;

use App\Domain\Companies\Models\Company;
use App\Domain\Users\Models\User;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Filterable;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
