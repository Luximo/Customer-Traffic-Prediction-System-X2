<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PredictionService
{
    protected $flaskBaseUrl = 'http://127.0.0.1:5000';

    public function predict(array $data): ?int
    {
        $response = Http::post("{$this->flaskBaseUrl}/predict", $data);

        if ($response->successful()) {
            return $response->json()['predicted_customer_count'];
        }

        return null;
    }
}
