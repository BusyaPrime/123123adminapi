<?php


namespace App\Http\Controllers\Api;


use App\Domain\Cars\Models\Car;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\Companies\Models\Company;
use App\Domain\Messages\Jobs\SendMessageJob;
use App\Domain\Messages\Models\Message;
use App\Domain\Messages\Requests\SendMessageRequest;
use App\Domain\Messages\Resuorces\MessageResource;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\Transactions\Models\Transaction;
use App\Domain\TruckBookings\Jobs\TruckBookingDriverReviewJob;
use App\Domain\TruckBookings\Jobs\TruckBookingJob;
use App\Domain\TruckBookings\Jobs\TruckBookingReviewJob;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingRequest;
use App\Domain\TruckBookings\Requests\TruckBookingReviewRequest;
use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Users\Models\User;
use App\Events\OrderStatusEvent;
use App\Events\SearchStartEvent;
use App\Events\StartOrderListUpdatingEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\SaveAddress\Models\SaveAddress;

class AddressController extends Controller
{

    //get recent address list
    public function getRecentAddress(Request $request)
    {   
        return SaveAddress::getRecent($request->input('authUser')->id);
    }

    //get Saved address list
    public function getSavedAddress(Request $request)
    {     
        return SaveAddress::getSaved($request->input('authUser')->id);
    }

    //add saved address list
    public function addSavedAddress(Request $request)
    {        
        try {
            $add = ['address' => $request->input('address'),'lat'=>$request->input('lat'),'lng'=>$request->input('lng')];
            $address = [];
            array_push($address,$add);            
            $svad = new SaveAddress;
            $svad->user_id = $request->input('authUser')->id;
            $svad->address = json_encode($address);
            $svad->full_address = $request->input("full_address");
            $svad->label = $request->input("label");
            $svad->save();
            return response()->json(['message'=>'address has saved'], 200);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    //edit saved address list
    public function editSavedAddress(Request $request, SaveAddress $address)
    {
        try {
            $addr = [['address' => $request->input('address'),'lat'=>$request->input('lat'),'lng'=>$request->input('lng')]];
            $address->address = json_encode($addr);
            $address->full_address = $request->input("full_address");
            $address->label = $request->input("label");              
            $address->update();
            return response()->json(['message'=>'Address updated'], 200);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    //delete saved address list
    public function deleteSavedAddress(Request $request, SaveAddress $address)
    {
        try {         
            $address->delete();
            return response()->json(['message'=>'address deleted.'], 200);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

}
