<?php


namespace App\Domain\Users\Resources;


use App\Domain\Users\Models\UserCode;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserCodeResource
 * @package App\Domain\Users\Resources
 * @mixin UserCode
 */
class UserCodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user_id' => (int) $this->user_id,
            // 'code' => \App::isLocal() ? $this->code: null
        ];
    }
}
