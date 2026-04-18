<?php


namespace App\Domain\TruckBookings\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TruckBookingReviewRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ];
    }
}
