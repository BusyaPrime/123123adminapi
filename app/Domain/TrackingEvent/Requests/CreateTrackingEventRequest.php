<?php

namespace App\Domain\TrackingEvent\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateTrackingEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event_type' => 'required|string|max:100',
            'screen_name' => 'nullable|max:150',
            'properties' => 'nullable|array',
            'funnel_step' => 'nullable|numeric',
            'funnel_name' => 'nullable|string',
            'order_id' => 'nullable|numeric',
            'value' => 'nullable|numeric'
        ];
    }
}
