<?php


namespace App\Domain\LoadTypes\Requests;


use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;

class LoadTypeRequest extends FormRequest
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
        ];
    }
}
