<?php

namespace AeroCrossSelling\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Catalog\Models\Product;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Http\Request;


class CrossSellingController extends Controller
{
    public function collections(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);
        $collection = CrossProductCollection::where('name', $request->collection)->first();
        return $product->crossProducts($collection, $request->limit);
    }
}