<?php


namespace App\Domain\TruckBookings\Requests;


use App\Domain\TruckBookings\Models\TruckBooking;
use Illuminate\Foundation\Http\FormRequest;

class TruckBookingRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->request->remove('client_company_id');
    }

    public function rules()
    {
        return [
            'routes' => 'required|array|min:2',
            'routes.*.address' => 'required|string',
            'routes.*.lat' => 'required|string',
            'routes.*.lng' => 'required|string',
            'routes.*.point' => 'required|string',
            'distance.*' => 'required|string',
            'regions' => 'required|array|min:2',
            'regions.*' => 'required|integer',
            'car_type_id' => 'required|exists:car_types,id',
            'driver_id' => 'nullable|exists:users,id',
            'cargo_type_id' => 'nullable|integer',
            'load_type_id' => 'nullable|integer',
            'weight' => 'nullable|numeric|min:0',
            'dimension_x' => 'nullable|numeric|min:0',
            'dimension_y' => 'nullable|numeric|min:0',
            'dimension_z' => 'nullable|numeric|min:0',
            'need_pack' => 'nullable|boolean',
            'pickup_date' => 'nullable|date',
            'need_provide_loader' => 'nullable|boolean',
            'loader_amount' => 'nullable|integer|min:0',
            'comment' => 'nullable|string',
            'reason_id' => 'nullable|string',
            'cancel_reason' => 'nullable|string',
            'payment_type' => 'nullable|in:'.implode(',', TruckBooking::paymentTypes()),
			'is_full_round_trip' => 'nullable|numeric|min:0',
        ];
    }
}
