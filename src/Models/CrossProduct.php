<?php

namespace AeroCrossSelling\Models;

use Aero\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;

class CrossProduct extends Model
{
    protected $table = 'cross_products';

    public function parent() {
        return $this->parentable();
    }

    public function child() {
        return $this->childable();
    }

    public function parentable() {
        return $this->morphTo();
    }

    public function childable() {
        return $this->morphTo();
    }

    public function collection() {
        return $this->belongsTo(CrossProductCollection::class, 'collection_id');
    }
}
