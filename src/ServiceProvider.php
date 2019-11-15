<?php

namespace Techquity\ProductUpsells;

use Twig\TwigFunction;
use Aero\Catalog\Models\Product;
use Aero\Store\Http\Responses\ProductPage;
use Aero\Store\Http\Responses\CartItemAdd;
use Aero\Store\Twig\Extensions\TwigFunctions;
use Aero\Common\Providers\ModuleServiceProvider;
use Techquity\ProductCollections\ProductCollections;
use Aero\DataPort\Commands\Pipelines\ProductCSVPipeline;

class ServiceProvider extends ModuleServiceProvider
{
    public function boot()
    {
        ProductPage::extend(AttachProductUpsells::class);
        CartItemAdd::extend(AddProductUpsells::class);

        ProductCSVPipeline::add('import', ImportProductUpsells::class);
        
        TwigFunctions::add(new TwigFunction('product_upsells', function (Product $product, $limit = null, $eager = null) {
            return $this->app->make(ProductCollections::class)->handle([
                [
                    'collection' => 'upsells',
                    'product' => $product,
                    'visible_only' => false,
                ],
            ], $limit, $eager);
        }));
    }
}
