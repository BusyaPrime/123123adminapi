<?php

namespace App\Domain\Sizes\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => $this->imageUrl(),
//            'min_distance' => $this->min_distance,
//            'min_price' => $this->min_price,
//            'price_per_km' => $this->price_per_km,
//            'price_per_min' => $this->price_per_min,
//            'price' => $this->getPrice(),
            'dimension_x' => $this->dimension_x,
            'dimension_y' => $this->dimension_y,
            'dimension_z' => $this->dimension_z,
            'max_weight' => $this->max_weight,
            'partial_30' => $this->partial_30??0,
            'partial_50' => $this->partial_50??0,
            'partial_70' => $this->partial_70??0,
        ];
    }
}
