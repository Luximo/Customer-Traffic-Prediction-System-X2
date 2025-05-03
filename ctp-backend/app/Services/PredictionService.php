<?php

// app/Services/PredictionService.php

namespace App\Services;

use App\Models\TrafficData;

class PredictionService
{
    public function predict(array $input): int
    {
        // Extract input
        $hour = $input['hour'];
        $weekday = $input['weekday'] ?? now()->dayOfWeek;
        $month = $input['month'] ?? now()->month;

        // --- 1. Lookup historical average for this hour + weekday
        $historicalAverage = TrafficData::query()
            ->where('hour', $hour)
            ->whereRaw("strftime('%w', date) = ?", [$weekday])
            ->avg('customer_count');

        // --- 2. Fallback if no data found
        $historicalAverage = $historicalAverage ?? 0;

        // --- 3. Generate synthetic prediction
        $base = 20 + ($input['is_promo'] ?? 0) * 10;

        $synthetic = $base
            + rand(0, 10)
            + (($input['condition'] ?? 0) * 2)
            + ($hour >= 11 && $hour <= 13 ? 15 : 0)
            + ($hour >= 17 && $hour <= 19 ? 10 : 0);

        // --- 4. Combine: Weighted blend (e.g., 70% synthetic, 30% historical)
        $final = $historicalAverage > 0
            ? round(($synthetic * 0.7) + ($historicalAverage * 0.3))
            : round($synthetic);

        return max(0, (int) $final);
    }
}
