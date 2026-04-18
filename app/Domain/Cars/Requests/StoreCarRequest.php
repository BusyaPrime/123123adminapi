<?php


namespace App\Domain\Cars\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'car_type_id' => 'required|exists:car_types,id',
            'company_id' => 'nullable|exists:companies,id',
            'model' => 'required|string|max:255',
            // 'brand' => 'required|string|max:255',
            'number' => 'required|string|max:255',
//            'max_weight' => 'required|integer|min:0',
//            'dimension_x' => 'required|numeric|min:0',
//            'dimension_y' => 'required|numeric|min:0',
//            'dimension_z' => 'required|numeric|min:0',
            'can_pack' => 'nullable|boolean',
            'can_provide_loader' => 'nullable|boolean',
            // 'load_type' => 'required|exists:load_types,id',
            'cargo_types' => 'nullable|array',
            'cargo_types.*' => 'nullable|exists:cargo_types,id',
            'active' => 'nullable|boolean',
        ];
    }
}
