<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/quote', [QuoteController::class, 'store']);
Route::get('/quotes', [QuoteController::class, 'index']);