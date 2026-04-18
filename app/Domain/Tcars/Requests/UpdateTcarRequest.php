<?php


namespace App\Domain\Tcars\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateTcarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'car_type_id' => 'required|exists:tcar_types,id',
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'peoples' => 'required|integer|min:1',
            'ac' => 'nullable|boolean',
            'kids_seat' => 'nullable|boolean',
        ];
    }
}
