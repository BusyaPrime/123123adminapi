<?php

namespace App\Domain\Companies\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Resources\UserProfileResource;
use App\Domain\Users\Resources\UserResource;
use App\Domain\Transactions\Resources\TransactionResource;
use App\Domain\CompanyPriorities\Resources\CompanyPriorityResource;

class CompanyResource extends JsonResource{

    public function toArray($request){

        return [
            'id' => $this->id,
            'copmany_name' => $this->title,
            'address' => $this->company_address,
            'bank' => $this->bank,
            'bank_account' => $this->bank_account,
            'inn' => $this->inn,
            'mfo' => $this->mfo,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            'balance' => number_format($this->balance, 0, '', ' ') ?? 0,
            'rating' => $this->rating??0,
            'logo' => $this->imageUrl('logo'),
            'order_counts' => $this->order_counts,
            'is_agreed_terms' => $this->is_agreed_terms ?? false,

            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user->profile)),
            'users_count' => $this->whenLoaded('users', $this->users->count()),
            'transactions' => $this->whenLoaded('transactions', TransactionResource::collection($this->transactions)),
            'priority' => $this->whenLoaded('priority', CompanyPriorityResource::make($this->priority)),
        ];
    }

}


?>