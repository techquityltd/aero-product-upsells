<?php

namespace AeroCrossSelling\Observers;

use Aero\Catalog\Models\Product;
use AeroCrossSelling\Models\CrossProduct;

class ProductObserver
{
    /**
     * Handle the product "deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        // Delete any instances where this product is used in upselling as it doesn't exist anymore
        CrossProduct::where('child_id', $product->id)->delete();
    }
}
