<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;

class PredictionTestController extends Controller
{
    protected $predictor;

    public function __construct(PredictionService $predictor)
    {
        $this->predictor = $predictor;
    }

    public function test()
    {
        $input = [
            "hour" => 14,
            "total_sales" => 300.0,
            "temperature" => 60.0,
            "condition" => 0,
            "weekday" => 5,
            "month" => 5,
            "is_promo" => 1,
        ];

        $prediction = $this->predictor->predict($input);

        return response()->json(['prediction' => $prediction]);
    }
}
