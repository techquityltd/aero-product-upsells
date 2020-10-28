@extends('admin::layouts.main')

@section('sidebar')
@endsection

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">Products linked to {{ $product->name }} in {{ $collection->name }}</h2>
        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}" class="btn btn-primary mr-2">Back</a>
        <a href="{{ route('admin.modules.aero-cross-selling.select_product', array_merge(request()->all(), ['sort' => 'name-za', 'page' => null, 'product' => $product, 'collection' => $collection])) }}" class="btn btn-secondary">@include('admin::icons.add') Link Product</a>
    </div>

    @if(session('success'))
        <notify><span class="notify-success">Linking was successful!</span></notify>
    @endif

    @include('admin::partials.alerts')
    <div class="card p-0">
        <table>
            <tr class="header">
                <th></th>
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

            <tbody id="sortableTable" class="sort" data-parent-id="{{ $product->id }}">
            @forelse($products as $product)
                <tr class="sort-table-row" data-id="{{ $product->id }}">
                    <td class="whitespace-no-wrap">
                        <svg viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="1.5" class="handle h-4 max-h-full fill-current inline cursor-move"><path d="M15.92 99.97H307.4M15.92 159.97H307.4M15.92 219.97H307.4M160 99.71V35.33l-21.8 21.8 21.8-21.8 21.8 21.8M160 220.71v64.38l21.8-21.8-21.8 21.8-21.8-21.8" fill="none" stroke="#000" stroke-width="16"></path></svg>
                    </td>
                    <td class="py-1 pr-0 pl-4">
                        <div class="block relative text-xs">
                            <img src="{{ !$product->images->isEmpty() ? asset('image-factory/60x60/'.$product->images[0]['file']) : asset('modules/aerocommerce/admin/no-image.svg') }}" class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" alt="{{ $product->name }}">
                            <div class="absolute pin shadow-inner rounded-sm border border-background"></div>
                        </div>
                    </td>
                    <td>
                        @if($product->name)
                            <p>{{ \Illuminate\Support\Str::limit($product->name, 60) }}</p>
                        @else
                            <p>N/A</p>
                        @endif
                    </td>
                    <td class="whitespace-no-wrap">
                        @if($product->model)
                            <p>{{ \Illuminate\Support\Str::limit($product->model, 30) }}</p>
                        @else
                            <p>N/A</p>
                        @endif
                    </td>
                    <td class="whitespace-no-wrap">
                        @if($product->manufacturer)
                            <p>{{ \Illuminate\Support\Str::limit($product->manufacturer['name'], 30)}}</p>
                        @else
                            <p>N/A</p>
                        @endif
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
        </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.sort').sortable({
                cursor: 'move',
                axis: 'y',
                update: function (e, ui) {
                    href = '/admin/product-cross-sells/update-sort-order';
                    $(this).sortable("refresh");
                    sorted = $(this).sortable("serialize", 'id');
                    let child_id_array = [];

                    $('.sort-table-row').each(function() {
                        child_id_array.push($(this).data('id'));
                    })

                    $.ajax({
                        type: 'POST',
                        url: href,
                        data: {
                            _token: $('form').find('input[name="_token"]').val(),
                            sorted: sorted,
                            child_id_array: child_id_array,
                            parent_id: $('#sortableTable').data('parent-id')
                        },
                        success: function (msg) {

                        }
                    });
                }
            });
        });
    </script>
@endpush
