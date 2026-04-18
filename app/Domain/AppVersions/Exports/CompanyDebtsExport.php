<?php

namespace App\Domain\AppVersions\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CompanyDebtsExport implements FromCollection
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
