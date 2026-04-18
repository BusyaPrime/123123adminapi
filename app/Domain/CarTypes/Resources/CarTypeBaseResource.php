<?php


namespace App\Domain\CarTypes\Resources;


use App\Domain\CarTypes\Models\CarType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CarTypeResource
 * @package App\Domain\CarTypes\Resources
 * @mixin CarType
 */
class CarTypeBaseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => $this->imageUrl(),
            'big_icon' => $this->bigImageUrl(),
            'max_weight' => (int) $this->max_weight,
            'partial_percentages' => json_decode($this->partial_percentages, true),
            'time_limit' => (int) $this->load_time_limit,
            'dim_x' => (string) $this->dimension_x,
            'dim_y' => (string) $this->dimension_y,
            'dim_z' => (string) $this->dimension_z,
        ];
    }
}

?>
