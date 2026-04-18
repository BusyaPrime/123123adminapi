<?php

namespace App\Domain\Regions\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class TariffsExport implements FromCollection
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
}
