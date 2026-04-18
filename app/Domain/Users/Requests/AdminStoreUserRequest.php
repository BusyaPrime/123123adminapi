<?php


namespace App\Domain\Users\Requests;

use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
//                Rule::unique('users')
            ],
            'middle_name' => 'nullable|string',
            'active' => 'nullable|in:'.implode(',', User::statuses()),
            'lang' => 'nullable|in:ru,en,uz',
            'can_call' => 'nullable|boolean',
            'have_terminal' => 'nullable|boolean',
            'balance' => 'nullable|integer|min:0',
        ];
    }
}
