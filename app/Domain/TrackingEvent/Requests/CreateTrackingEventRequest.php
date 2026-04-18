<?php

namespace App\Domain\TrackingEvent\Requests;


use App\Services\Tracking\TrackingOrderFunnelDefinition;
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $step = $this->input('funnel_step');
            $stepName = $this->input('funnel_name');

            if ($step === null || $stepName === null) {
                return;
            }

            $stepMap = collect(TrackingOrderFunnelDefinition::steps())->keyBy('order');
            $step = (int) $step;

            if (isset($stepMap[$step]) && $stepMap[$step]['name'] !== $stepName) {
                $validator->errors()->add('funnel_name', 'The funnel_name value does not match the provided funnel_step.');
            }
        });
    }
}
