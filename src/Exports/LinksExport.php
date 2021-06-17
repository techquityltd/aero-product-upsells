<?php

namespace AeroCrossSelling\Exports;

use AeroCrossSelling\Models\CrossProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LinksExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    protected $collection;
    protected $model;
    protected $search;

    public function __construct($collection, $model, $parent, $child)
    {
        $this->collection = array_keys($collection);
        $this->model = $model;
        $this->parent = collect(explode('|', $parent))->filter()->unique();
        $this->child = collect(explode('|', $child))->filter()->unique();
    }

    public function collection()
    {
        $crossSell = CrossProduct::query();

        if ($this->parent->count()) {
            $crossSell->whereHas('parent', function ($query) {
                $query->whereHas('variants', function ($query) {
                    $query->whereIn('sku', $this->parent);
                });
            });
        }

        if ($this->child->count()) {
            $crossSell->whereHas('child', function ($query) {
                $query->whereHas('variants', function ($query) {
                    $query->whereIn('sku', $this->child);
                });
            });
        }

        return $crossSell->whereIn('collection_id', $this->collection)->get();
    }

    public function map($row): array
    {
        if ($row->parent && $row->child) {
            return [
                $row->collection_id,
                $row->parent->variants()->first()->sku,
                $row->child->variants()->first()->sku,
                $row->sort,
            ];
        }

        return [];
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
