<?php

namespace App\Domain\Transactions\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionsExport implements FromCollection
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
