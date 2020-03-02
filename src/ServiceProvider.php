<?php

namespace Techquity\ProductUpsells;

use Aero\Catalog\Models\Product;
use Aero\Common\Providers\ModuleServiceProvider;
use Aero\DataPort\Commands\Pipelines\ImportProductCSVPipeline;
use Aero\Store\Http\Responses\CartItemAdd;
use Aero\Store\Http\Responses\ProductPage;
use Aero\Store\Twig\Extensions\TwigFunctions;
use Techquity\ProductCollections\ProductCollections;
use Twig\TwigFunction;

class ServiceProvider extends ModuleServiceProvider
{
    public function boot()
    {
        ProductPage::extend(AttachProductUpsells::class);
        CartItemAdd::extend(AddProductUpsells::class);

        ImportProductCSVPipeline::extend(ImportProductUpsells::class);

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
