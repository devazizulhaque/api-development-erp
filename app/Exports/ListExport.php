<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ListExport implements FromCollection
{
    protected $list;
    protected $columns;

    public function __construct($list, $columns)
    {
        $this->list = $list;
        $this->columns = $columns;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->list);

    }

    public function headings(): array
    {
        return $this->columns;
    }
}
