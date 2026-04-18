<?php

namespace App\Domain\TrackingOrderFunnel\Requests;


use App\Services\Tracking\TrackingOrderFunnelDefinition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'step' => ['required', 'numeric', Rule::in(array_column(TrackingOrderFunnelDefinition::steps(), 'order'))],
            'step_name' => ['required', 'string', Rule::in(TrackingOrderFunnelDefinition::stepNames())],
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $stepMap = collect(TrackingOrderFunnelDefinition::steps())->keyBy('order');
            $step = (int) $this->input('step');
            $stepName = (string) $this->input('step_name');

            if (isset($stepMap[$step]) && $stepMap[$step]['name'] !== $stepName) {
                $validator->errors()->add('step_name', 'The step_name value does not match the provided step.');
            }
        });
    }
}
