<?php

use App\Http\Controllers\RainfallDataController;

Route::get('rainfall-data', [RainfallDataController::class, 'index']);

