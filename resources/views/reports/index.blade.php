@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📊 Bedrijfsrapportage</h1>
            <p class="text-gray-600">Compleet overzicht van je automotive business prestaties</p>
            
            <!-- Quick Summary Bar -->
            <div class="mt-6 bg-gradient-to-r from-blue-600 via-purple-600 to-green-600 rounded-2xl p-6 text-white">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold number-highlight text-white">{{ $recentSalesData['total_recent_sales'] }}</div>
                        <div class="text-sm opacity-90">Verkopen (30d)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold number-highlight text-white">€{{ number_format($kpis['revenue_this_month'] / 1000, 0) }}k</div>
                        <div class="text-sm opacity-90">Omzet Deze Maand</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold number-highlight text-white">{{ $kpis['active_repairs'] }}</div>
                        <div class="text-sm opacity-90">Actieve Reparaties</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold number-highlight text-white">{{ $customerData['conversion_rate'] }}%</div>
                        <div class="text-sm opacity-90">Conversie Ratio</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cars -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Totaal Auto's</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['total_cars'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fa-solid fa-car text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Sales This Month -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Verkoop Deze Maand</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['sales_this_month'] }}</p>
                        @if($kpis['sales_growth'] > 0)
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fa-solid fa-arrow-up mr-1"></i>
                                +{{ $kpis['sales_growth'] }}%
                            </p>
                        @elseif($kpis['sales_growth'] < 0)
                            <p class="text-sm text-red-600 flex items-center">
                                <i class="fa-solid fa-arrow-down mr-1"></i>
                                {{ $kpis['sales_growth'] }}%
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Geen groei</p>
                        @endif
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fa-solid fa-handshake text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue This Month -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Omzet Deze Maand</p>
                        <p class="text-3xl font-bold text-gray-900">€{{ number_format($kpis['revenue_this_month'], 0, ',', '.') }}</p>
                        @if($kpis['revenue_growth'] > 0)
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fa-solid fa-arrow-up mr-1"></i>
                                +{{ $kpis['revenue_growth'] }}%
                            </p>
                        @elseif($kpis['revenue_growth'] < 0)
                            <p class="text-sm text-red-600 flex items-center">
                                <i class="fa-solid fa-arrow-down mr-1"></i>
                                {{ $kpis['revenue_growth'] }}%
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Geen groei</p>
                        @endif
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fa-solid fa-euro-sign text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Repairs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actieve Reparaties</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['active_repairs'] }}</p>
                        <p class="text-sm text-gray-500">{{ $kpis['avg_days_pipeline'] }} dagen gem. pipeline</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fa-solid fa-wrench text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Pipeline Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🏭 Pipeline Verdeling</h3>
                <div class="space-y-4">
                    @foreach($pipelineData['stage_distribution'] as $stage)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $stage['name'] }}</span>
                                <span class="text-sm text-gray-500">{{ $stage['count'] }} auto's</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $stage['percentage'] }}%"></div>
                            </div>
                            <div class="text-right text-xs text-gray-500 mt-1">{{ $stage['percentage'] }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Maandelijkse Omzet</h3>
                <div class="h-64 flex items-end justify-around space-x-2">
                    @foreach($financialData['monthly_revenue'] as $month)
                        @php
                            $maxRevenue = $financialData['monthly_revenue']->max('revenue');
                            $height = $maxRevenue > 0 ? ($month->revenue / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="bg-gradient-to-t from-green-600 to-green-400 rounded-t chart-bar w-12 transition-all duration-500" 
                                 style="height: {{ $height }}%"
                                 title="€{{ number_format($month->revenue, 0, ',', '.') }}"></div>
                            <div class="text-xs text-gray-500 mt-2 font-medium">{{ date('M', mktime(0, 0, 0, $month->month, 1)) }}</div>
                            <div class="text-xs text-gray-400">{{ $month->sales_count }} 🚗</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Analytics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Bottlenecks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Knelpunten</h3>
                <div class="space-y-3">
                    @forelse($pipelineData['bottlenecks'] as $bottleneck)
                        <div class="border-l-4 border-red-400 pl-4">
                            <p class="font-medium text-gray-900">{{ $bottleneck['stage'] }}</p>
                            <p class="text-sm text-gray-600">{{ $bottleneck['avg_days'] }} dagen gemiddeld</p>
                            <p class="text-xs text-gray-500">{{ $bottleneck['car_count'] }} auto's</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Geen knelpunten gedetecteerd! 🎉</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Repairs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Populaire Reparaties</h3>
                <div class="space-y-3">
                    @forelse($repairData['common_repairs'] as $repair)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($repair->description, 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $repair->frequency }}x uitgevoerd</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">€{{ number_format($repair->avg_cost, 0) }}</p>
                                <p class="text-xs text-gray-500">gemiddeld</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nog geen reparaties uitgevoerd.</p>
                    @endforelse
                </div>
            </div>

            <!-- Brand Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top Merken</h3>
                <div class="space-y-3">
                    @forelse($financialData['brand_performance'] as $brand)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $brand->brand }}</p>
                                <p class="text-xs text-gray-500">{{ $brand->sales_count }} verkocht</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">€{{ number_format($brand->total_revenue, 0) }}</p>
                                <p class="text-xs text-gray-500">€{{ number_format($brand->avg_price, 0) }} gem.</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nog geen verkopen geregistreerd.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Customer & Performance Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Customer Insights -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Klant Inzichten</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $customerData['total_customers'] }}</p>
                        <p class="text-sm text-gray-600">Totaal Klanten</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $customerData['new_customers_month'] }}</p>
                        <p class="text-sm text-gray-600">Nieuwe Deze Maand</p>
                    </div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg mb-4">
                    <p class="text-2xl font-bold text-purple-600">{{ $customerData['conversion_rate'] }}%</p>
                    <p class="text-sm text-gray-600">Conversie Ratio (Afspraak → Verkoop)</p>
                </div>
                
                <h4 class="font-medium text-gray-900 mb-2">Top Klanten</h4>
                <div class="space-y-2">
                    @forelse($customerData['top_customers'] as $customer)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-900">{{ $customer->name }}</span>
                            <span class="text-gray-500">{{ $customer->sales_count }} aankopen</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nog geen klant data beschikbaar.</p>
                    @endforelse
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Prestatie Metrics</h3>
                
                <div class="space-y-6">
                    <div class="border-l-4 border-green-400 pl-4">
                        <p class="text-lg font-bold text-gray-900">{{ $performanceData['cars_completed_week'] }}</p>
                        <p class="text-sm text-gray-600">Auto's voltooid deze week</p>
                    </div>
                    
                    <div class="border-l-4 border-blue-400 pl-4">
                        <p class="text-lg font-bold text-gray-900">{{ $performanceData['upcoming_appointments'] }}</p>
                        <p class="text-sm text-gray-600">Afspraken komende 7 dagen</p>
                    </div>
                    
                    <div class="border-l-4 border-orange-400 pl-4">
                        <p class="text-lg font-bold text-gray-900">{{ $performanceData['cars_awaiting_action'] }}</p>
                        <p class="text-sm text-gray-600">Auto's wachten op actie</p>
                    </div>
                    
                    <div class="border-l-4 border-purple-400 pl-4">
                        <p class="text-lg font-bold text-gray-900">€{{ number_format($financialData['repair_costs'], 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Reparatiekosten deze maand</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Deliveries Alert -->
        @if($pendingDeliveries['total_pending'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-truck text-amber-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-amber-800">⚠️ Wachtende Afleveringen</h3>
                    <p class="text-amber-700">Er zijn {{ $pendingDeliveries['total_pending'] }} volledig betaalde auto's die nog moeten worden afgeleverd.</p>
                    @if($pendingDeliveries['overdue_count'] > 0)
                        <p class="text-red-600 font-medium">{{ $pendingDeliveries['overdue_count'] }} daarvan zijn te laat!</p>
                    @endif
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-amber-200">
                    <thead class="bg-amber-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Auto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Klant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Afleverdatum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-amber-200">
                        @foreach($pendingDeliveries['pending_deliveries'] as $delivery)
                            <tr class="table-hover {{ $delivery['days_overdue'] > 0 ? 'alert-pulse' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $delivery['license_plate'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $delivery['brand_model'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $delivery['customer_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($delivery['status'] === 'contract_signed')
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Contract Getekend</span>
                                    @elseif($delivery['status'] === 'ready_for_delivery')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Klaar voor Aflevering</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($delivery['delivery_date'])
                                        <div class="{{ $delivery['days_overdue'] > 0 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ \Carbon\Carbon::parse($delivery['delivery_date'])->format('d-m-Y') }}
                                        </div>
                                        @if($delivery['days_overdue'] > 0)
                                            <div class="text-xs text-red-500 font-medium">{{ $delivery['days_overdue'] }} dagen te laat ⚠️</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Geen datum gepland</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('sales.show', $delivery['id']) }}" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition-colors duration-200">
                                       Bekijk →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Recent Sales Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">🚗 Recente Verkopen (Afgelopen 30 dagen)</h3>
                <div class="flex space-x-4 text-sm">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $recentSalesData['total_recent_sales'] }}</p>
                        <p class="text-gray-600">Verkocht</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">€{{ number_format($recentSalesData['total_recent_revenue'], 0, ',', '.') }}</p>
                        <p class="text-gray-600">Omzet</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">€{{ number_format($recentSalesData['avg_sale_price'], 0, ',', '.') }}</p>
                        <p class="text-gray-600">Gem. Prijs</p>
                    </div>
                </div>
            </div>
            
            @if($recentSalesData['recent_sales']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verkoopprijs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winst</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verkocht op</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentSalesData['recent_sales'] as $sale)
                                <tr class="table-hover">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sale['license_plate'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $sale['brand_model'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale['customer_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        €{{ number_format($sale['sale_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($sale['profit'] > 0)
                                            <span class="text-green-600 font-semibold">+€{{ number_format($sale['profit'], 0, ',', '.') }}</span>
                                        @elseif($sale['profit'] < 0)
                                            <span class="text-red-600 font-semibold">-€{{ number_format(abs($sale['profit']), 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-500">€0</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sale['sold_at']->format('d-m-Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Geen recente verkopen gevonden.</p>
                    <p class="text-sm text-gray-400">Verkoop een auto om deze sectie te vullen!</p>
                </div>
            @endif
        </div>

        <!-- Stage Completion Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">✅ Fase Voltooiing Overzicht</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pipelineData['completion_rates'] as $stage)
                    <div class=" rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-medium text-gray-900">{{ $stage['stage'] }}</h4>
                            <span class="text-sm font-bold text-gray-600">{{ $stage['completion_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $stage['completion_rate'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ $stage['completed_tasks'] }}/{{ $stage['total_tasks'] }} taken voltooid
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Repair Status Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Reparatie Status Verdeling</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($repairData['repair_status'] as $status)
                    @php
                        $statusColors = [
                            'gepland' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'bezig' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'wachten_op_onderdeel' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'gereed' => 'bg-green-100 text-green-800 border-green-200'
                        ];
                        $colorClass = $statusColors[$status->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    @endphp
                    <div class="border rounded-lg p-4 {{ $colorClass }}">
                        <p class="text-2xl font-bold">{{ $status->count }}</p>
                        <p class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.bg-white {
    animation: slideIn 0.5s ease-out;
}

/* Hover effects */
.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Progressive bar animation */
.progress-bar {
    background: linear-gradient(45deg, #3B82F6, #10B981);
    transition: width 1s ease-in-out;
}

/* Alert pulse animation for overdue items */
.alert-pulse {
    animation: pulse 2s infinite;
}

/* Monthly chart bars */
.chart-bar {
    transition: all 0.3s ease;
    border-radius: 4px 4px 0 0;
}

.chart-bar:hover {
    transform: scaleY(1.1);
    filter: brightness(1.1);
}

/* KPI cards gradient backgrounds */
.kpi-blue { background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); }
.kpi-green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
.kpi-yellow { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
.kpi-orange { background: linear-gradient(135deg, #F97316 0%, #EA580C 100%); }

/* Table hover effects */
.table-hover:hover {
    background: linear-gradient(45deg, rgba(59, 130, 246, 0.05), rgba(16, 185, 129, 0.05));
}

/* Add some sparkle to numbers */
.number-highlight {
    background: linear-gradient(45deg, #3B82F6, #10B981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: bold;
}
</style>
@endsection
