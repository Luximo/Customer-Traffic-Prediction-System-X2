@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 space-y-8">

    <h1 class="text-2xl font-bold mb-4">📋 Imported Traffic Data</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('traffic.index') }}" class="bg-white p-4 rounded shadow border flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium">Date</label>
            <input type="text" name="date" value="{{ \Carbon\Carbon::parse(request('date', now()))->format('d/m/y') }}"
                class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g. 05/01/25">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium">Hour</label>
            <input type="number" name="hour" value="{{ request('hour') }}" min="0" max="23"
                class="w-full rounded border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="0 - 23">
        </div>
        <div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded shadow hover:bg-indigo-700 transition">
                🔍 Filter
            </button>
        </div>
    </form>

    <!-- Results -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Date</th>
                    <th class="px-4 py-2 border">Hour</th>
                    <th class="px-4 py-2 border">Customer Count</th>
                    <th class="px-4 py-2 border">Uploaded</th>
                </tr>
            </thead>
            <tbody>
                @forelse($traffic as $entry)
                <tr>
                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($entry->date)->format('m/d/y') }}</td>
                    <td class="px-4 py-2 border">{{ $entry->hour }}:00</td>
                    <td class="px-4 py-2 border font-semibold">{{ $entry->customer_count }}</td>
                    <td class="px-4 py-2 border text-xs text-gray-500">{{ $entry->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No data found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $traffic->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
