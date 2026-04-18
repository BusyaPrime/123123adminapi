<?php


namespace App\Domain\Users\Resources;


use App\Domain\Users\Models\UserProfile;
use App\Domain\Companies\Models\Company;
use App\Domain\CompanyPriorities\Models\CompanyPriority;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserProfileResource
 * @package App\Domain\Users\Resources
 * @mixin UserProfile
 */
class UserProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $companyName = '';
        $company = null;
        $companyLimit = 0;
        $companyBalance = 0;
        $companyStatus = null;
        $corporativeName = null;
        $user = $this->user;

        if ($this->company_id) {
            $company = Company::with(['user', 'priority'])->find($this->company_id);
            $companyName = $company ? (string) $company->title : '';
        }

        if ($user && method_exists($user, 'resolveClientCompany')) {
            $resolvedCompany = $user->resolveClientCompany(['user', 'priority']);

            if ($resolvedCompany instanceof Company) {
                $company = $resolvedCompany;
            }
        }

        if ($user && (int) $user->is_external === 1) {
            $corporativeName = $company ? (string) $company->title : null;
        }

        if ($company) {
            $companyPriority = $company->relationLoaded('priority')
                ? $company->priority
                : CompanyPriority::find($company->priority_id);

            if ($companyPriority) {
                $companyLimit = (int) $companyPriority->quantity;
            }

            $companyBalance = (int) $company->balance;
            $companyStatus = optional($company->user)->active;
        }

        return [
            'user_id' => $this->user_id,
            'phone' => optional($user)->username,
            'email' => $this->email??'',
            'avatar' => $this->imageUrl(),
            'name' => (string) $this->name ?? '',
            'surname' => (string) $this->surname ?? '',
            'telegram' => $this->telegram??'',
            'middle_name' => (string) $this->middle_name ?? '',
            'birthday' => $this->birthday,
            'balance' => $this->balance??0,
            'lang' => $this->language,
            'rating' => $this->rating??'0',
            'lat' => $this->lat,
            'lng' => $this->lng,
            'canCall' => $this->can_call ? true: false,
            'have_terminal' => $this->have_terminal ? true: false,
            'licence_number' => $this->licence_number,
            'car_licence_number' => $this->car_licence_number,
            'licence' => $this->licenceUrl(),
            'car_licence' => $this->carLicenceUrl(),
            'company_id' => $this->company_id??null,
            'company_balance' => (int) ($companyBalance + $companyLimit),
            'balance_limit' => (int) $companyLimit,
            'is_external' => (int) optional($user)->is_external,
            'phone_number' => (string) $this->phone_number,
            'company_name' => $companyName,
            'company' => $this->whenLoaded('company', $this->company),
            'corporative_name' => $corporativeName,
            'active' => $companyStatus ?? optional($user)->active,
        ];
    }
}
