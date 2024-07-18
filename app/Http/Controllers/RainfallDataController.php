<?php

namespace App\Http\Controllers;

use App\Models\RainfallData;
use Illuminate\Http\Request;

class RainfallDataController extends Controller
{
    public function index()
    {
        $data = RainfallData::all();
        return response()->json($data);
    }
}