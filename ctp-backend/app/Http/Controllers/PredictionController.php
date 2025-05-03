<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;
use Illuminate\Support\Carbon;
use App\Models\ManualOverride;

class PredictionController extends Controller
{
    protected $predictor;

    public function __construct(PredictionService $predictor)
    {
        $this->predictor = $predictor;
    }

    public function index(Request $request, PredictionService $predictor)
    {
        $labels = [];
        $values = [];
        $overridden = [];

        // Get and validate the date
        $date = $request->input('date', now()->toDateString());
        try {
            $parsedDate = Carbon::parse($date);
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return redirect()->back()->withErrors(['date' => 'Invalid date format. Please use MM/DD/YY or YYYY-MM-DD.']);
        }

        $condition = (int) $request->input('condition', 0);
        $isPromo = (int) $request->input('is_promo', 1);

        $weekday = $parsedDate->dayOfWeek;
        $month = $parsedDate->month;

        // --- Hourly Predictions (existing)
        for ($hour = 8; $hour <= 21; $hour++) {
            $input = [
                "hour" => $hour,
                "total_sales" => 300.0,
                "temperature" => 60.0,
                "condition" => $condition,
                "weekday" => $weekday,
                "month" => $month,
                "is_promo" => $isPromo,
            ];

            $override = ManualOverride::where('date', $parsedDate->toDateString())->where('hour', $hour)->first();
            $prediction = $override ? $override->value : $predictor->predict($input);

            $labels[] = date('g A', strtotime("$hour:00"));
            $values[] = $prediction;
            $overridden[] = $override ? true : false;
        }

        // --- Weekly Trends (new)
        $weeklyLabels = [];
        $weeklyTotals = [];
        $weeklyAverages = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $parsedDate->copy()->addDays($i);
            $dayWeekday = $day->dayOfWeek;
            $dayMonth = $day->month;

            $dailyPredictions = [];

            for ($hour = 8; $hour <= 21; $hour++) {
                $input = [
                    "hour" => $hour,
                    "total_sales" => 300.0,
                    "temperature" => 60.0,
                    "condition" => $condition,
                    "weekday" => $dayWeekday,
                    "month" => $dayMonth,
                    "is_promo" => $isPromo,
                ];

                $dailyPredictions[] = $predictor->predict($input);
            }

            $weeklyLabels[] = $day->format('D M j'); // e.g. Fri May 2
            $weeklyTotals[] = array_sum($dailyPredictions);
            $weeklyAverages[] = round(array_sum($dailyPredictions) / count($dailyPredictions), 2);
        }

        // 🔥 Step 4: Heatmap Data (7 days × 14 hours)
        $heatmapDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $heatmapHours = range(8, 21); // 8AM to 9PM
        $heatmapData = [];

        foreach ($heatmapDays as $dayIndex => $dayName) {
            $dayPredictions = [];

            foreach ($heatmapHours as $hour) {
                $input = [
                    "hour" => $hour,
                    "total_sales" => 300.0,
                    "temperature" => 60.0,
                    "condition" => (int) $request->input('condition', 0),
                    "weekday" => $dayIndex, // 0 = Monday
                    "month" => now()->month,
                    "is_promo" => (int) $request->input('is_promo', 1),
                ];

                $dayPredictions[] = $predictor->predict($input);
            }

            $heatmapData[$dayName] = $dayPredictions;
        }

        return view('predictions', [
            'prediction' => $values[6],
            'labels' => $labels,
            'values' => $values,
            'overridden' => $overridden,
            'weeklyLabels' => $weeklyLabels,
            'weeklyTotals' => $weeklyTotals,
            'weeklyAverages' => $weeklyAverages,
            'heatmapLabels' => $heatmapHours,
            'heatmapData' => $heatmapData,
        ]);
    }
}
