<?php

namespace AeroCrossSelling\Models;

use Aero\Catalog\Models\Category;
use Aero\Catalog\Models\Manufacturer;
use Aero\Catalog\Models\Product;
use Aero\Catalog\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CrossProductsPreset extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label', 'product_serialized', 'recommends_serialized'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function recommended()
    {
        return $this->belongsToMany(
            Product::class,
            'cross_products_preset_recommended',
            'cross_products_preset_id',
            'recommended_id',

        );
    }

    // public function recommended()
    // {
    //     return $this->hasMany(CrossProductsPresetRecommended::class, 'preset_id');
    // }

    /**
     * Get the products 
     */
    // public function scopeAvailable($query, $product, $limit = 10)
    // {
    //     return $query->get()->filter(function ($preset) use ($product) {

    //         return (bool) $preset->getProducts()->where('id', $product)->exists();

    //     })->map(function ($preset) use ($limit) {

    //         return $preset->getRecommended()->limit($limit);

    //     })->first();
    // }

    public function getProductsDeserializedAttribute()
    {
        return $this->unserialize($this->product_serialized);
    }

    public function getRecommendsDeserializedAttribute()
    {
        return $this->unserialize($this->recommends_serialized);
    }

    public function setProductsDeserializedAttribute($value)
    {
        $this->attributes['product_serialized'] = $this->serialize($value);
    }

    public function setRecommendsDeserializedAttribute($value)
    {
        $this->attributes['recommends_serialized'] = $this->serialize($value);
    }

    protected function serialize(array $collection): string
    {
        return $collection = collect($collection)->filter(function ($resource) {
            return (bool) collect($resource)->whereNotNull('value')->count();
        })->mapWithKeys(function ($items, $resource) {
            return [$resource => collect($items)->pluck('value')->filter()->implode('|')];
        })->map(function ($items, $resource) {
            return $resource . ':' . $items;
        })->filter()->implode(';');
    }

    protected function unserialize($string)
    {
        $resources = explode(';', $string);

        return collect($resources)->mapWithKeys(function ($resource) {
            $split = explode(':', $resource);
            $items = explode('|', $split[1]);

            switch ($split[0]) {
                case 'products':
                    $item = Product::whereIn('id', $items)->get()->map(function ($product) {
                        return [
                            'value' => $product->id,
                            'group' => $product->model,
                            'name' => $product->name,
                        ];
                    });
                    break;
                case 'manufacturers':
                    $item = Manufacturer::whereIn('id', $items)->get()->map(function ($manufacturer) {
                        return [
                            'value' => $manufacturer->id,
                            'name' => $manufacturer->name,
                        ];
                    });
                    break;
                case 'categories':
                    $item = Category::whereIn('id', $items)->get()->map(function ($category) {
                        return [
                            'value' => $category->id,
                            'name' => $category->name,
                        ];
                    });
                    break;
                case 'tags':
                    $item = Tag::with('group')->whereIn('id', $items)->get()->map(function ($tag) {
                        return [
                            'value' => $tag->id,
                            'name' => $tag->name,
                            'group' => $tag->group->name
                        ];
                    });
                    break;
            }

            return [$split[0] => $item];
        });
    }

    public function getProducts()
    {
        $query = Product::query();

        if (isset($this->products_deserialized['categories'])) {
            $query->whereHas('categories', function ($query) {
                $query->whereIn('id', $this->products_deserialized['categories']->pluck('value'));
            });
        }

        if (isset($this->products_deserialized['manufacturers'])) {
            $query->whereHas('manufacturer', function ($query) {
                $query->whereIn('id', $this->products_deserialized['manufacturers']->pluck('value'));
            });
        }

        if (isset($this->products_deserialized['tags'])) {
            $query->whereHas('tags', function ($query) {
                $query->whereIn('id', $this->products_deserialized['tags']->pluck('value'));
            });
        }

        if (isset($this->products_deserialized['products'])) {
            $query->whereIn('id', $this->products_deserialized['products']->pluck('value'));
        }

        return $query;
    }

    public function getRecommended()
    {
        $query = Product::query();

        if (isset($this->recommends_deserialized['categories'])) {
            $query->whereHas('categories', function ($query) {
                $query->whereIn('id', $this->recommends_deserialized['categories']->pluck('value'));
            });
        }

        if (isset($this->recommends_deserialized['manufacturers'])) {
            $query->whereHas('manufacturer', function ($query) {
                $query->whereIn('id', $this->recommends_deserialized['manufacturers']->pluck('value'));
            });
        }

        if (isset($this->recommends_deserialized['tags'])) {
            $query->whereHas('tags', function ($query) {
                $query->whereIn('id', $this->recommends_deserialized['tags']->pluck('value'));
            });
        }

        if (isset($this->recommends_deserialized['products'])) {
            $query->whereIn('id', $this->recommends_deserialized['products']->pluck('value'));
        }

        return $query;
    }
}
