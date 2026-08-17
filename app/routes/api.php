<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/order', [OrderController::class, 'index']);
Route::get('/order/processed', [OrderController::class, 'processed']);
Route::post('/order', [OrderController::class, 'store']);
