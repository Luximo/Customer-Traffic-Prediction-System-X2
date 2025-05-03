<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TrafficData;
use Carbon\Carbon;

class CsvImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate CSV file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',  // Max 2MB file
        ]);

        // If validation fails, return to the previous page with error message
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Get the uploaded file
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header row
        fgetcsv($handle);

        $insertData = [];

        // Process each row in the CSV
        while (($row = fgetcsv($handle)) !== false) {
            // Ensure the CSV has the correct number of columns
            if (count($row) !== 3) {
                continue; // Skip invalid rows (this is optional, depending on your needs)
            }

            // Validate and format the data
            $date = Carbon::createFromFormat('m/d/y', $row[0])->format('Y-m-d');  // Ensure date format
            $hour = (int) $row[1];  // Ensure hour is an integer
            $customerCount = (int) $row[2];  // Ensure customer count is an integer

            // Prepare the data for batch insert
            $insertData[] = [
                'date' => $date,
                'hour' => $hour,
                'customer_count' => $customerCount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Close the file
        fclose($handle);

        // Perform batch insertion to improve performance
        TrafficData::insert($insertData);

        // Return success message
        return redirect()->back()->with('upload_success', '📥 CSV imported successfully!');
    }
}
