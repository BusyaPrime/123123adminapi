<?php

namespace App\Domain\Sizes\Requests;

use App\Services\TranslationService\TranslationsRule;
use Illuminate\Foundation\Http\FormRequest;

class SizeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'translations' => ['required', new TranslationsRule()],
            'translations.*.title' => 'max:255',
            'translations.'.\LaravelLocalization::getDefaultLocale().'.title' => 'required|max:255',
            'priority' => 'nullable|integer|min:0',
            'icon' => 'nullable|image|mimes:jpeg,bmp,png,svg',
            'dimension_x' => 'required|numeric|min:0',
            'dimension_y' => 'required|numeric|min:0',
            'dimension_z' => 'required|numeric|min:0',
        ];
    }
}
