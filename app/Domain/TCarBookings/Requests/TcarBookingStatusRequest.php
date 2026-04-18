<?php


namespace App\Domain\TCarBookings\Requests;


use App\Domain\TCarBookings\Models\TcarBooking;
use Illuminate\Foundation\Http\FormRequest;

class TcarBookingStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'required|in:'.implode(',', TcarBooking::statuses()),
            'driver_id' => 'nullable|exists:users,id|required_if:status,'.TcarBooking::STATUS_ACCEPTED,
            'waiting_time' => 'nullable|integer|min:0'
        ];
    }
}
