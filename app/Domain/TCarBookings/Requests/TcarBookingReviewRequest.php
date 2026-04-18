<?php


namespace App\Domain\TCarBookings\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TcarBookingReviewRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rating' => 'required|integer|min:0|max:5',
            'comment' => 'required|string',
        ];
    }
}
