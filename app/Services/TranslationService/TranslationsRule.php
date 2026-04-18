<?php

namespace App\Services\TranslationService;

use Illuminate\Contracts\Validation\Rule;
use LaravelLocalization;

class TranslationsRule implements Rule
{
    private $message;

    private $fallbackLocale;

    private $locales = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fallbackLocale = config('app.fallback_locale');

        $this->locales = LaravelLocalization::getSupportedLanguagesKeys();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $locales = [];

        if (!is_array($value)) {
            $this->message = 'The :attribute must be array.';
            return false;
        }

        foreach ($value as $val) {
            if (!isset($val['locale'])) {
                $this->message = 'Each translation element must have the fields `locale`';
                return false;
            }

            if (!in_array($val['locale'], $this->locales)) {
                $this->message = 'The `'.$val['locale'].'` not supported.';
                return false;
            }

            if (in_array($val['locale'], $locales)) {
                $this->message = 'The `locale` field must contain a unique value';
                return false;
            }

            $locales[] = $val['locale'];
        }



        if (!in_array($this->fallbackLocale, $locales)) {
            $this->message = 'In translations must be have `locale` '.$this->fallbackLocale;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

}
