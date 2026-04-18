<?php


namespace App\Domain\Tickets\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'nullable|string',
            'admin_comment' => 'nullable|string',
        ];
    }
}
