<?php


namespace App\Domain\Tickets\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'user_name' => $this->user_name,
            'subject' => $this->subject,
            'text' => $this->text,
            'status' => $this->status,
            'admin_comment' => $this->admin_comment,
        ];
    }
}
