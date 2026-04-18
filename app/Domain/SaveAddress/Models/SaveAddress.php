<?php


namespace App\Domain\SaveAddress\Models;


use Illuminate\Database\Eloquent\Model;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\SaveAddress\Models\SaveAddress;

class SaveAddress extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $table = 'save_address';


    public static function getRecent($id)
    {
        $result = TruckBooking::select('id','routes','user_id')->where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        foreach($result as $k=>$o) $result[$k]->routes =  json_decode($result[$k]->routes);
        return $result;        
    }


    public static function getSaved($id)
    {
        $result = SaveAddress::where('user_id', $id)->orderBy('id', 'DESC')->get();
        foreach($result as $k=>$o) $result[$k]->address =  json_decode($result[$k]->address);
        return $result;
    }

}
