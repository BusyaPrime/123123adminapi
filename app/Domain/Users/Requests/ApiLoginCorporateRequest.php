<?php


namespace App\Domain\Users\Requests;


use App\Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ApiLoginCorporateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'username' => [
                'required'
            ],
            'password' => [
                'required'
            ],
        ];
    }
}
