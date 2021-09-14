<?php

namespace AeroCrossSelling\Providers;

use Aero\Admin\AdminModule;
use Aero\Common\Providers\ModuleServiceProvider;
use AeroCrossSelling\Models\CrossProductCollection;
use AeroCrossSelling\Observers\ProductObserver;
use Illuminate\Routing\Router;
use Aero\Catalog\Models\Product;
use Aero\Catalog\Models\Variant;
use Aero\DataPort\Commands\Pipelines\ImportProductCSVPipeline;
use Aero\Store\Http\Responses\ProductPage;
use Aero\Store\Http\Responses\CartItemAdd;
use AeroCrossSelling\Console\Extensions\AddUpsellsFromCSV;
use AeroCrossSelling\Models\CrossProduct;
use AeroCrossSelling\Http\Extensions\AddProductUpsells;
use AeroCrossSelling\Http\Extensions\AttachProductUpsells;
use NumberFormatter;
use Twig\TwigFunction;
use Aero\Store\Twig\Extensions\TwigFunctions;
use AeroCrossSelling\Models\CrossProductsPreset;
use AeroCrossSelling\Models\CrossProductsPresetMapping;

class ServiceProvider extends ModuleServiceProvider
{
    public function register()
    {
        // Autoload the config without needing to publish - remove if not needed.
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php',
            'aero-product-upsells'
        );
    }

    public function boot()
    {
        parent::boot();

        // dd(
        //     CrossProductsPresetMapping::whereHas('products', function ($query) {
        //         $query->where('id', 9258);
        //     })->count()
        // );

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'aero-product-upsells');

        $this->loadRoutes();

        $this->publishAssets('aero-product-upsells');
        $this->publishViews('aero-product-upsells');
        $this->publishConfig('aero-product-upsells');
        $this->publishMigrations('aero-product-upsells');

        Product::observe(ProductObserver::class);
        ProductPage::extend(AttachProductUpsells::class);
        CartItemAdd::extend(AddProductUpsells::class);

        ImportProductCSVPipeline::extend(AddUpsellsFromCSV::class);

        AdminModule::create('aero-product-upsells')
            ->title('Cross-sell products')
            ->summary('Link products together in order to add them to cross-sell.')
            ->route('admin.modules.aero-cross-selling.presets.index');

        /**
         * This adds a crossProducts function on the product model, allowing us to get the child products linked to the current product within that collection
         * So for example, we could get all products linked to our parent via colour, size, cross-sell etc.
         */
        Product::macro('crossProducts', function ($collection = null, $limit = 10) {

            if (!$limit) {
                $limit = 10;
            }

            $query = CrossProduct::where('id', '>', 0);

            if ($collection) {
                $query = $collection->products();
            }

            if ($limit) {
                $query->limit($limit);
            }

            $default = $query->where('parent_id', $this->id)->whereHas('child')->orderBy('sort', 'asc')->get()->map(function ($p) {
                $child = $p->child;
                $child->cross_id = $p->id;
                return $child;
            });

            /**
             * Upsells update
             */
            $presets = CrossProductsPreset::whereHas('products', function ($query) {
                $query->where('product_id', $this->id);
            })->get();

            $limit = ($limit - $default->count() > 0) ? $limit - $default->count() : 0;

            return $default->merge(
                Product::query()
                    ->visible()
                    ->whereIn('cross_products_preset_recommended.cross_products_preset_id', $presets->pluck('id'))
                    ->join('cross_products_preset_recommended', 'recommended_id', 'products.id')
                    ->select('products.*')
                    ->limit($limit)
                    ->get()
            );
        });

        /**
         * This allows you to display the sale price.
         */
        TwigFunctions::add(new TwigFunction('crossProduct_currency', function (float $value) {
            $formatter = new \NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($value / 100, 'GBP');
        }));

        // public function scopeWithStockLevel(Builder $query)
        // {
        //     $variants = $this->variants()->getRelated();

        //     return $query->selectSub($variants
        //         ->whereColumn($this->getForeignKey(), $this->getQualifiedKeyName())
        //         ->selectRaw("sum({$variants->qualifyColumn('stock_level')})")
        //         ->getQuery(), 'stock_level');
        // }

        /**
         * This adds a crossProductCollections function on the product model, allowing us to get the child products linked to the current product within that collection
         * So for example, we could get all products linked to our parent via colour, size, cross-sell etc.
         */
        Product::macro('crossProductCollections', function () {
            $query = CrossProduct::where('id', '>', 0);
            return $query->where('parent_id', $this->id)->get()->map(function ($link) {
                return $link->collection;
            });
        });

        TwigFunctions::add(new TwigFunction('cross_products', function (Product $product, $collection, $limit = null) {
            $collection = CrossProductCollection::where('name', $collection)->first();
            return $product->crossProducts($collection, $limit);
        }));
    }

    private function publishAssets(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../resources/css' => public_path("vendor/{$name}/css"),
        ], $name);

        $this->publishes([
            __DIR__ . '/../../resources/js' => public_path("vendor/{$name}/js"),
        ], $name);
    }

    private function publishViews(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path("views/vendor/{$name}"),
        ], $name);
    }

    private function publishMigrations(string $name)
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => base_path('/database/migrations'),
        ], $name);
    }

    private function publishConfig(string $name)
    {
        $this->publishes([
            __DIR__ . "/../../config/config.php" => base_path("config/{$name}.php"),
        ], $name);
    }

    private function loadRoutes()
    {
        Router::addStoreRoutes(__DIR__ . '/../../routes/store.php');
        Router::addAdminRoutes(__DIR__ . '/../../routes/admin.php');
    }
}
