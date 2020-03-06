<?php

namespace AeroCrossSelling\Models;

use Aero\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;

class CrossProductCollection extends Model
{
    protected $table = 'cross_product_collections';

    public function products() {
        return $this->hasMany(CrossProduct::class, 'collection_id');
    }
}
