<?php

namespace App\Domain\Transactions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource{

    public function toArray($request){
        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount' => number_format($this->amount, 0, '', ' '),
            'type' => $this->type,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }

}


?>