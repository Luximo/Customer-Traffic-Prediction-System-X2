<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Traffic Predictions</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        accent: '#10b981',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased leading-relaxed">
    <div class="max-w-6xl mx-auto py-10 px-6 space-y-10">

        <!-- Title -->
        <h1 class="text-3xl font-bold flex items-center gap-2">
            📊 Customer Traffic Predictions
        </h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('predictions.index') }}"
            class="bg-white p-6 rounded-lg shadow space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium">Date</label>
                    <input type="date" name="date" id="date"
                        value="{{ request('date', now()->toDateString()) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label for="condition" class="block text-sm font-medium">Weather</label>
                    <select name="condition" id="condition"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary focus:border-primary">
                        @foreach(['Clear','Cloudy','Rainy','Snowy','Stormy'] as $index => $label)
                        <option value="{{ $index }}" {{ request('condition') == $index ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="is_promo" class="block text-sm font-medium">Promotion Active?</label>
                    <select name="is_promo" id="is_promo"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary focus:border-primary">
                        <option value="1" {{ request('is_promo', '1') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('is_promo') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-semibold rounded shadow hover:bg-indigo-700 transition">
                    🔄 Update Prediction
                </button>
                <a href="{{ route('predictions.export', [
    'date' => request('date', now()->toDateString()),
    'condition' => request('condition', 0),
    'is_promo' => request('is_promo', 1)
]) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded shadow hover:bg-green-700 transition">
                    🧾 Export to CSV
                </a>


            </div>
        </form>

        <!-- Context -->
        <div class="text-sm text-gray-600">
            Showing predictions for <strong>{{ \Carbon\Carbon::parse(request('date', now()))->format('l, M d') }}</strong>,
            weather: <strong>{{ ['Clear','Cloudy','Rainy','Snowy','Stormy'][(int)request('condition', 0)] }}</strong>,
            promo: <strong>{{ request('is_promo', 1) ? 'Yes' : 'No' }}</strong>
        </div>

        <!-- Noon Summary -->
        <div class="bg-primary/10 border border-primary/20 p-4 rounded text-center shadow">
            <div class="text-sm text-gray-700">Predicted Customer Count at Noon</div>
            <div class="text-3xl font-bold text-primary">{{ $prediction }}</div>
        </div>

        <!-- Hourly Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">🕘 Hourly Traffic</h2>
            <canvas id="predictionChart" height="300"></canvas>
        </div>

        <!-- Weekly Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">📆 Weekly Trends</h2>
            <canvas id="weeklyChart" height="300"></canvas>
        </div>

        <!-- Calendar-Style Heatmap -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">🔥 Weekly Heatmap</h2>
            <div class="overflow-auto scroll-smooth snap-x rounded">
                <table class="min-w-full table-auto text-sm text-center snap-start">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-2 py-2 text-left bg-white">Day / Hour</th>
                            @foreach($heatmapLabels as $hour)
                            <th class="px-2 py-2 text-gray-700 font-medium whitespace-nowrap">{{ $hour }}:00</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatmapData as $day => $hourlyCounts)
                        <tr class="border-t">
                            <td class="text-left font-medium px-2 py-1 bg-gray-50">{{ $day }}</td>
                            @foreach($hourlyCounts as $value)
                            @php
                            $value = (int) $value;
                            $bgColor = match (true) {
                            $value <= 10=> 'bg-blue-100',
                                $value <= 20=> 'bg-green-200',
                                    $value <= 30=> 'bg-green-400',
                                        $value <= 40=> 'bg-yellow-400',
                                            $value <= 50=> 'bg-orange-400',
                                                default => 'bg-red-500 text-white',
                                                };
                                                @endphp
                                                <td class="relative group px-1 py-2 {{ $bgColor }}">
                                                    <div class="hidden group-hover:block absolute bottom-full mb-1 w-28 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded px-2 py-1 shadow">
                                                        {{ $value }} customers
                                                    </div>
                                                    &nbsp;
                                                </td>
                                                @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Debug JSON Output -->
            @if(app()->environment('local'))
            <pre class="bg-gray-50 mt-6 p-4 text-xs text-gray-500 border rounded overflow-auto">
{!! json_encode($values, JSON_PRETTY_PRINT) !!}
            </pre>
            @endif
        </div>

        <!-- Chart Theme Script -->
        <script>
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = "#4b5563";
            Chart.defaults.elements.point.radius = 4;
            Chart.defaults.elements.line.borderWidth = 2;
            Chart.defaults.elements.point.hoverRadius = 6;
            Chart.defaults.elements.point.hoverBackgroundColor = '#6366f1';
        </script>

        <!-- Line Charts -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById('predictionChart')?.getContext('2d');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($labels),
                            datasets: [{
                                label: 'Predicted Customers',
                                data: @json($values),
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Customers'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Hour'
                                    }
                                }
                            }
                        }
                    });
                }

                const wctx = document.getElementById('weeklyChart')?.getContext('2d');
                if (wctx) {
                    new Chart(wctx, {
                        type: 'line',
                        data: {
                            labels: @json($weeklyLabels),
                            datasets: [{
                                    label: 'Total Daily Customers',
                                    data: @json($weeklyTotals),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                },
                                {
                                    label: 'Average per Hour',
                                    data: @json($weeklyAverages),
                                    borderColor: '#f59e0b',
                                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Customers'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Day'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    </div>
</body>

</html>