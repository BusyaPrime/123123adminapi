<?php

namespace App\Domain\TrackingOrderFunnel\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TrackingOrderFunnelStepRequest extends FormRequest
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
            'step' => 'required|numeric|min:1|max:15',
            'step_name' => 'required|string|min:2|max:255',
            'completed' => 'required|boolean',
            'screen_name' => 'required|string|max:100',
            'from_address' => 'nullable|string|max:255',
            'to_address' => 'nullable|string|max:255',
            'calculated_price' => 'nullable|string',
            'car_type' => 'nullable|string|max:255',
            'car_type_id' => 'nullable|numeric',
            'cargo_type' => 'nullable|string|max:255',
            'cargo_type_id' => 'nullable|numeric',
            'cargo_weight' => 'nullable|numeric'
            // 'max_step_reached' => 'nullable|numeric'
        ];
    }
}