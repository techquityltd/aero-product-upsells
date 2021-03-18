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
        $fields = collect($content->row)->filter(static function ($_, $key) {
            return Str::startsWith($key, ['upsell:', 'Upsell:']);
        });

        if ($fields->isNotEmpty()) {
            $fields->each(function ($value, $key) use ($content) {
                $related = Product::where('model', $value)->first();

                if ($related) {
                    $groupName = $this->fieldName($key);

                    $group = $this->findOrCreateCollection($groupName);

                    if (! $group->products()->whereHasMorph('parentable', [Product::class, Variant::class])->whereHasMorph('childable', [Product::class, Variant::class])->exists()) {
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
