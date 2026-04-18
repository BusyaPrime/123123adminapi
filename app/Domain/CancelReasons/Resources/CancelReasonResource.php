<?php

namespace App\Domain\CancelReasons\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class CancelReasonResource extends JsonResource
{
    public function toArray($request)
    {
        if($this->reason == null){
            $attrs = parent::getTranslation(\App::getLocale());
            $this->reason = $attrs->reason;
        }

        return [
            'id' => $this->id,
            'reason' => $this->reason
        ];
    }
}
