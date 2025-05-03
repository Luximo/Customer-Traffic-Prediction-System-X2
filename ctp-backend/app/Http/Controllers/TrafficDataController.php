<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficData;

class TrafficDataController extends Controller
{
    public function index(Request $request)
    {
        $query = TrafficData::query();

        if ($request->filled('date')) {
            $query->where('date', $request->input('date'));
        }

        if ($request->filled('hour')) {
            $query->where('hour', $request->input('hour'));
        }

        $traffic = $query->orderBy('date', 'desc')->orderBy('hour')->paginate(25);

        return view('traffic.index', [
            'traffic' => $traffic,
            'filters' => $request->only(['date', 'hour'])
        ]);
    }
}
