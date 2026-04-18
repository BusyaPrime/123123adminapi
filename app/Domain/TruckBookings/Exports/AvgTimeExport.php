<?php

namespace App\Domain\TruckBookings\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class AvgTimeExport implements FromCollection, WithTitle
{
    protected $sheetTitle;
    protected $items;

    public function __construct($items, $sheetTitle)
    {
        $this->sheetTitle = $sheetTitle;
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
