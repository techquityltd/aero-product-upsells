<?php

use Illuminate\Support\Facades\Route;
use AeroCrossSelling\Http\Controllers\AdminCrossSellingController;

Route::prefix('modules/product-cross-sells')->name('admin.modules.aero-cross-selling.')->group(function () {
    Route::get('/', [AdminCrossSellingController::class, 'index'])->name('index');
    Route::get('/products/json', [AdminCrossSellingController::class, 'getProductsAsJSON'])->name('products-json');
    Route::get('/{product}/collections/create', [AdminCrossSellingController::class, 'create_collection'])->name('create_collection');
    Route::post('/{product}/collections/store', [AdminCrossSellingController::class, 'store_collection'])->name('store_collection');
    Route::post('/update-sort-order', [AdminCrossSellingController::class, 'updateSortOrder'])->name('update_sort_order');
    Route::post('/link', [AdminCrossSellingController::class, 'link_products'])->name('link_products');
    Route::delete('/link/{link}/remove', [AdminCrossSellingController::class, 'remove_link'])->name('remove_link');
    Route::get('/{product_id}', [AdminCrossSellingController::class, 'collections'])->name('product');
    Route::get('/{product_id}/{collection_id}', [AdminCrossSellingController::class, 'products'])->name('links');
    Route::get('/{product}/{collection}/add', [AdminCrossSellingController::class, 'add_product'])->name('select_product');
});

/**
 * Upload Import Links
 */
Route::prefix('product-cross-sells')->name('admin.modules.aero-cross-selling.')->group(function () {
    Route::get('/csv', [AdminCrossSellingController::class, 'csv'])->name('csv');
    Route::post('/csv/import', [AdminCrossSellingController::class, 'csvImport'])->name('csv-import');
    Route::post('/csv/export', [AdminCrossSellingController::class, 'csvExport'])->name('csv-export');
    Route::get('/csv/download/{download}', [AdminCrossSellingController::class, 'csvDownload'])->name('csv-download');
    Route::delete('/csv/delete/{download}', [AdminCrossSellingController::class, 'csvDelete'])->name('csv-download.delete');
});
