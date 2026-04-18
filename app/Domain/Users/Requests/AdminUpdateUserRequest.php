<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 16:56
 */

namespace App\Domain\Users\Requests;


use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateUserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => 'string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
//                Rule::unique('users')->ignore($this->route('user'))
            ],
            'active' => 'nullable|in:'.implode(',', User::statuses()),
            'lang' => 'nullable|in:ru,en,uz',
            'can_call' => 'nullable|boolean',
            'balance' => 'nullable|integer|min:0',
            'have_terminal' => 'nullable|boolean',
            'telegram' => 'nullable|string',
            'email' => 'nullable|string',
            'region_id' => 'nullable|numeric',

        ];
    }
}
