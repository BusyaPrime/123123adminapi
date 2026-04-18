<?php


namespace App\Domain\CancelReasons\Requests;


use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelReasonRequest extends FormRequest
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
            'translations.*.reason' => 'nullable|string|max:255',
            'translations.'.\LaravelLocalization::getDefaultLocale().'.reason' => 'required|string|max:255',
        ];
    }
}
