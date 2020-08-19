<?php

use Illuminate\Support\Facades\Route;
use AeroCrossSelling\Http\Controllers\AdminCrossSellingController;

Route::get('/product-cross-sells', [AdminCrossSellingController::class, 'index'])->name('admin.modules.aero-cross-selling.index');
Route::get('/product-cross-sells/products/json', [AdminCrossSellingController::class, 'getProductsAsJSON'])->name('admin.modules.aero-cross-selling.products-json');

Route::get('/product-cross-sells/{product}/collections/create', [AdminCrossSellingController::class, 'create_collection'])->name('admin.modules.aero-cross-selling.create_collection');
Route::post('/product-cross-sells/{product}/collections/store', [AdminCrossSellingController::class, 'store_collection'])->name('admin.modules.aero-cross-selling.store_collection');

Route::post('/product-cross-sells/update-sort-order', [AdminCrossSellingController::class, 'updateSortOrder'])->name('admin.modules.aero-cross-selling.update_sort_order');
Route::post('/product-cross-sells/link', [AdminCrossSellingController::class, 'link_products'])->name('admin.modules.aero-cross-selling.link_products');
Route::delete('/product-cross-sells/link/{link}/remove', [AdminCrossSellingController::class, 'remove_link'])->name('admin.modules.aero-cross-selling.remove_link');

Route::get('/product-cross-sells/{product_id}', [AdminCrossSellingController::class, 'collections'])->name('admin.modules.aero-cross-selling.product');
Route::get('/product-cross-sells/{product_id}/{collection_id}', [AdminCrossSellingController::class, 'products'])->name('admin.modules.aero-cross-selling.links');
Route::get('/product-cross-sells/{product}/{collection}/add', [AdminCrossSellingController::class, 'add_product'])->name('admin.modules.aero-cross-selling.select_product');