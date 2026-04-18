<?php


namespace App\Domain\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationHeadlessRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 'location' => 'required|string',
        ];
    }
}
