<?php

namespace AeroCrossSelling\Observers;

use Aero\Catalog\Models\Variant;
use AeroCrossSelling\Models\CrossProduct;

class VariantObserver
{
    /**
     * Handle the product "deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        // Delete any instances where this variant is used in upselling as it doesn't exist anymore
        CrossProduct::where('childable_id', $product->id)->where('childable_type', '\Aero\Catalog\Models\Variant')->delete();
    }
}
