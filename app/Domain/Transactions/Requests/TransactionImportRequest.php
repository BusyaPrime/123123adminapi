<?php

namespace App\Domain\Transactions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionImportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'file' => 'file|mimes:xls,xlsx'
        ];
    }
}
