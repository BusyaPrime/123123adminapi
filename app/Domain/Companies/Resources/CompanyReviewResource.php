<?php

namespace App\Domain\Companies\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'director' => $this->director,
            'director_role' => $this->director_role,
            'logo' => $this->logo ? $this->imageUrl('logo') : null,
            'review' => $this->review,
        ];
    }
}
