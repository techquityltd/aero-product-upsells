<?php

use Illuminate\Support\Facades\Route;
use AeroCrossSelling\Http\Controllers\AdminCrossSellingController;
use AeroCrossSelling\Http\Controllers\PresetController;

/**
 * Presets feature
 */
Route::prefix('modules/product-cross-sells')->name('admin.modules.aero-cross-selling.presets.')->group(function () {
    Route::get('/presets', [PresetController::class, 'index'])->name('index');
    Route::get('/create', [PresetController::class, 'create'])->name('create');
    Route::post('/store', [PresetController::class, 'store'])->name('store');
    Route::get('/edit/{preset}', [PresetController::class, 'edit'])->name('edit');
    Route::put('/edit/{preset}', [PresetController::class, 'update'])->name('update');
    Route::delete('/delete/{preset}', [PresetController::class, 'destroy'])->name('destroy');
    Route::get('/products/search', [PresetController::class, 'search'])->name('search');
});

/**
 * Legacy.
 */
Route::get('/product-cross-sells/legacy', [AdminCrossSellingController::class, 'index'])->name('admin.modules.aero-cross-selling.index');
Route::get('/product-cross-sells/legacy/products/json', [AdminCrossSellingController::class, 'getProductsAsJSON'])->name('admin.modules.aero-cross-selling.products-json');
Route::get('/product-cross-sells/legacy/{product}/collections/create', [AdminCrossSellingController::class, 'create_collection'])->name('admin.modules.aero-cross-selling.create_collection');
Route::post('/product-cross-sells/legacy/{product}/collections/store', [AdminCrossSellingController::class, 'store_collection'])->name('admin.modules.aero-cross-selling.store_collection');
Route::post('/product-cross-sells/legacy/update-sort-order', [AdminCrossSellingController::class, 'updateSortOrder'])->name('admin.modules.aero-cross-selling.update_sort_order');
Route::post('/product-cross-sells/legacy/link', [AdminCrossSellingController::class, 'link_products'])->name('admin.modules.aero-cross-selling.link_products');
Route::delete('/product-cross-sells/legacy/link/{link}/remove', [AdminCrossSellingController::class, 'remove_link'])->name('admin.modules.aero-cross-selling.remove_link');
Route::get('/product-cross-sells/legacy/{product_id}', [AdminCrossSellingController::class, 'collections'])->name('admin.modules.aero-cross-selling.product');
Route::get('/product-cross-sells/legacy/{product_id}/{collection_id}', [AdminCrossSellingController::class, 'products'])->name('admin.modules.aero-cross-selling.links');
Route::get('/product-cross-sells/legacy/{product}/{collection}/add', [AdminCrossSellingController::class, 'add_product'])->name('admin.modules.aero-cross-selling.select_product');

/**
 * Upload Import Links
 */
Route::prefix('modules/product-cross-sells')->name('admin.modules.aero-cross-selling.')->group(function () {
    Route::get('/csv', [AdminCrossSellingController::class, 'csv'])->name('csv');
    Route::post('/csv/import', [AdminCrossSellingController::class, 'csvImport'])->name('csv-import');
    Route::post('/csv/export', [AdminCrossSellingController::class, 'csvExport'])->name('csv-export');
    Route::get('/csv/download/{download}', [AdminCrossSellingController::class, 'csvDownload'])->name('csv-download');
    Route::delete('/csv/delete/{download}', [AdminCrossSellingController::class, 'csvDelete'])->name('csv-download.delete');
});
