<?php

namespace App\Domain\Transactions\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransactionsImport implements ToCollection
{
    use Importable;
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row[2]) && $row[2] != '' && $row[2] != 'ИНН') {
                collect(['date' => $row[0] ?? date('d.m.Y'), 'inn' => $row[2] ?? '', 'sum' => $row[7] ?? 0, 'description' => $row[9]] ?? '');
            }
        }
    }
}
