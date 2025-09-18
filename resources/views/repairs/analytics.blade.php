@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                    <a href="{{ route('repairs.index') }}" class="hover:text-teal-600 transition-colors">
                        <i class="fa-solid fa-wrench mr-1"></i>
                        Reparaties
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-900 font-medium">Analytics</span>
                </nav>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">
                    📊 Reparatie Analytics
                </h1>
                <p class="text-gray-600 mt-2">Inzichten en statistieken over jouw reparaties</p>
            </div>
            <a href="{{ route('repairs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Terug naar Reparaties
            </a>
        </div>

        @php
            $totalRepairs = $repairs->count();
            $completedRepairs = $repairs->where('status', 'gereed')->count();
            $activeRepairs = $repairs->whereIn('status', ['gepland', 'bezig'])->count();
            $waitingParts = $repairs->where('status', 'wachten_op_onderdeel')->count();
            $totalValue = $repairs->sum('cost_estimate');
            $totalPartsValue = $repairs->sum(function($repair) { return $repair->parts->sum('price'); });
            $avgRepairValue = $totalRepairs > 0 ? $totalValue / $totalRepairs : 0;
            $completionRate = $totalRepairs > 0 ? ($completedRepairs / $totalRepairs) * 100 : 0;
        @endphp

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totaal Reparaties</p>
                        <p class="text-3xl font-bold text-teal-600">{{ $totalRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-wrench text-teal-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Voltooiingspercentage</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ number_format($completionRate, 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totale Waarde</p>
                        <p class="text-3xl font-bold text-blue-600">€{{ number_format($totalValue + $totalPartsValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-euro-sign text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Gem. Reparatiewaarde</p>
                        <p class="text-3xl font-bold text-purple-600">€{{ number_format($avgRepairValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-calculator text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Status Distribution -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fa-solid fa-chart-pie text-teal-600 mr-3"></i>
                    Status Verdeling
                </h3>
                
                <div class="space-y-4">
                    @php
                        $statusData = [
                            ['name' => 'Gepland', 'count' => $repairs->where('status', 'gepland')->count(), 'color' => 'bg-yellow-500', 'light' => 'bg-yellow-100'],
                            ['name' => 'Bezig', 'count' => $repairs->where('status', 'bezig')->count(), 'color' => 'bg-blue-500', 'light' => 'bg-blue-100'],
                            ['name' => 'Wachten op onderdeel', 'count' => $waitingParts, 'color' => 'bg-orange-500', 'light' => 'bg-orange-100'],
                            ['name' => 'Gereed', 'count' => $completedRepairs, 'color' => 'bg-green-500', 'light' => 'bg-green-100']
                        ];
                    @endphp
                    
                    @foreach($statusData as $status)
                        @php
                            $percentage = $totalRepairs > 0 ? ($status['count'] / $totalRepairs) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between p-4 {{ $status['light'] }} rounded-xl">
                            <div class="flex items-center">
                                <div class="w-4 h-4 {{ $status['color'] }} rounded-full mr-3"></div>
                                <span class="font-medium text-gray-900">{{ $status['name'] }}</span>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-900">{{ $status['count'] }}</div>
                                <div class="text-sm text-gray-600">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fa-solid fa-clock text-cyan-600 mr-3"></i>
                    Recente Activiteit
                </h3>
                
                <div class="space-y-4">
                    @foreach($repairs->sortByDesc('updated_at')->take(5) as $repair)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ Str::limit($repair->description, 40) }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ $repair->car->license_plate ?? '—' }} • {{ $repair->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            @php
                                $statusColors = [
                                    'gepland' => 'bg-yellow-100 text-yellow-800',
                                    'bezig' => 'bg-blue-100 text-blue-800',
                                    'wachten_op_onderdeel' => 'bg-orange-100 text-orange-800',
                                    'gereed' => 'bg-green-100 text-green-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$repair->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Cars by Repairs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fa-solid fa-car text-blue-600 mr-3"></i>
                    Meeste Reparaties per Auto
                </h3>
                
                @php
                    $carStats = $repairs->groupBy('car_id')->map(function($repairs, $carId) {
                        $car = $repairs->first()->car;
                        return [
                            'car' => $car,
                            'count' => $repairs->count(),
                            'value' => $repairs->sum('cost_estimate') + $repairs->sum(function($r) { return $r->parts->sum('price'); })
                        ];
                    })->sortByDesc('count')->take(5);
                @endphp
                
                <div class="space-y-4">
                    @forelse($carStats as $stat)
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $stat['car']->license_plate ?? '—' }}</div>
                                <div class="text-sm text-gray-600">{{ ($stat['car']->brand ?? '') . ' ' . ($stat['car']->model ?? '') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">{{ $stat['count'] }} reparaties</div>
                                <div class="text-sm text-gray-600">€{{ number_format($stat['value'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Geen data beschikbaar</p>
                    @endforelse
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fa-solid fa-calendar text-purple-600 mr-3"></i>
                    Maandelijke Trends
                </h3>
                
                @php
                    $monthlyData = $repairs->groupBy(function($repair) {
                        return $repair->created_at->format('Y-m');
                    })->map(function($repairs, $month) {
                        return [
                            'month' => $month,
                            'count' => $repairs->count(),
                            'value' => $repairs->sum('cost_estimate')
                        ];
                    })->sortByDesc('month')->take(6);
                @endphp
                
                <div class="space-y-4">
                    @forelse($monthlyData as $data)
                        <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ \Carbon\Carbon::createFromFormat('Y-m', $data['month'])->format('F Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-purple-600">{{ $data['count'] }} reparaties</div>
                                <div class="text-sm text-gray-600">€{{ number_format($data['value'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Geen data beschikbaar</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-teal-50 to-cyan-50 border border-teal-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-teal-900 mb-4 flex items-center">
                <i class="fa-solid fa-bolt text-yellow-500 mr-2"></i>
                Snelle Acties
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('repairs.create') }}" class="block p-4 bg-white rounded-xl border border-teal-200 hover:border-teal-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-plus text-teal-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Nieuwe Reparatie</div>
                            <div class="text-sm text-gray-600">Voeg een reparatie toe</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('repairs.index') }}?status=wachten_op_onderdeel" class="block p-4 bg-white rounded-xl border border-teal-200 hover:border-teal-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-clock text-orange-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Wachtende Reparaties</div>
                            <div class="text-sm text-gray-600">{{ $waitingParts }} wachten op onderdelen</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('repairs.index') }}?status=bezig" class="block p-4 bg-white rounded-xl border border-teal-200 hover:border-teal-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-cog text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Actieve Reparaties</div>
                            <div class="text-sm text-gray-600">{{ $activeRepairs }} in behandeling</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slideInUp {
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.bg-white {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.bg-white:nth-child(1) { animation-delay: 0.1s; }
.bg-white:nth-child(2) { animation-delay: 0.2s; }
.bg-white:nth-child(3) { animation-delay: 0.3s; }
.bg-white:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection
