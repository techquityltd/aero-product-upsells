@extends('admin::layouts.main')

@section('sidebar')
@endsection

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">Products linked to {{ $product->name }} in {{ $collection->name }}</h2>
        <a href="{{ route('admin.modules.aero-cross-selling.select_product', array_merge(request()->all(), ['sort' => 'name-za', 'page' => null, 'product' => $product, 'collection' => $collection])) }}" class="btn btn-secondary">@include('admin::icons.add') Link Product</a>
    </div>

    @if(session('success'))
        <notify><span class="notify-success">Linking was successful!</span></notify>
    @endif

    @include('admin::partials.alerts')
    <div class="card p-0">
        <table>
            <tr class="header">
                <th style="min-width:46px">&nbsp;</th>
                <th class="w-full whitespace-no-wrap">
                    @if($sortBy === 'name-az')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'name-za', 'page' => null])) }}">Name<span class="no-underline ml-2">@include('admin::icons.sort-az')</span></a>
                    @elseif($sortBy === 'name-za')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => null, 'page' => null])) }}">Name<span class="no-underline ml-2">@include('admin::icons.sort-za')</span></a>
                    @else
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'name-az', 'page' => null])) }}">Name</a>
                    @endif
                </th>
                <th class="whitespace-no-wrap">
                    @if($sortBy === 'model-az')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'model-za', 'page' => null])) }}">Model<span class="no-underline ml-2">@include('admin::icons.sort-az')</span></a>
                    @elseif($sortBy === 'model-za')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => null, 'page' => null])) }}">Model<span class="no-underline ml-2">@include('admin::icons.sort-za')</span></a>
                    @else
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'model-az', 'page' => null])) }}">Model</a>
                    @endif
                </th>
                <th class="whitespace-no-wrap">
                    @if($sortBy === 'manufacturer-az')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'manufacturer-za', 'page' => null])) }}">Manufacturer<span class="no-underline ml-2">@include('admin::icons.sort-az')</span></a>
                    @elseif($sortBy === 'manufacturer-za')
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => null, 'page' => null])) }}">Manufacturer<span class="no-underline ml-2">@include('admin::icons.sort-za')</span></a>
                    @else
                        <a href="{{ route('admin.modules.aero-cross-selling.index', array_merge(request()->all(), ['sort' => 'manufacturer-az', 'page' => null])) }}">Manufacturer</a>
                    @endif
                </th>
                <th>&nbsp;</th>
            </tr>
            @forelse($products as $product)
                <tr>
                    <td class="py-1 pr-0 pl-4">
                        <div class="block relative text-xs">
                            <img src="{{ ! empty($product->images) ? asset('image-factory/60x60/'.$product->images[0]['file']) : asset('modules/aerocommerce/admin/no-image.svg') }}" class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" alt="{{ $product->name }}">
                            <div class="absolute pin shadow-inner rounded-sm border border-background"></div>
                        </div>
                    </td>
                    <td>
                        <p>{{ \Illuminate\Support\Str::limit($product->name, 60) }}</p>
                    </td>
                    <td class="whitespace-no-wrap">
                        <p>{{ \Illuminate\Support\Str::limit($product->model, 30) }}</p>
                    </td>
                    <td class="whitespace-no-wrap">
                        <p>{{ \Illuminate\Support\Str::limit($product->manufacturer['name'], 30) }}</p>
                    </td>
                    <td>
                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.modules.aero-cross-selling.remove_link', array_merge(request()->all(), ['link' => $product->cross_id])) }}"
                               @click.prevent="confirmDeleteSubmit($refs.deleteLink{{ $product->cross_id }}, 'Are you sure?')">@include('admin::icons.bin')</a>
                            <form ref="deleteLink{{ $product->cross_id }}" action="{{ route('admin.modules.aero-cross-selling.remove_link', array_merge(request()->all(), ['link' => $product->cross_id])) }}" method="post">
                                @csrf
                                @method('delete')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No products</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection
