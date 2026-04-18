<?php


namespace App\Domain\Tickets\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required_without:user_name|exists:users,id',
            'user_name' => 'required_without:user_id|string|max:255',
            'user_type' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'text' => 'required|string',
            'file' => 'nullable|image',
        ];
    }
}
