<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 15.04.2019
 * Time: 13:30
 */

namespace App\Domain\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'new_password' => 'required|min:6|confirmed',
            'current_password' => 'required_with:password',
        ];
    }
}
