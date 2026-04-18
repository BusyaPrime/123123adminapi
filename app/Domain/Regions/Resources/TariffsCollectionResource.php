<?php


namespace App\Domain\Regions\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class TariffsCollectionResource extends JsonResource
{
    public function toArray($request)
    {
        $arr = $this->resource;
        return [
            'region_from_id' => $arr['region_from_id'],
            'region_to_id' => $arr['region_to_id'],
            'region_from' => $arr['region_from'],
            'region_to' => $arr['region_to'],
            'cars' => $arr['carType']
        ];
    }
}
