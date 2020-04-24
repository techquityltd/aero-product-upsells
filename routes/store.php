<?php

use Illuminate\Support\Facades\Route;
use AeroCrossSelling\Http\Controllers\CrossSellingController;

Route::get('/product-cross-sells/{product_id}', [CrossSellingController::class, 'collections']);
