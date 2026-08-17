<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/order',[OrderController::class,'index']);
Route::post('/order',[OrderController::class,'store']);