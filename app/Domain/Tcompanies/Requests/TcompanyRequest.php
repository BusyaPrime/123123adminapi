<?php


namespace App\Domain\Tcompanies\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TcompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'contract_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phones' => 'nullable|array',
            'emails' => 'nullable|array',

            'company_name' => 'nullable|string|max:255',
            'company_city' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'post_index' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'oked' => 'nullable|string|max:255',
            'mfo' => 'nullable|string|max:255',
            'inn' => 'nullable|string|max:255',
            'okonh' => 'nullable|string|max:255',
        ];
    }
}
