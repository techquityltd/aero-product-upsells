<?php

namespace AeroCrossSelling\Exports;

use AeroCrossSelling\Models\CrossProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LinksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = array_keys($collection);
    }

    public function collection()
    {
        return CrossProduct::whereIn('collection_id', $this->collection)->get();
    }

    public function map($row): array
    {
        return [
            $row->collection_id,
            $row->parent_id,
            $row->child_id,
            $row->sort,
        ];
    }

    public function headings(): array
    {
        return [
            'collection_id',
            'parent_id',
            'child_id',
            'sort'
        ];
    }
}
