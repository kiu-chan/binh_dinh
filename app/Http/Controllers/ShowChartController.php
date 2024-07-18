<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ShowChartController extends Controller
{
    // public function showChart()
    // {
    //     return view('chart');
    // }

    // public function fetchData()
    // {
    //     $response = Http::get('http://171.244.133.49/api/getLandSlideRawData');
        
    //     if ($response->successful()) {
    //         $data = $response->json()['data'];
    //         $filteredData = array_map(function($item) {
    //             $fields = [];
    //             foreach (explode(';', $item['raw_content']) as $field) {
    //                 list($key, $value) = explode(',', $field);
    //                 $fields[$key] = $value;
    //             }
    //             return [
    //                 'id' => $item['id'],
    //                 'Tilt_A_Or_1_sin' => $fields['Tilt_A_Or_1_sin'] ?? null,
    //                 'Tilt_A_Or_2_sin' => $fields['Tilt_A_Or_2_sin'] ?? null,
    //                 'Tilt_A_Or_3_sin' => $fields['Tilt_A_Or_3_sin'] ?? null,
    //                 'created_at' => $item['created_at'],
    //                 'updated_at' => $item['updated_at']
    //             ];
    //         }, $data);

    //         return view('note', ['data' => json_encode($filteredData)]);
    //     } else {
    //         return response()->json(['message' => 'Failed to fetch data'], 500);
    //     }
    // }

    public function showLandSlideData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $client = new Client();
        $response = $client->get('http://171.244.133.49/api/getLandSlideRawData');
        $data = json_decode($response->getBody(), true);

        if ($data['status'] === 1) {
            $filteredData = collect($data['data'])->map(function ($item) {
                $contentArray = explode(';', $item['raw_content']);
                $parsedContent = [];
                foreach ($contentArray as $content) {
                    list($key, $value) = explode(',', $content);
                    $parsedContent[trim($key)] = trim($value);
                }

                $calculated_Tilt_A_Or_1_sin = 500 * ((float)$parsedContent['Tilt_A_Or_1(sin)'] - (- 0.001565));
                $calculated_Tilt_A_Or_2_sin = 500 * ((float)$parsedContent['Tilt_A_Or_2(sin)'] - 0.009616);
                $calculated_Tilt_A_Or_3_sin = 500 * ((float)$parsedContent['Tilt_A_Or_3(sin)'] - 0.000935);

                $calculated_Tilt_B_Or_1_sin = 500 * ((float)$parsedContent['Tilt_B_Or_1(sin)'] - (- 0.03261));
                $calculated_Tilt_B_Or_2_sin = 500 * ((float)$parsedContent['Tilt_B_Or_2(sin)'] - (- 0.053559));
                $calculated_Tilt_B_Or_3_sin = 500 * ((float)$parsedContent['Tilt_B_Or_3(sin)'] - (- 0.032529));

                return [
                    'id' => $item['id'],
                    'calculated_Tilt_A_Or_1_sin' => $calculated_Tilt_A_Or_1_sin,
                    'calculated_Tilt_A_Or_2_sin' => $calculated_Tilt_A_Or_2_sin,
                    'calculated_Tilt_A_Or_3_sin' => $calculated_Tilt_A_Or_3_sin,
                    'calculated_Tilt_B_Or_1_sin' => $calculated_Tilt_B_Or_1_sin,
                    'calculated_Tilt_B_Or_2_sin' => $calculated_Tilt_B_Or_2_sin,
                    'calculated_Tilt_B_Or_3_sin' => $calculated_Tilt_B_Or_3_sin,
                    'created_at' => $item['created_at'],
                ];
            });

            if ($startDate && $endDate) {
                $filteredData = $filteredData->filter(function ($item) use ($startDate, $endDate) {
                    $itemDate = Carbon::parse($item['created_at']);
                    return $itemDate->between($startDate, $endDate);
                });
            }

            $chartData = $filteredData->map(function ($item) {
                return [
                    'label' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    'data' => [
                        ['x' => $item['calculated_Tilt_A_Or_1_sin'], 'y' => -6],
                        ['x' => $item['calculated_Tilt_A_Or_2_sin'], 'y' => -11],
                        ['x' => $item['calculated_Tilt_A_Or_3_sin'], 'y' => -16],
                    ],
                ];
            })->values();

            $chartDataB = $filteredData->map(function ($item) {
                return [
                    'label' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    'data' => [
                        ['x' => $item['calculated_Tilt_B_Or_1_sin'], 'y' => -6],
                        ['x' => $item['calculated_Tilt_B_Or_2_sin'], 'y' => -11],
                        ['x' => $item['calculated_Tilt_B_Or_3_sin'], 'y' => -16],
                    ],
                ];
            })->values();

            $chartData->push([
                'label' => 'Bắt đầu',
                'data' => [
                    ['x' => -0.001565, 'y' => -6],
                    ['x' => 0.009616, 'y' => -11],
                    ['x' => 0.000935, 'y' => -16],
                ],
            ]);

            $chartDataB->push([
                'label' => 'Bắt đầu',
                'data' => [
                    ['x' => -0.03261, 'y' => -6],
                    ['x' => -0.053559, 'y' => -11],
                    ['x' => -0.032529, 'y' => -16],
                ],
            ]);

            $lineCount = $chartData->count();

            return view('chart', [
                'data' => $filteredData, 
                'chartData' => json_encode($chartData),
                'chartDataB' => json_encode($chartDataB),
                'lineCount' => $lineCount,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } else {
            return view('chart', ['data' => [], 'chartData' => json_encode([]),'chartDataB' => json_encode([]), 'lineCount' => 0]);
        }
    }

    // public function chartData()
    // {
    //     $response = Http::get('http://171.244.133.49/api/getLandSlideRawData');
        
    //     if ($response->successful()) {
    //         $data = $response->json()['data'];
    //         $filteredData = array_map(function($item) {
    //             $fields = [];
    //             foreach (explode(';', $item['raw_content']) as $field) {
    //                 list($key, $value) = explode(',', $field);
    //                 $fields[$key] = $value;
    //             }
    //             return [
    //                 'id' => $item['id'],
    //                 'PZ1_Digit' => $fields['PZ1_(Digit)'] ?? null,
    //                 'created_at' => $item['created_at'],
    //             ];
    //         }, $data);

    //         return view('note', ['data' => json_encode($filteredData)]);
    //     } else {
    //         return response()->json(['message' => 'Failed to fetch data'], 500);
    //     }
    // }
}