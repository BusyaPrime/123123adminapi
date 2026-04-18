<?php


namespace App\Domain\Cars\Resources;


use App\Domain\CargoTypes\Resources\CargoTypeResource;
use App\Domain\Cars\Models\Car;
use App\Domain\CarTypes\Resources\CarTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CarResource
 * @package App\Domain\Cars\Resources
 * @mixin Car
 */
class CarResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'car_type_id' => $this->car_type_id,
            'car_type' => $this->whenLoaded('carType', CarTypeResource::make($this->carType)),
            'cargo_types' => $this->whenLoaded('cargoTypes', CargoTypeResource::collection($this->cargoTypes)),
            'loadTypes' => $this->whenLoaded('loadTypes', CargoTypeResource::collection($this->loadTypes)),
            'load_type' => $this->whenLoaded('loadTypes', CargoTypeResource::make($this->loadTypes->first())),
            'model' => $this->model,
            'color' => $this->color,
            'number' => $this->number,
            'trailer_number' => $this->trailer_number,
            'brand' => $this->brand,
            'max_weight' => $this->max_weight,
            'dimension_x' => $this->dimension_x,
            'dimension_y' => $this->dimension_y,
            'dimension_z' => $this->dimension_z,
            'can_pack' => $this->can_pack ? true: false,
            'can_provide_loader' => $this->can_provide_loader ? true: false,
        ];
    }
}
