<?php

namespace App\Domain\TrackingSession\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TrackingSessionRequest extends FormRequest
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
            'device_info' => 'required|required',
            'device_info.app_version' => 'required|string|max:255',
            'device_info.os' => 'nullable|string|max:255',
            'device_info.device_id' => 'nullable|string|max:255',
            'device_info.device_model' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'screen' => 'required|string|max:255'
        ];
    }
}
