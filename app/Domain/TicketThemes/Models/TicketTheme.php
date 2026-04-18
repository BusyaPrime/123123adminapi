<?php

namespace App\Domain\TicketThemes\Models;

use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class TicketTheme extends Model
{
    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;
    protected $guarded = ['id'];
}
