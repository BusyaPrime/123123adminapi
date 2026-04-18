<?php

namespace App\Domain\CommissionRate\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'commission' => 'required|numeric',
        ];
    }
}
