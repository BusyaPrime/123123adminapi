<?php

namespace App\Domain\TicketThemes\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketThemesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
