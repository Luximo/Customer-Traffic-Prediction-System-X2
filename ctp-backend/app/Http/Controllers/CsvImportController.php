<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TrafficData;

class CsvImportController extends Controller
{
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            TrafficData::create([
                'date' => $row[0],
                'hour' => $row[1],
                'customer_count' => $row[2],
            ]);
        }

        fclose($handle);

        return redirect()->back()->with('upload_success', '📥 CSV imported successfully!');
    }
}
