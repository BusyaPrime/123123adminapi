<?php


namespace App\Domain\PushNotifications\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'title' => $this->title,
            'read' => $this->read == 1,
            'message' => $this->message,
            'created_at' => optional($this->created_at)->format('d.m.Y H:i:s'),
        ];
    }
}
