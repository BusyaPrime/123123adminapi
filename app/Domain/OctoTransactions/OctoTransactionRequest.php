<?php

namespace App\Domain\OctoTransactions;

use Illuminate\Foundation\Http\FormRequest;

class OctoTransactionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'total_sum' => 'required|integer'
        ];
    }
}
