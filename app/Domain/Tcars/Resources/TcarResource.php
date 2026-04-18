<?php


namespace App\Domain\Tcars\Resources;


use App\Domain\Tcars\Models\Tcar;
use App\Domain\TCarTypes\Resources\TcarTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TcarResource
 * @package App\Domain\Tcars\Resources
 * @mixin Tcar
 */
class TcarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'car_type_id' => $this->tcar_type_id,
            'car_type' => $this->whenLoaded('carType', TcarTypeResource::make($this->carType)),
            'model' => $this->model,
            'color' => $this->color,
            'number' => $this->number,
            'peoples' => $this->peoples,
            'ac' => $this->ac ? true: false,
            'kids_seat' => $this->kids_seat ? true: false,
        ];
    }
}
