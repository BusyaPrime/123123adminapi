<?php

namespace App\Domain\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class DriverDebtsExport implements FromCollection
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
