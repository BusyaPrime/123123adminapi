<?php

namespace App\Domain\TruckBookings\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $acceptTime;
    protected $loadTime;
    protected $items;

    public function __construct($acceptTime, $loadTime, $items)
    {
        $this->acceptTime = $acceptTime;
        $this->loadTime = $loadTime;
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new AvgTimeExport($this->acceptTime, 'Среднее время принятия заказа');
        $sheets[] = new AvgTimeExport($this->loadTime, 'Среднее время погрузки и разгрузки');
        $sheets[] = new AvgTimeExport($this->items, 'Заказы');

        return $sheets;
    }
}
