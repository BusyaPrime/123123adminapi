<?php


namespace App\Domain\TCarBookings\Requests;


use App\Domain\TCarBookings\Models\TcarBooking;
use Illuminate\Foundation\Http\FormRequest;

class TcarBookingRequest extends FormRequest
{

    public function rules()
    {
        return [
            'routes' => 'required|array|min:2',
            'routes.*.address' => 'required|string',
            'routes.*.lat' => 'required|string',
            'routes.*.lng' => 'required|string',
//            'user_id' => 'required|exists:users,id',
            'distance' => 'required|string',
            'date' => 'required|string',
            'time' => 'nullable|string',
            'car_type_id' => 'required|exists:tcar_types,id',
            'driver_id' => 'nullable|exists:users,id',
            'peoples' => 'required|integer|min:1',
            'ac' => 'nullable|boolean',
            'kids_seat' => 'nullable|boolean',
            'comment' => 'nullable|string',
            'payment_type' => 'nullable|in:'.implode(',', TcarBooking::paymentTypes()),
        ];
    }
}
