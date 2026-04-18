<?php


namespace App\Domain\CarTypes\Requests;


use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarTypeRequest extends FormRequest
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
            'translations' => ['required', new TranslationsRule()],
            'translations.*.title' => 'max:255',
            'translations.'.\LaravelLocalization::getDefaultLocale().'.title' => 'required|max:255',
            'priority' => 'nullable|integer|min:0',
            'icon' => 'nullable|image|mimes:jpeg,bmp,png,svg',
            'min_distance' => 'nullable|integer|min:0',
            'min_price' => 'nullable|integer|min:0',
            'price_per_km' => 'nullable|integer|min:0',
            'price_per_min' => 'nullable|integer|min:0',
            'commission' => 'nullable|numeric|min:0',
            'max_weight' => 'required|integer|min:0',
            'dimensions' => 'required|json|filled',
            'partial_percentages' => 'nullable',
            
            // 'dimension_x' => 'required|numeric|min:0',
            // 'dimension_y' => 'required|numeric|min:0',
            // 'dimension_z' => 'required|numeric|min:0',
        ];
    }
}
