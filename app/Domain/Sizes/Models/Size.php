<?php

namespace App\Domain\Sizes\Models;

use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Size extends Model
{

    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static $imagePath = 'uploads/sizes/';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = app()->getLocale();
    }

    public static function getImagePath()
    {
        return self::$imagePath;
    }

    public function imageUrl()
    {
        if (!$this->icon) {
            return asset('uploads/defaults/size.png');
        }
        return asset(self::getImagePath().$this->icon);
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        if($extension != 'svg'){
            \Image::make($image)->save(public_path(self::getImagePath().$filename));
        } else{
            \Storage::disk('uploads')->putFileAs(self::getImagePath(), $image, $filename);
        }
        return $filename;
    }


    public function deleteImage()
    {
        $imagePath = public_path(self::getImagePath().$this->icon);
        if ($this->icon != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->icon = null;
    }
}
