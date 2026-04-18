<?php

namespace App\Domain\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Domain\TruckBookings\Models\TruckBooking;

class CompanyUserResource extends JsonResource{

    public function toArray($request){
        $user = $this->user;
        $allCount = $user->bookings->count();
        $doneCount = $user->bookings->where('status', TruckBooking::STATUS_DONE)->count();
        $processingCount = $user->bookings->whereNotIn('status', [TruckBooking::STATUS_DONE, TruckBooking::STATUS_CANCELED, TruckBooking::STATUS_ORDER])->count();
        $fio = trim($this->name.' '.$this->surname.' '.$this->middle_name);

        return [
            'id' => $user->id,
            'avatar' => $user->profile->imageUrl(),
            'fio' => $fio,
            'phone_number' => $user->username,
            'telegram' => $this->telegram??null,
            'email' => $user->email??null,
            'region_id' => $this->region_id??null,
            'all_orders_count' => $allCount??0,
            'done_orders_count' => $doneCount??0,
            'processing_orders_count' => $processingCount,
            'rating' => (int)$this->rating??0,
            'registered_at' => optional($user->created_at)->format('d.m.Y H:i'),
            'last_seen_at' => optional($user->last_seen_at)->format('d.m.Y H:i'),
            'status' => $user->active,
        ];
    }

}


?>