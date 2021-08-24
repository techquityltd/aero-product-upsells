<?php

namespace AeroCrossSelling\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Catalog\Models\Product;
use AeroCrossSelling\Models\CrossProductsPreset;
use Illuminate\Http\Request;

class PresetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $presets = CrossProductsPreset::paginate();

        return view('aero-product-upsells::admin.presets.index', compact('presets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('aero-product-upsells::admin.presets.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|max:100',
            'product' => 'required|array',
            'recommends' => 'required|array'
        ]);

        $preset = new CrossProductsPreset($data);
        $preset->products_deserialized = $data['product'];
        $preset->recommends_deserialized = $data['recommends'];
        $preset->save();

        $preset->products()->sync(
            $preset->getProducts()->select('id')->pluck('id'),
        );
        $preset->recommended()->sync(
            $preset->getRecommended()->select('id')->pluck('id'),
        );

        return redirect()->route('admin.modules.aero-cross-selling.presets.index')->with(['message' => 'Preset Successfully Created']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CrossProductsPreset $preset)
    {
        return view('aero-product-upsells::admin.presets.edit', compact('preset'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CrossProductsPreset $preset)
    {
        $data = $request->validate([
            'label' => 'required|max:100',
            'product' => 'required|array',
            'recommends' => 'required|array'
        ]);

        $preset->label = $data['label'];
        $preset->products_deserialized = $data['product'];
        $preset->recommends_deserialized = $data['recommends'];
        $preset->save();

        $preset->products()->sync(
            $preset->getProducts()->select('id')->pluck('id'),
        );
        $preset->recommended()->sync(
            $preset->getRecommended()->select('id')->pluck('id'),
        );

        return redirect()->back()->with(['message' => 'Preset Successfully Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CrossProductsPreset $preset)
    {
        $preset->delete();

        return redirect()->route('admin.modules.aero-cross-selling.presets.index')->with(['message' => 'Preset Successfully Deleted']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function search(Request $request)
    {
        $search = strtolower($request->input('q'));

        $products = Product::query();

        if (is_numeric($search)) {
            $products = $products->where('id', $search);
        } else {
            $products = $products->where('name', 'LIKE', "%{$search}%");
        }

        $products = $products->limit(5)->get()->map(function ($product) {
            return [
                'value' => $product->id,
                'group' => $product->model,
                'name' => $product->name
            ];
        });

        return response()->json($products);
    }
}
