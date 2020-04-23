<?php

namespace AeroCrossSelling\Console\Extensions;

use Aero\Catalog\Models\Product;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Support\Str;

class AddUpsellsFromCSV
{
    public function handle($content): void
    {
        $fields = collect($content->row)->filter(static function ($_, $key) {
            return Str::startsWith($key, ['upsell:', 'Upsell:']);
        });

        if ($fields->isNotEmpty()) {
            $fields->each(function ($value, $key) use ($content) {
                $related = Product::where('model', $value)->first();

                if ($related) {
                    $groupName = $this->fieldName($key);

                    $group = $this->findOrCreateCollection($groupName);

                    if (! $group->products()->whereHas('parent', static function ($query) use ($content) {
                        $query->where('id', $content->product->id);
                    })->whereHas('child', static function ($query) use ($related) {
                        $query->where('id', $related->id);
                    })->exists()) {
                        $link = $group->products()->make();
                        $link->parent()->associate($content->product);
                        $link->child()->associate($related);
                        $link->save();
                    }
                }
            });
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
