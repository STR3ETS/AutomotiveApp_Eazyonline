@extends('layouts.app')

@section('content')
<div style="background-color: var(--text-kleur-white); min-height: 100vh;">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black); margin-bottom: 8px;">📊 Bedrijfsrapportage</h1>
            <p style="color: var(--secondary-color);">Compleet overzicht van je automotive business prestaties</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cars -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="font-size: 14px; font-weight: 500; color: var(--secondary-color);">Totaal Auto's</p>
                        <p style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black);">{{ $kpis['total_cars'] }}</p>
                    </div>
                    <div style="padding: 12px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%;">
                        <i class="fa-solid fa-car" style="color: var(--text-kleur-white); font-size: 20px;"></i>
                    </div>
                </div>
            </div>

            <!-- Sales This Month -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="font-size: 14px; font-weight: 500; color: var(--secondary-color);">Verkoop Deze Maand</p>
                        <p style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black);">{{ $kpis['sales_this_month'] }}</p>
                        @if($kpis['sales_growth'] > 0)
                            <p style="font-size: 14px; color: #22c55e; display: flex; align-items: center;">
                                <i class="fa-solid fa-arrow-up mr-1"></i>
                                +{{ $kpis['sales_growth'] }}%
                            </p>
                        @elseif($kpis['sales_growth'] < 0)
                            <p style="font-size: 14px; color: #ef4444; display: flex; align-items: center;">
                                <i class="fa-solid fa-arrow-down mr-1"></i>
                                {{ $kpis['sales_growth'] }}%
                            </p>
                        @else
                            <p style="font-size: 14px; color: var(--secondary-color);">Geen groei</p>
                        @endif
                    </div>
                    <div style="padding: 12px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%;">
                        <i class="fa-solid fa-handshake" style="color: var(--text-kleur-white); font-size: 20px;"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue This Month -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="font-size: 14px; font-weight: 500; color: var(--secondary-color);">Omzet Deze Maand</p>
                        <p style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black);">€{{ number_format($kpis['revenue_this_month'], 0, ',', '.') }}</p>
                        @if($kpis['revenue_growth'] > 0)
                            <p style="font-size: 14px; color: #22c55e; display: flex; align-items: center;">
                                <i class="fa-solid fa-arrow-up mr-1"></i>
                                +{{ $kpis['revenue_growth'] }}%
                            </p>
                        @elseif($kpis['revenue_growth'] < 0)
                            <p style="font-size: 14px; color: #ef4444; display: flex; align-items: center;">
                                <i class="fa-solid fa-arrow-down mr-1"></i>
                                {{ $kpis['revenue_growth'] }}%
                            </p>
                        @else
                            <p style="font-size: 14px; color: var(--secondary-color);">Geen groei</p>
                        @endif
                    </div>
                    <div style="padding: 12px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%;">
                        <i class="fa-solid fa-euro-sign" style="color: var(--text-kleur-white); font-size: 20px;"></i>
                    </div>
                </div>
            </div>

            <!-- Active Repairs -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="font-size: 14px; font-weight: 500; color: var(--secondary-color);">Actieve Reparaties</p>
                        <p style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black);">{{ $kpis['active_repairs'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">{{ $kpis['avg_days_pipeline'] }} dagen gem. pipeline</p>
                    </div>
                    <div style="padding: 12px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%;">
                        <i class="fa-solid fa-wrench" style="color: var(--text-kleur-white); font-size: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Pipeline Distribution -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">🏭 Pipeline Verdeling</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($pipelineData['stage_distribution'] as $stage)
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">{{ $stage['name'] }}</span>
                                <span style="font-size: 14px; color: var(--secondary-color);">{{ $stage['count'] }} auto's</span>
                            </div>
                            <div style="width: 100%; background-color: var(--third-color); border-radius: 20px; height: 8px;">
                                <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); height: 8px; border-radius: 20px; transition: all 0.5s ease; width: {{ $stage['percentage'] }}%;"></div>
                            </div>
                            <div style="text-align: right; font-size: 12px; color: var(--secondary-color); margin-top: 4px;">{{ $stage['percentage'] }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">💰 Maandelijkse Omzet</h3>
                <div class="h-64 flex items-end justify-around space-x-2">
                    @foreach($financialData['monthly_revenue'] as $month)
                        @php
                            $maxRevenue = $financialData['monthly_revenue']->max('revenue');
                            $height = $maxRevenue > 0 ? ($month->revenue / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 8px 8px 0 0; width: 48px; transition: all 0.5s ease; height: {{ $height }}%;" 
                                 title="€{{ number_format($month->revenue, 0, ',', '.') }}"></div>
                            <div style="font-size: 12px; color: var(--secondary-color); margin-top: 8px;">{{ date('M', mktime(0, 0, 0, $month->month, 1)) }}</div>
                            <div style="font-size: 12px; color: var(--third-color);">{{ $month->sales_count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Analytics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Bottlenecks -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">🚨 Knelpunten</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($pipelineData['bottlenecks'] as $bottleneck)
                        <div style="border-left: 4px solid #ef4444; padding-left: 16px;">
                            <p style="font-weight: 500; color: var(--text-kleur-black);">{{ $bottleneck['stage'] }}</p>
                            <p style="font-size: 14px; color: var(--secondary-color);">{{ $bottleneck['avg_days'] }} dagen gemiddeld</p>
                            <p style="font-size: 12px; color: var(--secondary-color);">{{ $bottleneck['car_count'] }} auto's</p>
                        </div>
                    @empty
                        <p style="color: var(--secondary-color); font-size: 14px;">Geen knelpunten gedetecteerd! 🎉</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Repairs -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">🔧 Populaire Reparaties</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($repairData['common_repairs'] as $repair)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">{{ Str::limit($repair->description, 20) }}</p>
                                <p style="font-size: 12px; color: var(--secondary-color);">{{ $repair->frequency }}x uitgevoerd</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">€{{ number_format($repair->avg_cost, 0) }}</p>
                                <p style="font-size: 12px; color: var(--secondary-color);">gemiddeld</p>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--secondary-color); font-size: 14px;">Nog geen reparaties uitgevoerd.</p>
                    @endforelse
                </div>
            </div>

            <!-- Brand Performance -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">🏆 Top Merken</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($financialData['brand_performance'] as $brand)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">{{ $brand->brand }}</p>
                                <p style="font-size: 12px; color: var(--secondary-color);">{{ $brand->sales_count }} verkocht</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">€{{ number_format($brand->total_revenue, 0) }}</p>
                                <p style="font-size: 12px; color: var(--secondary-color);">€{{ number_format($brand->avg_price, 0) }} gem.</p>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--secondary-color); font-size: 14px;">Nog geen verkopen geregistreerd.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Customer & Performance Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Customer Insights -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">👥 Klant Inzichten</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div style="text-align: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px;">
                        <p style="font-size: 24px; font-weight: 700; color: var(--primary-color);">{{ $customerData['total_customers'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Totaal Klanten</p>
                    </div>
                    <div style="text-align: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px;">
                        <p style="font-size: 24px; font-weight: 700; color: var(--primary-color);">{{ $customerData['new_customers_month'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Nieuwe Deze Maand</p>
                    </div>
                </div>
                <div style="text-align: center; padding: 16px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 8px; margin-bottom: 16px;">
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-kleur-white);">{{ $customerData['conversion_rate'] }}%</p>
                    <p style="font-size: 14px; color: rgba(255,255,255,0.8);">Conversie Ratio (Afspraak → Verkoop)</p>
                </div>
                
                <h4 style="font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Top Klanten</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @forelse($customerData['top_customers'] as $customer)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                            <span style="color: var(--text-kleur-black);">{{ $customer->name }}</span>
                            <span style="color: var(--secondary-color);">{{ $customer->sales_count }} aankopen</span>
                        </div>
                    @empty
                        <p style="color: var(--secondary-color); font-size: 14px;">Nog geen klant data beschikbaar.</p>
                    @endforelse
                </div>
            </div>

            <!-- Performance Metrics -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">📈 Prestatie Metrics</h3>
                
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="border-left: 4px solid #22c55e; padding-left: 16px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--text-kleur-black);">{{ $performanceData['cars_completed_week'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Auto's voltooid deze week</p>
                    </div>
                    
                    <div style="border-left: 4px solid var(--primary-color); padding-left: 16px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--text-kleur-black);">{{ $performanceData['upcoming_appointments'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Afspraken komende 7 dagen</p>
                    </div>
                    
                    <div style="border-left: 4px solid #f97316; padding-left: 16px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--text-kleur-black);">{{ $performanceData['cars_awaiting_action'] }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Auto's wachten op actie</p>
                    </div>
                    
                    <div style="border-left: 4px solid var(--secondary-color); padding-left: 16px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--text-kleur-black);">€{{ number_format($financialData['repair_costs'], 0, ',', '.') }}</p>
                        <p style="font-size: 14px; color: var(--secondary-color);">Reparatiekosten deze maand</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Completion Overview -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px; margin-bottom: 32px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">✅ Fase Voltooiing Overzicht</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pipelineData['completion_rates'] as $stage)
                    <div style="border-radius: 8px; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white));">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <h4 style="font-weight: 500; color: var(--text-kleur-black);">{{ $stage['stage'] }}</h4>
                            <span style="font-size: 14px; font-weight: 700; color: var(--primary-color);">{{ $stage['completion_rate'] }}%</span>
                        </div>
                        <div style="width: 100%; background-color: var(--third-color); border-radius: 20px; height: 8px; margin-bottom: 8px;">
                            <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); height: 8px; border-radius: 20px; transition: all 0.5s ease; width: {{ $stage['completion_rate'] }}%;"></div>
                        </div>
                        <p style="font-size: 12px; color: var(--secondary-color);">
                            {{ $stage['completed_tasks'] }}/{{ $stage['total_tasks'] }} taken voltooid
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Repair Status Distribution -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-kleur-black); margin-bottom: 16px;">🔧 Reparatie Status Verdeling</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($repairData['repair_status'] as $status)
                    @php
                        $statusColors = [
                            'gepland' => 'background: linear-gradient(135deg, #fbbf24, #f59e0b); color: var(--text-kleur-white);',
                            'bezig' => 'background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);',
                            'wachten_op_onderdeel' => 'background: linear-gradient(135deg, #f97316, #ea580c); color: var(--text-kleur-white);',
                            'gereed' => 'background: linear-gradient(135deg, #22c55e, #16a34a); color: var(--text-kleur-white);'
                        ];
                        $colorStyle = $statusColors[$status->status] ?? 'background-color: var(--third-color); color: var(--text-kleur-black);';
                    @endphp
                    <div style="border: 1px solid var(--third-color); border-radius: 8px; padding: 16px; {{ $colorStyle }}">
                        <p style="font-size: 24px; font-weight: 700;">{{ $status->count }}</p>
                        <p style="font-size: 14px; font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</p>
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

[style*="background-color: var(--text-kleur-white)"] {
    animation: slideIn 0.5s ease-out;
}

/* Hover effects */
[style*="background-color: var(--text-kleur-white)"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}
</style>
@endsection
