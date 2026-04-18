<?php


namespace App\Domain\AdminMessages\Resources;


use App\Domain\Users\Resources\UserProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMessageResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'message' => $this->message,
            'user_id' => $this->user_id,
            'read' => $this->read == 1,
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
