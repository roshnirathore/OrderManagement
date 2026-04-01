<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


    Route::get('customers' , [UserController::class, 'index']);
    Route::post('customers' , [UserController::class, 'store']);
    Route::get('customers/{id}/orders' , [UserController::class, 'orderList']);
   

    Route::get('products',[ProductController::class, 'index']);
    Route::post('products',[ProductController::class, 'store']);

    Route::get('orders',[OrderController::class, 'index']);
    Route::post('orders',[OrderController::class, 'store']);
