<?php


namespace App\Domain\CompanyPriorities\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CompanyPriorityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|max:40000000',
        ];
    }
}
