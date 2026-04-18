<?php


namespace App\Domain\Users\Models;


use App\Domain\Companies\Models\Company;
use App\Domain\Regions\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * App\Domain\Users\Models\UserProfile
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $language
 * @property int $can_call
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereCanCall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Domain\Users\Models\User $user
 * @property string|null $avatar
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereAvatar($value)
 * @property int|null $rating
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereRating($value)
 * @property int|null $balance
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereBalance($value)
 * @property int|null $have_terminal
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserProfile whereHaveTerminal($value)
 */
class UserProfile extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static $imagePath = 'uploads/avatars/';
    protected static $licencePath = 'uploads/licences/';

    public static function getImagePath()
    {
        return self::$imagePath;
    }

    public static function getLicencePath()
    {
        return self::$licencePath;
    }

    public function imageUrl()
    {
        if(!$this->avatar) {
            return asset('uploads/defaults/user.png');
        }
        return asset(self::getImagePath().$this->avatar);
    }

    public function licenceUrl()
    {
        if(!$this->licence) {
            return null;
        }
        return asset(self::getLicencePath().$this->licence);
    }

    public function carLicenceUrl()
    {
        if(!$this->car_licence) {
            return null;
        }
        return asset(self::getLicencePath().$this->car_licence);
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        \Image::make($image)->fit(512, 512)->save(public_path(self::getImagePath().$filename));
        return $filename;
    }

    // public function uploadLicence(UploadedFile $image)
    public function uploadLicence(Array $images)
    {
        $front = \Image::make($images['front']);
        $back = \Image::make($images['back']);

        if($front->height() < $front->width() && $back->height() < $back->width()){
            $image = \Image::canvas(max($front->width(), $back->width()), $front->height() + $back->height());
            $image->insert($front, 'top');
            $image->insert($back, 'bottom');
        } else {
            $image = \Image::canvas($front->width() + $back->width(), max($front->height(), $back->height()));
            $image->insert($front, 'left');
            $image->insert($back, 'right');
        }

        $extension = $images['front']->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        // \Image::make($image)->save(public_path(self::getLicencePath().$filename));
        $image->save(public_path(self::getLicencePath(). $filename));
        return $filename;
    }

    public function uploadCarLicence(Array $images)
    {

        $front = \Image::make($images['front']);
        $back = \Image::make($images['back']);

        if ($front->height() < $front->width() && $back->height() < $back->width()) {
            $image = \Image::canvas(max($front->width(), $back->width()), $front->height() + $back->height());
            $image->insert($front, 'top');
            $image->insert($back, 'bottom');
        } else {
            $image = \Image::canvas($front->width() + $back->width(), max($front->height(), $back->height()));
            $image->insert($front, 'left');
            $image->insert($back, 'right');
        }

        $extension = $images['front']->getClientOriginalExtension();
        $filename = $this->id . '_' . uniqid() . '.' . $extension;
        $image->save(public_path(self::getLicencePath() . $filename));
        return $filename;
    }
    
    // public function uploadCarLicence($image)
    // {
    //     $extension = $image->getClientOriginalExtension();
    //     $filename = $this->id.'_'.uniqid().'.'.$extension;
    //     \Image::make($image)->save(public_path(self::getLicencePath().$filename));
    //     return $filename;
    // }

    // public function uploadLicence($image)
    // {
    //     $extension = $image->getClientOriginalExtension();
    //     $filename = $this->id.'_'.uniqid().'.'.$extension;
    //     \Image::make($image)->save(public_path(self::getLicencePath().$filename));
    //     return $filename;
    // }

    public function deleteImage()
    {
        $imagePath = public_path(self::getImagePath().$this->avatar);
        if ($this->avatar != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->avatar = null;
    }

    public function deleteLicence()
    {
        $imagePath = public_path(self::getLicencePath().$this->licence);
        if ($this->licence != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->licence = null;
        $this->save();
    }

    public function deleteCarLicence()
    {
        $imagePath = public_path(self::getLicencePath().$this->car_licence);
        if ($this->car_licence != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->car_licence = null;
        $this->save();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

}
