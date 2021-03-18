<?php

namespace AeroCrossSelling\Console\Extensions;

use Aero\Catalog\Models\Product;
use Aero\Catalog\Models\Variant;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Support\Str;

class AddUpsellsFromCSV
{
    public function handle($content): void
    {
        $items = collect($content->row);

        $groupName = $this->fieldName($items->last());

        $product = Product::where('model', $items->model)->first();
        $variant = Variant::where('sku', $items->model)->first();

        $parent = $variant ? $variant : $product;

        $upsellName = 'Upsell:' . $groupName;

        $product = Product::where('model', $items->{$upsellName})->first();
        $variant = Variant::where('sku', $items->{$upsellName})->first();

        $child = $variant ? $variant : $product;

        $group = $this->findOrCreateCollection($groupName);

        $crossProduct = CrossProduct::where('collection_id', $group->id)
            ->where('parentable_id', $parent->id)
            ->where('parentable_type', get_class($parent))
            ->where('childable_id', $child->id)
            ->where('childable_type', get_class($child))
            ->first();

        if(!$crossProduct) {

            //
            $link = $group->products()->make();
            $link->parent()->associate($parent);
            $link->child()->associate($child);
            $link->save();

        }
    }

    /**
     * @param  string  $key
     * @return bool|string
     */
    protected function fieldName(string $key)
    {
        return trim(substr($key, strpos($key, ':') + 1));
    }

    /**
     * @param  string  $name
     * @return \AeroCrossSelling\Models\CrossProductCollection
     */
    protected function findOrCreateCollection(string $name): CrossProductCollection
    {
        $group = CrossProductCollection::where('name', $name)->first();

        if (! $group) {
            $group = new CrossProductCollection();
            $group->name = $name;
            $group->save();
        }

        return $group;
    }
}
