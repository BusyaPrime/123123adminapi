<?php


namespace App\Domain\TCarTypes\Resources;


use App\Domain\TCarTypes\Models\TcarType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TCarTypeResource
 * @package App\Domain\TCarTypes\Resources
 * @mixin TcarType
 */
class TcarTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'peoples' => $this->peoples,
            'icon' => $this->imageUrl(),
            'min_distance' => $this->min_distance,
            'min_price' => $this->min_price,
            'price_per_km' => $this->price_per_km,
            'price_per_min' => $this->price_per_min,
            'price' => $this->getPrice(),
        ];
    }
}
