<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;

class EpcExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return \App\Consignment::with(['company', 'packages', 'packages.items'])->withCount(['items', 'packages'])->find(2);
    }
    
    // public function query()
    // {
    //     return \App\Consignment::with(['company', 'packages', 'packages.items'])->withCount(['items', 'packages'])->find(2);
    // }
}
