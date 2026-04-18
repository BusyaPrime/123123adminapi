<?php


namespace App\Domain\Regions\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'polygon' => $this->polygon ? json_decode($this->polygon): null,
        ];
    }
}
