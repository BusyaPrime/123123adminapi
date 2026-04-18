<?php


namespace App\Domain\CargoTypes\Resources;


use App\Domain\CargoTypes\Models\CargoType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CargoTypeResource
 * @package App\Domain\CargoTypes\Resources
 * @mixin CargoType
 */
class CargoTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
