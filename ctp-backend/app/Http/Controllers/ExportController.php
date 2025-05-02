<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\PredictionService;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    public function export(Request $request, PredictionService $predictor)
    {
        $date = $request->input('date', now()->toDateString());
        $condition = (int) $request->input('condition', 0);
        $isPromo = (int) $request->input('is_promo', 1);
        $weekday = Carbon::parse($date)->dayOfWeek;
        $month = Carbon::parse($date)->month;

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="predictions_' . $date . '.csv"',
        ];

        $columns = ['Hour', 'Predicted Customers'];

        return new StreamedResponse(function () use ($columns, $predictor, $weekday, $month, $condition, $isPromo) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

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
                $prediction = $predictor->predict($input);
                fputcsv($handle, [$hour . ':00', $prediction]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
