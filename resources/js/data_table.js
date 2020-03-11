import $ from 'jquery';
import DataTable from 'datatables.net';

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
                        let to_render = '';
                        if(JsonResultRow.default_images.length > 0) {
                            to_render = '<img class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" src="' + window.location.origin + '/storage/' + JsonResultRow.default_images[0].file + '">';
                        } else {
                            to_render = '<img class="block w-full rounded-sm mx-auto" style="width:auto;height:30px" src="#">';
                        }
                        return to_render;
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
