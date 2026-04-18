<?php


namespace App\Domain\Articles\Models;


use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Article extends Model
{
    use Translatable, Filterable;

    public $translatedAttributes = ['title', 'short', 'full', 'link'];
    protected static $imagePath = 'uploads/articles/';

    
    public static $categories = [
        'diagnostics',
        'therapy',
        'nature',
        'new_forms',
    ];

    protected $guarded = ['id'];

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
        if(!$this->image) {
            return asset('uploads/defaults/article.png');
        }
        return asset(self::getImagePath().$this->image);
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        // \Image::make($image)->fit(1200, 600)->save(public_path(self::getImagePath().$filename));
        \Image::make($image)->save(public_path(self::getImagePath().$filename));
        return $filename;
    }

    public function deleteImage()
    {
        $imagePath = public_path(self::getImagePath().$this->image);
        if ($this->image != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->image = null;
    }

}
