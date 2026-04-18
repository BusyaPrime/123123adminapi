<?php


namespace App\Domain\Cars\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'car_type_id' => 'required|exists:car_types,id',
            'model' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'number' => 'required|string|max:255',
//            'max_weight' => 'required|integer|min:0',
//            'dimension_x' => 'required|numeric|min:0',
//            'dimension_y' => 'required|numeric|min:0',
//            'dimension_z' => 'required|numeric|min:0',
            'can_pack' => 'nullable|boolean',
            'can_provide_loader' => 'nullable|boolean',
            'load_type' => 'nullable|exists:load_types,id',
            'cargo_types' => 'nullable|array',
            'cargo_types.*' => 'nullable|exists:cargo_types,id',
        ];
    }
}
