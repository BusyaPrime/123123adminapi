<?php


namespace App\Domain\UserDeleteRequests\Resources;


use App\Domain\Users\Models\UserProfile;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserProfileResource
 * @package App\Domain\Users\Resources
 * @mixin UserProfile
 */
class UserDeleteRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'phone' => $this->user->username,
            'avatar' => $this->imageUrl(),
            'name' => (string) $this->name ?? '',
            'surname' => (string) $this->surname ?? '',
            'telegram' => $this->telegram,
            'middle_name' => (string) $this->middle_name ?? '',
            'birthday' => $this->birthday,
            'balance' => $this->balance,
            'lang' => $this->language,
            'rating' => $this->rating,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'canCall' => $this->can_call ? true: false,
            'have_terminal' => $this->have_terminal ? true: false,
            'licence_number' => $this->licence_number,
            'car_licence_number' => $this->car_licence_number,
            'licence' => $this->licenceUrl(),
            'car_licence' => $this->carLicenceUrl(),
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company', $this->company),
        ];
    }
}
