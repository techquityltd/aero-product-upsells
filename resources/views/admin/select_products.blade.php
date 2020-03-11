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
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">--}}
    <link rel="stylesheet" type="text/css" href="/vendor/aero-cross-selling-module/css/data-table.css">
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
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            fetch('/admin/product-cross-sells/products/json')
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    sortTable(data);
                });

            function sortTable(data) {
                let selected_products = [];

                var table = $('#myTable').DataTable({
                    data: data,
                    "columns": [
                        {
                            render: function (data, type, JsonResultRow, meta) {
                                return '<img class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" src="' + window.location.origin + '/storage/' + JsonResultRow.default_images[0].file + '">';
                            },
                            title: "Image"
                        },
                        {
                            data: "name",
                            title: "Name"
                        },
                        {
                            data: "model",
                            title: "Model"
                        }
                    ]
                });

                $('#search').keyup(function(){
                    table.search($(this).val()).draw();
                });

                $('#myTable tbody').on('click', 'tr', function () {
                    let id = table.row(this).data().id;
                    // table.$('tr.selected').removeClass('selected');
                    if($(this).closest('tr').hasClass('selected')) {
                        $(this).closest('tr').removeClass('selected');
                        const index = selected_products.indexOf(id);
                        if (index > -1) {
                            selected_products.splice(index, 1);
                        }

                        if(selected_products.length === 0) {
                            $('#add_products').addClass('hide__button');
                        }
                    } else {
                        $(this).closest('tr').addClass('selected');
                        selected_products.push(id);

                        if(selected_products.length > 0 && $('#add_products').hasClass('hide__button')) {
                            $('#add_products').removeClass('hide__button');
                        }
                    }
                });

                $('#add_products').on('click', function() {
                   let product = $('#myTable').attr('data-product');
                   let collection = $('#myTable').attr('data-collection');

                    $.ajax({
                        type: "POST",
                        url: '/admin/product-cross-sells/link',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'products': selected_products,
                            'product': product,
                            'collection': collection
                        },
                        success: function(data) {
                            window.location.href = '/admin/product-cross-sells/' + product + '/' + collection + '?success=true';
                        },
                        dataType: 'json'
                    });
                });
            }
        });
    </script>
@endpush