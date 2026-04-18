<?php


namespace App\Domain\Messages\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'message' => 'required|string',
        ];
    }
}
