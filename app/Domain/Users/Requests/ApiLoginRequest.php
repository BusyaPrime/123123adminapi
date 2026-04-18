<?php


namespace App\Domain\Users\Requests;


use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ApiLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:/^998[0-9]{9}$/i'
            ],
            'role' => [
                'nullable',
                'in:'.implode(',', User::apiRoles()),
            ],
        ];
    }
}
