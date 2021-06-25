<?php

namespace AeroCrossSelling\Imports;

use Aero\Catalog\Models\Product;
use Aero\Catalog\Models\Variant;
use AeroCrossSelling\Models\CrossProduct;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinksImport implements ToCollection, WithHeadingRow
{
    protected $unlink;

    public function __construct($unlink)
    {
        $this->unlink = (bool) $unlink;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $collection->each(function ($row) {
            $parent = $this->findProduct($row, 'parent_id');
            $child = $this->findProduct($row, 'child_id');
            $collection = CrossProductCollection::find($row['collection_id']);

            if ($child && $parent && $collection) {

                if ($this->unlink) {
                    CrossProduct::where('parent_id', $parent->id)->delete();
                }

                CrossProduct::firstOrCreate([
                    'collection_id' => $collection->id,
                    'parent_id' => $parent->id,
                    'child_id' => $child->id,
                    'sort' => $row['sort'],
                ]);
            } else {
                dd($row);
            }
        });
    }

    protected function findProduct($row, $reference)
    {
        // Check Model
        if ($model = Product::where('model', $row[$reference])->first()) {
            return $model;
        }

        // Check SKU
        if ($sku = Variant::with('product')->where('sku', $row[$reference])->first()) {
            return $sku->product;
        }

        // Check ID
        if ($id = Product::find($row[$reference])) {
            return $id->first();
        }
    }
}
