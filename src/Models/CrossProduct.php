<?php

namespace AeroCrossSelling\Models;

use Aero\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;

class CrossProduct extends Model
{
    protected $table = 'cross_products';

    public function parent() {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function child() {
        return $this->belongsTo(Product::class, 'child_id');
    }

    public function collection() {
        return $this->belongsTo(CrossProductCollection::class, 'collection_id');
    }
}
