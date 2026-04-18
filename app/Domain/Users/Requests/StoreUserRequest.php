<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 16:39
 */

namespace App\Domain\Users\Requests;


use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
                'nullable',
                'required_without:email',
                'string',
                'max:255',
                Rule::unique('users')
            ],
            'email' => [
                'nullable',
                'required_without:username',
                'string',
                'max:255',
                Rule::unique('users')
            ],
            'password' => 'required|string|min:6',
            'verified' => 'nullable|boolean',
            'active' => 'nullable|in:'.implode(',', User::statuses()),
        ];
    }
}
