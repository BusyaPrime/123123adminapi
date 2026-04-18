<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 29.04.2019
 * Time: 1:35
 */

namespace App\Domain\Users\Resources;


use App\Domain\Users\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Domain\Users\Resources
 * @mixin User
 */
class UserResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'created_at' => optional($this->created_at)->format('d.m.Y H:i:s'),
        ];
    }
}
