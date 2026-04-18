<?php


namespace App\Domain\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => 'required|string|max:255',
            'lng' => 'required|string|max:255',
        ];
    }
}
