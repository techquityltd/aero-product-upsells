<?php

namespace Techquity\ProductUpsells;

use Closure;
use Aero\Catalog\Models\Product;
use Illuminate\Support\Facades\DB;
use Aero\Store\Pipelines\Pipes\Pipe;

class ImportProductUpsells implements Pipe
{
    public function handle($content, Closure $next)
    {
        if (! empty($content->row['Upsell'])) {
            $related = Product::where('model', $content->row['Upsell'])->first();

            if ($related) {
                DB::table('product_collection_product')->updateOrInsert([
                    'collection' => 'upsells',
                    'product_id' => $related->id,
                    'for_product_id' => $content->product->id,
                ], [
                    'sort' => $content->row['Upsell Position'] ?? 0,
                ]);
            }
        }

        return $next($content);
    }
}
