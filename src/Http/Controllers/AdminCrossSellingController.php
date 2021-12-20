<?php

namespace AeroCrossSelling\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Catalog\Models\Product;
use Aero\Search\Contracts\ProductRepository;
use AeroCrossSelling\Exports\LinksExport;
use AeroCrossSelling\Imports\LinksImport;
use AeroCrossSelling\Jobs\MarkDownloadAsComplete;
use AeroCrossSelling\Models\CrossProduct;
use AeroCrossSelling\Models\CrossProductCollection;
use AeroCrossSelling\Models\CrossProductDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AdminCrossSellingController extends Controller
{
    /**
     * @var \Aero\Search\Contracts\ProductRepository
     */
    private $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $results = $this->products
            ->search($request->input('q'))
            ->apply($request->query)
            ->paginate()
            ->toArray();
        $products = $results['listings'];
        $categories = $results['categories'];
        $searchTerm = $results['search_term'];
        $sortBy = $request->input('sort');
        $filters = Collection::make($results['filters']);
        $appliedFilters = $filters->flatMap(static function ($group) {
            return optional($group->facets)->filter(static function ($facet) {
                return !empty($facet['applied']);
            });
        });
        $selectedCategory = false;

        if ($filter = $filters->firstWhere('id', 'c')) {
            $filter->facets = $filter->facets->sortByDesc('applied');

            $selectedCategory = $filter->facets->first()['applied'];
        }

        return view('aero-product-upsells::admin/index', compact('products', 'searchTerm', 'sortBy', 'filters', 'categories', 'appliedFilters', 'selectedCategory'));
    }

    public function collections(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);
        $links = [];

        $collection = CrossProductCollection::all();

        foreach ($collection as $link) {
            $items_linked = CrossProduct::where('parent_id', $product_id)->where('collection_id', $link->id)->count();

            $item = [];
            $item['links'] = $items_linked;
            $item['name'] = $link->name;
            $item['id'] = $link->id;

            array_push($links, $item);
        }

        $sortBy = $request->input('sort');

        return view('aero-product-upsells::admin/collections', compact('product', 'links', 'sortBy'));
    }

    public function products(Request $request, $product_id, $collection_id)
    {
        $collection = CrossProductCollection::findOrFail($collection_id);
        $product = Product::findOrFail($product_id);
        $products = $product->crossProducts($collection);
        $sortBy = $request->input('sort');

        if ($request->get('success')) {
            Session::flash('success');
        }

        return view('aero-product-upsells::admin/product', compact('product', 'collection', 'products', 'sortBy'));
    }

    public function add_product(Request $request, Product $product, CrossProductCollection $collection)
    {
        $admin_link = config('aero.admin.slug');
        return view('aero-product-upsells::admin/select_products', compact('product', 'collection', 'admin_link'));
    }

    public function getProductsAsJSON(Request $request)
    {
        $columns = array(
            0 => 'Image',
            1 => 'Name',
            2 => 'Model'
        );

        $totalData = Product::count();

        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');

        if (empty($request->input('search.value'))) {
            $products = Product::offset($start)
                ->limit($limit)
                ->get();
        } else {
            $search = $request->input('search.value');

            $products = Product::where('id', 'LIKE', '%' . $search . '%')
                ->orWhere('model', 'LIKE', '%' . $search . '%')
                ->orWhereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%'])
                ->offset($start)
                ->limit($limit)
                ->get();

            $totalFiltered = Product::where('id', 'LIKE', '%' . $search . '%')
                ->orWhereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%'])
                ->count();
        }

        $data = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $nestedData['Image'] = isset($product->images()->first()->file) ? $product->images()->first()->file : null;
                $nestedData['Name'] = $product->name;
                $nestedData['Model'] = $product->model;
                $nestedData['id'] = $product->id;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function updateSortOrder(Request $request)
    {

        if (
            $request->input('child_id_array') &&
            $request->input('parent_id')
        ) {
            $child_ids_in_order = $request->input('child_id_array');
            $parent_id = $request->input('parent_id');
            $sort_count = 0;

            foreach ($child_ids_in_order as $child_id) {
                $child = CrossProduct::where('parent_id', $parent_id)->where('child_id', $child_id)->first();
                $child->sort = $sort_count++;
                $child->save();
            }

            return ['success' => true, 'message' => 'Updated'];
        }
    }

    public function link_products(Request $request)
    {
        try {
            $product = Product::findOrFail($request->input('product'));
            $collection = CrossProductCollection::findOrFail($request->input('collection'));
            $products = $request->input('products');
            $existingCount = CrossProduct::where('parent_id', $request->input('product'))->count();

            foreach ($products as $id) {
                $exists = CrossProduct::where('collection_id', $collection->id)->where('parent_id', $product->id)->where('child_id', $id)->count();
                if (!$exists) {
                    $p = Product::find($id);
                    $link = new CrossProduct();
                    $link->parent_id = $product->id;
                    $link->child_id = $p->id;
                    $link->collection_id = $collection->id;
                    $link->sort = $existingCount++;
                    $link->save();
                }
            }

            return response()->json('Success', 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function remove_link(CrossProduct $link)
    {
        try {
            $parent_id = $link->parent_id;

            // Remove link
            $link->delete();

            // Re-index existing links sort field
            $remaining_products = CrossProduct::where('parent_id', $parent_id)->orderBy('sort', 'asc')->get();
            $count = 0;

            foreach ($remaining_products as $product) {
                $product->sort = $count++;
                $product->save();
            }

            return redirect()->back();
        } catch (\Exception $err) {
            return redirect()->back();
        }
    }

    public function create_collection(Product $product)
    {
        return view('aero-product-upsells::admin/add_collection', compact('product'));
    }

    public function store_collection(Request $request, Product $product)
    {
        try {
            $exists = CrossProductCollection::where('name', $request->get('name'))->count();

            if (!$exists) {
                if ($request->get('name')) {
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

    public function csv()
    {
        $collections = CrossProductCollection::all();

        $downloads = CrossProductDownload::orderBy('id', 'desc')->paginate(10);

        return view('aero-product-upsells::admin.csv', compact('collections', 'downloads'));
    }

    public function csvDownload(CrossProductDownload $download)
    {
        return Storage::download($download->location);
    }

    public function csvImport(Request $request)
    {
        $validatedData = $request->validate([
            'csv' => 'required|mimes:csv,txt',
            'unlink-all' => 'boolean',
            'unlink-associated' => 'boolean'
        ]);

        if (isset($validatedData['unlink-all']) && $validatedData['unlink-all']) {
            DB::table('cross_products')->truncate();
        }

        Excel::import(new LinksImport($validatedData['unlink-associated'] ?? false), $validatedData['csv']);

        return back()->with('message', 'Links successfully created');
    }

    public function csvExport(Request $request)
    {
        $validatedData = $request->validate([
            'collections' => 'required|array',
            'parent' => '',
            'child' => '',
        ]);

        $model = CrossProductDownload::create([
            'location' => 'product-upsells/downloads/' . Str::random(12) . '.csv',
            'collections' => collect($validatedData['collections'])->keys()
        ]);

        $model->admin()->associate($request->user())->save();

        (new LinksExport($validatedData['collections'], $model, $validatedData['parent'], $validatedData['child']))
            ->store($model->location)->chain([
                new MarkDownloadAsComplete($model),
            ]);;

        return back()->with('message', 'Generating export.');
    }

    public function csvDelete(Request $request, CrossProductDownload $download)
    {
        Storage::delete($download->location);

        $download->delete();

        return back()->with('message', 'Export deleted.');
    }
}
