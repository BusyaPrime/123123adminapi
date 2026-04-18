<?php


namespace App\Domain\LoadTypes\Resources;


use App\Domain\LoadTypes\Models\LoadType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LoadTypeResource
 * @package App\Domain\LoadTypes\Resources
 * @mixin LoadType
 */
class LoadTypeResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
