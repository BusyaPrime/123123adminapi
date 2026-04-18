<?php

namespace App\Domain\TruckBookings\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class BookingsExport implements FromCollection
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function toArray()
    {
        return $this->items;
    }
}
