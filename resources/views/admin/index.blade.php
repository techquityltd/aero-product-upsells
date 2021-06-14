@extends('admin::layouts.main')

@section('sidebar')
    <div class="min-w-content-sidebar bg-content-sidebar shadow h-full p-4">
        <div>
            <form action="{{ route('admin.modules.aero-cross-selling.index') }}" method="get">
                <div>
                    <label for="search-term" class="block">Search</label>
                    <input type="search" id="search-term" name="q" autocomplete="off" placeholder="Search..."
                           value="{{ $searchTerm }}" class="text-base w-full">
                </div>
                <div class="mt-4">
                    <label for="categorized" class="block">Categorised</label>
                    <div class="select w-full">
                        <select name="categorized" id="categorized" class="w-full">
                            <option value="">Any</option>
                            <option value="1" {{ (string) request()->input('categorized') === '1' ? 'selected' : null }}>Categorised</option>
                            <option value="0" {{ (string) request()->input('categorized') === '0' ? 'selected' : null }}>Un-categorised</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn">Go</button>
                </div>
            </form>
            <div class="mt-4 border-t border-grey-lighter"></div>
            @if($appliedFilters->isNotEmpty())
                <div class="mt-4 flex flex-col bg-white mb-4 rounded border border-grey-lighter overflow-hidden">
                    <div class="bg-background"><span class="block font-lg font-medium p-4 py-3">Applied Filters</span></div>
                    <ul class="list-reset overflow-auto">
                        @foreach($appliedFilters as $filter)
                            <li>
                                <a href="{{ $filter['json_url'] }}"
                                   class="relative block px-4 py-3 w-full text-sm text-primary hover:text-primary-dark no-underline lg:hover:underline truncate">
                                    {{ $filter['name'] }}
                                    <span class="absolute pin-r mr-4 inline-block text-grey-darker">&times;</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mt-4">
                @foreach($filters as $filter)
                    @isset($filter['facets'])
                        <div class="flex flex-col bg-white mb-4 rounded border border-grey-lighter overflow-hidden">
                            <div class="bg-background"><span class="block font-lg font-medium p-4 py-3">{{ $filter['name'] }}</span></div>
                            <ul class="list-reset overflow-auto" style="max-height: 216px">
                                @foreach($filter['facets'] as $facet)
                                    <li class="border-t border-grey-lighter">
                                        <a href="{{ $facet['json_url'] }}"
                                           class="relative block px-8 py-3 w-full text-sm text-primary hover:text-primary-dark no-underline lg:hover:underline truncate">
                                            @if($facet['applied'] || ! $selectedCategory || $filter['id'] !== 'c')
                                                <span class="-ml-4 mr-2 inline-block w-3 h-3 border border-primary{{ $facet['applied'] ? ' bg-primary' : '' }}"></span>
                                            @elseif($selectedCategory && $loop->index > 0)
                                                <span class="-ml-4 mr-1">└</span>
                                            @endif
                                            {{ $facet['name'] }}
                                            <span class="absolute pin-r mr-4 inline-block text-grey-darker text-xs">({{ $facet['count'] }})</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">
            <a href="{{ route('admin.modules') }}" class="btn mr-4">@include('admin::icons.back') Back</a>
            <span class="flex-1">Select a product to add cross-links</span>
            <div class="flex">
                <span class="mr-1 mt-1 text-sm">
                    <a href="{{ route('admin.modules.aero-cross-selling.csv')}}" class="btn btn-secondary">
                        Import/Export Links
                    </a>
                </span>
            </div>
        </h2>
    </div>

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
                        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}" class="block relative text-xs">
                            <img src="{{ ! empty($product->images) ? asset('image-factory/60x60/'.$product->images[0]['file']) : asset('modules/aerocommerce/admin/no-image.svg') }}" class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" alt="{{ $product->name }}">
                            <div class="absolute pin shadow-inner rounded-sm border border-background"></div>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}">{{ \Illuminate\Support\Str::limit($product->name, 60) }}</a>
                    </td>
                    <td class="whitespace-no-wrap">
                        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}">{{ \Illuminate\Support\Str::limit($product->model, 30) }}</a>
                    </td>
                    <td class="whitespace-no-wrap">
                        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}">{{ \Illuminate\Support\Str::limit($product->manufacturer['name'], 30) }}</a>
                    </td>
                    <td>
                        <div class="flex items-center justify-end">
                            <a class="pt-1" href="{{ route('admin.modules.aero-cross-selling.product', $product) }}">@include('admin::icons.manage')</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No products</td>
                </tr>
            @endforelse
        </table>
        {{ $products->appends(request()->except('page'))->links() }}
    </div>
@endsection
