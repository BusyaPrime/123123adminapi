<?php


namespace App\Domain\CarTypes\Resources;


use App\Domain\CarTypes\Models\CarType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CarTypeResource
 * @package App\Domain\CarTypes\Resources
 * @mixin CarType
 */
class CarTypeResource extends JsonResource
{
    public function toArray($request)
    {
        $regionFrom = $request->input('region_from_id');
        $regionTo = $request->input('region_to_id');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => $this->imageUrl(),
            'big_icon' => $this->bigImageUrl(),
            'price' => $this->calculatedPrice,
            'not_full_enabled' => $this->notFullEnabled??false,
            'not_full_percentage' => $this->notFullEnabledPercentage,
            'not_full_price' => $this->calculatedPriceNotFull,
            'max_weight' => $this->max_weight,
            'is_available' => ($regionFrom !== $regionTo && $this->is_multi_region === 1) || $regionFrom === $regionTo,
            'time_limit' => $this->load_time_limit,
            'dim_x' => (string) $this->dimension_x,
            'dim_y' => (string) $this->dimension_y,
            'dim_z' => (string) $this->dimension_z,
        ];
    }
}

?>
