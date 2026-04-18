<?php


namespace App\Domain\AdminMessages\Requests;


use Illuminate\Foundation\Http\FormRequest;

class AdminMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required|string',
        ];
    }
}
