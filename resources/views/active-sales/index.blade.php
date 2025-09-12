@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 32px; color: var(--text-kleur-black);">Actieve Verkoop & Oplevering</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Proefritten deze week -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color);">
            <div style="padding: 24px; border-bottom: 1px solid var(--third-color); background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px 12px 0 0;">
                <h2 style="font-size: 20px; font-weight: 700; color: var(--text-kleur-white); display: flex; align-items: center;">
                    <i class="fas fa-car" style="color: var(--text-kleur-white); margin-right: 12px;"></i>
                    Geplande Proefritten
                    <span style="margin-left: 8px; background-color: rgba(255,255,255,0.2); color: var(--text-kleur-white); font-size: 14px; padding: 4px 8px; border-radius: 20px;">{{ $testDrives->count() }}</span>
                </h2>
            </div>
            <div style="padding: 24px;">
                @if($testDrives->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($testDrives as $appointment)
                            <div style="display: flex; align-items: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px; border: 1px solid var(--secondary-color);">
                                <div style="flex-shrink: 0; width: 12px; height: 12px; background-color: var(--primary-color); border-radius: 50%; margin-right: 16px;"></div>
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div>
                                            <h3 style="font-weight: 600; color: var(--text-kleur-black);">
                                                @if($appointment->car)
                                                    {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                                @endif
                                            </h3>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                Klant: {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                            </p>
                                            @if($appointment->notes)
                                                <p style="color: var(--secondary-color); font-size: 14px;">{{ $appointment->notes }}</p>
                                            @endif
                                        </div>
                                        <span style="font-size: 14px; font-weight: 500; color: var(--primary-color);">{{ $appointment->day_label }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 32px 0; color: var(--secondary-color);">
                        <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 16px; color: var(--third-color);"></i>
                        <p>Geen proefritten gepland deze week</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Verkochte auto's (nog niet opgeleverd) -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color);">
            <div style="padding: 24px; border-bottom: 1px solid var(--third-color); background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px 12px 0 0;">
                <h2 style="font-size: 20px; font-weight: 700; color: var(--text-kleur-white); display: flex; align-items: center;">
                    <i class="fas fa-handshake" style="color: var(--text-kleur-white); margin-right: 12px;"></i>
                    Verkochte Auto's
                    <span style="margin-left: 8px; background-color: rgba(255,255,255,0.2); color: var(--text-kleur-white); font-size: 14px; padding: 4px 8px; border-radius: 20px;">{{ $soldCars->count() }}</span>
                </h2>
                <p style="font-size: 14px; color: rgba(255,255,255,0.8); margin-top: 4px;">Wachten op oplevering</p>
            </div>
            <div style="padding: 24px;">
                @if($soldCars->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($soldCars as $sale)
                            <div style="display: flex; align-items: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px; border: 1px solid var(--secondary-color);">
                                <div style="flex-shrink: 0; width: 12px; height: 12px; background-color: var(--primary-color); border-radius: 50%; margin-right: 16px;"></div>
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div>
                                            <h3 style="font-weight: 600; color: var(--text-kleur-black);">
                                                @if($sale->car)
                                                    {{ $sale->car->brand }} {{ $sale->car->model }}
                                                @endif
                                            </h3>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                Verkocht aan: {{ $sale->customer ? $sale->customer->name : 'Onbekende klant' }}
                                            </p>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                Verkoopdatum: {{ \Carbon\Carbon::parse($sale->sold_at)->format('d-m-Y') }}
                                            </p>
                                        </div>
                                        <span style="font-size: 18px; font-weight: 700; color: var(--primary-color);">€{{ number_format($sale->sale_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 32px 0; color: var(--secondary-color);">
                        <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 16px; color: var(--third-color);"></i>
                        <p>Geen verkochte auto's wachtend op oplevering</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Geplande ophalingen/afleveringen -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color);">
            <div style="padding: 24px; border-bottom: 1px solid var(--third-color); background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px 12px 0 0;">
                <h2 style="font-size: 20px; font-weight: 700; color: var(--text-kleur-white); display: flex; align-items: center;">
                    <i class="fas fa-truck" style="color: var(--text-kleur-white); margin-right: 12px;"></i>
                    Geplande Afleveringen
                    <span style="margin-left: 8px; background-color: rgba(255,255,255,0.2); color: var(--text-kleur-white); font-size: 14px; padding: 4px 8px; border-radius: 20px;">{{ $deliveries->count() }}</span>
                </h2>
            </div>
            <div style="padding: 24px;">
                @if($deliveries->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($deliveries as $appointment)
                            <div style="display: flex; align-items: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px; border: 1px solid var(--secondary-color);">
                                <div style="flex-shrink: 0; width: 12px; height: 12px; background-color: var(--primary-color); border-radius: 50%; margin-right: 16px;"></div>
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div>
                                            <h3 style="font-weight: 600; color: var(--text-kleur-black);">
                                                @if($appointment->car)
                                                    {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                                @endif
                                            </h3>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                Aflevering aan: {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                            </p>
                                            @if($appointment->notes)
                                                <p style="color: var(--secondary-color); font-size: 14px;">{{ $appointment->notes }}</p>
                                            @endif
                                        </div>
                                        <span style="font-size: 14px; font-weight: 500; color: var(--primary-color);">{{ $appointment->day_label }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 32px 0; color: var(--secondary-color);">
                        <i class="fas fa-truck-loading" style="font-size: 48px; margin-bottom: 16px; color: var(--third-color);"></i>
                        <p>Geen afleveringen gepland deze week</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Auto's klaar voor verkoop -->
        <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color);">
            <div style="padding: 24px; border-bottom: 1px solid var(--third-color); background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px 12px 0 0;">
                <h2 style="font-size: 20px; font-weight: 700; color: var(--text-kleur-white); display: flex; align-items: center;">
                    <i class="fas fa-tag" style="color: var(--text-kleur-white); margin-right: 12px;"></i>
                    Klaar voor Verkoop
                    <span style="margin-left: 8px; background-color: rgba(255,255,255,0.2); color: var(--text-kleur-white); font-size: 14px; padding: 4px 8px; border-radius: 20px;">{{ $readyForSale->count() }}</span>
                </h2>
                <p style="font-size: 14px; color: rgba(255,255,255,0.8); margin-top: 4px;">Auto's in "Verkoop klaar" fase</p>
            </div>
            <div style="padding: 24px;">
                @if($readyForSale->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($readyForSale as $car)
                            <div style="display: flex; align-items: center; padding: 16px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px; border: 1px solid var(--secondary-color);">
                                <div style="flex-shrink: 0; width: 12px; height: 12px; background-color: var(--primary-color); border-radius: 50%; margin-right: 16px;"></div>
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div>
                                            <h3 style="font-weight: 600; color: var(--text-kleur-black);">
                                                {{ $car->brand }} {{ $car->model }}
                                            </h3>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                {{ $car->license_plate ?? $car->kenteken }} • {{ $car->year ?? 'Onbekend jaar' }}
                                            </p>
                                            <p style="color: var(--secondary-color); font-size: 14px;">
                                                Vraagprijs: €{{ number_format($car->price ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <span style="font-size: 14px; padding: 6px 12px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); border-radius: 20px; font-weight: 500;">
                                            Verkoop klaar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 32px 0; color: var(--secondary-color);">
                        <i class="fas fa-car-side" style="font-size: 48px; margin-bottom: 16px; color: var(--third-color);"></i>
                        <p>Geen auto's klaar voor verkoop</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
