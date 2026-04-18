<?php


namespace App\Domain\TCarTypes\Requests;


use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;

class TcarTypeRequest extends FormRequest
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
            'poster_logo' => 'nullable|image|mimes:jpeg,bmp,png,svg',
            'min_distance' => 'nullable|integer|min:0',
            'min_price' => 'nullable|integer|min:0',
            'peoples' => 'nullable|integer|min:0',
            'price_per_km' => 'nullable|integer|min:0',
            'price_per_min' => 'nullable|integer|min:0',
            'commission' => 'nullable|numeric|min:0',
        ];
    }
}
