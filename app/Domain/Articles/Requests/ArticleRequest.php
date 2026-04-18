<?php


namespace App\Domain\Articles\Requests;


use App\Domain\Articles\Models\Article;
use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.short' => 'nullable|string|max:255',
            'translations.*.full' => 'nullable|string',
            'translations.*.link' => 'nullable|string|max:255',
            'translations.'.\LaravelLocalization::getDefaultLocale().'.title' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,bmp,png',
        ];
    }
}
