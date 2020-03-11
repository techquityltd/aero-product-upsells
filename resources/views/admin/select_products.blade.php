@extends('admin::layouts.main')

@section('sidebar')
    <div class="min-w-content-sidebar bg-content-sidebar shadow h-full p-4">
        <div>
            <div>
                <label for="search-term" class="block">Search</label>
                <input type="search" id="search" autocomplete="off" placeholder="Search..."
                       value="" class="text-base w-full">
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="/vendor/aero-product-upsells/css/data-table.css">
@endpush

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">Select a product to link</h2>
        <a href="#" id="add_products" class="btn btn-secondary hide__button">@include('admin::icons.add') Link Products</a>
    </div>

    <div class="card">
        <table id="myTable" style="padding: 20px;" data-product="{{ $product->id }}" data-collection="{{ $collection->id }}">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Model</th>
            </tr>
            <tr>
                <td style="max-width: 48px;"></td>
                <td></td>
                <td class="whitespace-no-wrap"></td>
            </tr>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="/js/data_table_prod.js"></script>
@endpush