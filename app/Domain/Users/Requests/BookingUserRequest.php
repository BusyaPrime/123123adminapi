<?php


namespace App\Domain\Users\Requests;

use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUserRequest extends FormRequest
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
            'active' => 'nullable|in:'.implode(',', User::statuses()),
        ];
    }
}
