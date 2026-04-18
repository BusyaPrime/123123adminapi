<?php

namespace App\Domain\CancelReasons\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class CancelReason extends Model
{

    use Translatable;
    public $translatedAttributes = ['reason'];
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = app()->getLocale();
    }
}
