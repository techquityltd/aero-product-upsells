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
        Log::info('Importing Upsells');

        $items = collect($content->row);

        $upsells = $items->filter(static function ($_, $key) {
            return Str::startsWith($key, ['upsell:', 'Upsell:']);
        });

        Log::error('Upsell: ' . print_r($upsells->toArray()));

        $upsellName = $upsells->keys()->first();

        if ($upsells->isNotEmpty()) {

            $groupName = $this->fieldName($upsellName);

            $model = $items->has('Model') ? $items['Model'] : null;
            $sku = $items->has('SKU') ? $items['SKU'] : $model;

            $product = Product::where('model', $model)->first();
            $variant = Variant::where('sku', $sku)->first();

            $parent = $variant ? $variant : $product;

            if (!$parent) {
                Log::error('Missing Parent Product for Upsell Importing', ['model' => $model, 'Sku' => $sku]);
                return;
            }

            foreach (explode(',', $items[$upsellName]) as $upsell) {

                //
                $product = Product::where('model', $upsell)->first();
                $variant = Variant::where('sku', $upsell)->first();

                $child = $variant ? $variant : $product;

                if (!$child) {
                    Log::error('Missing Child Product for Upsell Importing', ['model' => $upsell]);
                    continue;
                }

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
                    $link->parentable_id = $parent->id;
                    $link->parentable_type = get_class($parent);
                    $link->childable_id = $child->id;
                    $link->childable_type = get_class($child);
                    $link->save();

                }

            }

        }

        Log::info('Finished Importing Upsells');
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
