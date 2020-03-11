<?php

namespace AeroCrossSelling\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Catalog\Models\Product;
use Aero\Catalog\Models\Variant;
use Aero\Search\Repositories\ElasticProducts;
use AeroCrossSelling\Models\CrossProduct;
use AeroCrossSelling\Models\CrossProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminCrossSellingController extends Controller
{
    /**
     * @var ElasticProducts
     */
    private $products;

    public function __construct(ElasticProducts $products)
    {
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $results = $this->products->search($request->input('q'))->toArray();
        $products = $results['listings'];
        $categories = $results['categories'];
        $searchTerm = $results['search_term'];
        $sortBy = $request->input('sort');
        $filters = Collection::make($results['filters']);
        $appliedFilters = $filters->flatMap(static function ($group) {
            return optional($group->facets)->filter(static function ($facet) {
                return ! empty($facet['applied']);
            });
        });
        $selectedCategory = false;

        if ($filter = $filters->firstWhere('id', 'c')) {
            $filter->facets = $filter->facets->sortByDesc('applied');

            $selectedCategory = $filter->facets->first()['applied'];
        }

        return view('aero-cross-selling-module::admin/index', compact('products', 'searchTerm', 'sortBy', 'filters', 'categories', 'appliedFilters', 'selectedCategory'));
    }

    public function collections(Request $request, $product_id) {
        $product = Product::findOrFail($product_id);
        $links = [];

        $collection = CrossProductCollection::all();

        foreach($collection as $link) {
            $items_linked = CrossProduct::where('parent_id', $product_id)->where('collection_id', $link->id)->count();

            $item = [];
            $item['links'] = $items_linked;
            $item['name'] = $link->name;
            $item['id'] = $link->id;

            array_push($links, $item);
        }

        $sortBy = $request->input('sort');

        return view('aero-cross-selling-module::admin/collections', compact('product', 'links', 'sortBy'));
    }

    public function products(Request $request, $product_id, $collection_id) {
        $collection = CrossProductCollection::findOrFail($collection_id);
        $product = Product::findOrFail($product_id);
        $products = $product->crossProducts($collection);

        $sortBy = $request->input('sort');

        if($request->get('success')) {
            Session::flash('success');
        }

        return view('aero-cross-selling-module::admin/product', compact('product', 'collection', 'products', 'sortBy'));
    }

    public function add_product(Request $request, Product $product, CrossProductCollection $collection)
    {
        return view('aero-cross-selling-module::admin/select_products', compact('product', 'collection'));
    }

    public function getProductsAsJSON(Request $request) {
        $products = Product::all();
        return response($products);
    }

    public function link_products(Request $request) {
        try {
            $product = Product::findOrFail($request->input('product'));
            $collection = CrossProductCollection::findOrFail($request->input('collection'));

            $products = $request->input('products');
            foreach($products as $id) {
                $exists = CrossProduct::where('collection_id', $collection->id)->where('parent_id', $product->id)->where('child_id', $id)->count();
                if(!$exists) {
                    $p = Product::find($id);
                    $link = new CrossProduct();
                    $link->parent_id = $product->id;
                    $link->child_id = $p->id;
                    $link->collection_id = $collection->id;
                    $link->save();
                }
            }

            return response()->json('Success', 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function remove_link($link) {
        try {
            $link = CrossProduct::findOrFail($link);
            $link->delete();

            return redirect()->back();
        } catch (\Exception $err) {
            return redirect()->back();
        }
    }

    public function create_collection(Product $product) {
        return view('aero-cross-selling-module::admin/add_collection', compact('product'));
    }

    public function store_collection(Request $request, Product $product) {
        try {
            $exists = CrossProductCollection::where('name', $request->get('name'))->count();

            if(!$exists) {
                if($request->get('name')) {
                    $collection = new CrossProductCollection();
                    $collection->name = $request->get('name');
                    $collection->save();

                    Session::flash('success');
                    return redirect(route('admin.modules.aero-cross-selling.product', $product));
                } else {
                    Session::flash('error', 'Collection name is missing.');
                    return redirect()->back();
                }
            } else {
                Session::flash('success');
                return redirect(route('admin.modules.aero-cross-selling.product', $product));
            }
        } catch (\Exception $err) {
            Log::error('Error in AdminCrossSellingController@store_collection - ' . $err->getMessage());
            dd($err);
        }
    }
}