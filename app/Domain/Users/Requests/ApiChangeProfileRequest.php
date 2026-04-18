<?php


namespace App\Domain\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ApiChangeProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'lang' => 'nullable|in:ru,en,uz',
            'can_call' => 'nullable|boolean',
            'have_terminal' => 'nullable|boolean',
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg,bmp',
            'avatar' => 'nullable|mimes:jpeg,png,jpg,bmp',
        ];
    }
}
