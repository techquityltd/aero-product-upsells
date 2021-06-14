<?php

namespace AeroCrossSelling\Imports;

use Aero\Catalog\Models\Variant;
use AeroCrossSelling\Models\CrossProduct;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinksImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $collection->each(function ($row) {
            $parent = Variant::with('product')->where('sku', $row['parent_id'])->first();
            $child = Variant::with('product')->where('sku', $row['child_id'])->first();
            $collection = CrossProductCollection::find($row['collection_id']);

            if ($child && $parent && $collection) {
                CrossProduct::firstOrCreate([
                    'collection_id' => $collection->id,
                    'parent_id' => $parent->product->id,
                    'child_id' => $child->product->id,
                    'sort' => $row['sort'],
                ]);
            }
        });
    }
}
