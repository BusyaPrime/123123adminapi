<?php

namespace App\Domain\CancelReasons\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class CancelReasonTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
