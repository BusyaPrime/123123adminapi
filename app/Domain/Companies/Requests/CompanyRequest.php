<?php


namespace App\Domain\Companies\Requests;


use App\Domain\Companies\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'companyRole' => 'nullable|string|in:'.Company::ROLE_COMPANY.','.Company::ROLE_LOGISTICS,
            'contract_number' => 'nullable|string|max:255',
            'priority_id' => 'nullable|integer',
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
            'commission_rate_id' => 'nullable|string|max:255'
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->filled('companyRole') && $this->filled('role')) {
            $this->merge([
                'companyRole' => $this->input('role'),
            ]);
        }
    }
}
