<?php


namespace App\Domain\Users\Resources;


use App\Domain\Users\Models\UserToken;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserTokenResource
 * @package App\Domain\Users\Resources
 * @mixin UserToken
 */
class UserTokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'token' => $this->token,
            'device_info' => $this->device_info,
			/*******05-09-2022******/
            'is_verified' => (int) $this->is_verified,
            'is_external' => $this->is_external,
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'is_agreed_terms' => $this->is_agreed_terms,
			/*************/
            'logged_at' => optional($this->logged_at)->format('d.m.Y H:i:s'),
            'last_seen_at' => optional($this->last_seen_at)->format('d.m.Y H:i:s'),
        ];
    }
}
