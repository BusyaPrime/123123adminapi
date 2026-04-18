<?php

namespace App\Domain\CompanyPriorities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Resources\UserProfileResource;
use App\Domain\Users\Resources\UserResource;
use App\Domain\Transactions\Resources\TransactionResource;

class CompanyPriorityResource extends JsonResource{

    public function toArray($request){

        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => number_format($this->quantity, 0,'', ' ')
        ];
    }

}


?>