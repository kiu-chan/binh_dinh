<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ChartDataController;
use App\Http\Controllers\RainfallDataController;
use App\Http\Controllers\ShowChartController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', [MapController::class, 'showMap']);

// Route::get('rainfall', [RainfallDataController::class, 'index']);
Route::post('rainfall', [RainfallDataController::class, 'store']);
Route::get('/rainfall-chart', function () {
    return view('rainfall_chart');
});
Route::get('rainfall-data', [RainfallDataController::class, 'index']);
Route::get('show-chart', [ShowChartController::class, 'showChart']);

Route::get('/landslide-data', [ShowChartController::class, 'showLandSlideData']);
Route::get('/landslide', [ShowChartController::class, 'chartData']);


