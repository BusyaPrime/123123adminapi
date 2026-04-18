<?php


namespace App\Domain\Articles\Models;


use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
