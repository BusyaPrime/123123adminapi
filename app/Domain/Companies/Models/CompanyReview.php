<?php

namespace App\Domain\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
class CompanyReview extends Model
{
    protected $guarded = ['id'];
    protected static $imagePath = 'uploads/documents/';

    protected $table = 'client_reviews';

    public static function getImagePath()
    {
        return self::$imagePath;
    }

    public function imageUrl($key = null)
    {
        if (!$this->{$key}) {
            return null;
        }
        return asset(self::getImagePath() . $this->{$key});
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id . '_' . uniqid() . '.' . $extension;
        $image->move(public_path(self::getImagePath()), $filename);
        return $filename;
    }

    static public function published(){
        return self::where('published', 1);
    }

    public function deleteImage($key)
    {
        $imagePath = public_path(self::getImagePath() . $this->{$key});
        if ($this->{$key} != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->{$key} = null;
    }
}
