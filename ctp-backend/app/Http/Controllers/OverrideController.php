<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManualOverride;

class OverrideController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'hour' => 'required|integer|min:0|max:23',
            'value' => 'required|integer|min:0|max:500',
        ]);

        ManualOverride::updateOrCreate(
            ['date' => $data['date'], 'hour' => $data['hour']],
            ['value' => $data['value']]
        );

        return redirect()->back()->with('status', 'Manual override saved!');
    }

    public function destroy($id)
    {
        ManualOverride::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'Override removed successfully!');
    }
}
