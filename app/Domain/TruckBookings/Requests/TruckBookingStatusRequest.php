<?php


namespace App\Domain\TruckBookings\Requests;


use App\Domain\TruckBookings\Models\TruckBooking;
use Illuminate\Foundation\Http\FormRequest;

class TruckBookingStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'required|in:'.implode(',', TruckBooking::statuses()),
            'driver_id' => 'nullable|exists:users,id|required_if:status,'.TruckBooking::STATUS_ACCEPTED,
            'waiting_time' => 'nullable|integer|min:0'
        ];
    }
}
