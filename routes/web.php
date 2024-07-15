<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ChartDataController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', [MapController::class, 'showMap']);

