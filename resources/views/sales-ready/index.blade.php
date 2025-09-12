@extends('layouts.app')

@section('content')
<div class="mx-auto py-10 px-4" style="background-color: var(--third-color);">
    <div class="flex justify-between items-center mb-8 p-6 rounded-xl shadow-sm" style="background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)20); border: 1px solid var(--secondary-color);">
        <h1 class="text-3xl font-bold" style="color: var(--primary-color);">✅ Verkoop Klaar</h1>
        <div class="text-sm font-medium px-3 py-1 rounded-full" style="background: linear-gradient(135deg, var(--primary-color)20, var(--secondary-color)20); color: var(--text-kleur-black);">
            {{ $cars->count() }} auto{{ $cars->count() !== 1 ? "'s" : '' }} verkoop klaar
        </div>
    </div>

    @if($cars->count() === 0)
        <div class="text-center py-16 rounded-xl shadow-sm" style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
            <div class="text-6xl mb-4">🚗</div>
            <h3 class="text-xl font-semibold mb-2" style="color: var(--text-kleur-black);">Geen auto's verkoop klaar</h3>
            <p style="color: var(--secondary-color);">Er staan momenteel geen auto's in de "Verkoop klaar" fase.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($cars as $car)
                <div class="rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
                    <!-- Car Header -->
                    <div class="p-6" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-bottom: 1px solid var(--third-color);">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold mb-1" style="color: var(--text-kleur-white);">
                                    {{ $car->license_plate }}
                                </h2>
                                <p class="font-medium" style="color: var(--text-kleur-white);">
                                    {{ $car->brand }} {{ $car->model }} ({{ $car->year }})
                                </p>
                                <p class="text-sm mt-1" style="color: var(--third-color);">
                                    {{ number_format($car->mileage) }} km
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold" style="color: var(--text-kleur-white);">
                                    € {{ number_format($car->price, 2, ',', '.') }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full mt-2" style="background-color: var(--third-color); color: var(--text-kleur-black);">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: var(--primary-color);"></span>
                                    Verkoop klaar
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Tasks Overview -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center" style="color: var(--text-kleur-black);">
                            <i class="fas fa-check-circle mr-2" style="color: var(--primary-color);"></i>
                            Uitgevoerde Werkzaamheden
                        </h3>

                        @if($car->completed_tasks_by_stage->count() === 0)
                            <p class="italic" style="color: var(--secondary-color);">Geen voltooide taken gevonden.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($car->completed_tasks_by_stage as $stageName => $tasks)
                                    <div class="rounded-lg p-4" style="border: 1px solid var(--third-color);">
                                        <h4 class="font-semibold mb-3 flex items-center" style="color: var(--text-kleur-black);">
                                            @if($stageName === 'Intake')
                                                <i class="fas fa-clipboard-list mr-2" style="color: var(--primary-color);"></i>
                                            @elseif($stageName === 'Technische controle')
                                                <i class="fas fa-tools mr-2" style="color: var(--secondary-color);"></i>
                                            @elseif($stageName === 'Herstel & Onderhoud')
                                                <i class="fas fa-wrench mr-2" style="color: var(--primary-color);"></i>
                                            @elseif($stageName === 'Commercieel gereed')
                                                <i class="fas fa-camera mr-2" style="color: var(--secondary-color);"></i>
                                            @else
                                                <i class="fas fa-check mr-2" style="color: var(--secondary-color);"></i>
                                            @endif
                                            {{ $stageName }}
                                            <span class="ml-2 text-xs px-2 py-1 rounded-full" style="background-color: var(--third-color); color: var(--text-kleur-black);">
                                                {{ $tasks->count() }} taken
                                            </span>
                                        </h4>

                                        <div class="space-y-2">
                                            @foreach($tasks as $task)
                                                <div class="flex items-start">
                                                    <i class="fas fa-check mr-3 mt-1 text-sm" style="color: var(--primary-color);"></i>
                                                    <div class="flex-1">
                                                        <span style="color: var(--text-kleur-black);">{{ $task->task }}</span>
                                                        @if($task->repair)
                                                            <div class="flex items-center mt-1">
                                                                <span class="text-xs px-2 py-1 rounded-full mr-2" style="background-color: var(--third-color); color: var(--text-kleur-black);">
                                                                    <i class="fas fa-wrench mr-1"></i>
                                                                    Reparatie
                                                                </span>
                                                                <span class="text-xs" style="color: var(--secondary-color);">
                                                                    Status: {{ ucfirst($task->repair->status) }}
                                                                    @if($task->repair->cost_estimate)
                                                                        • €{{ number_format($task->repair->cost_estimate, 2, ',', '.') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div class="text-xs mt-1" style="color: var(--secondary-color);">
                                                            Voltooid op {{ $task->updated_at->format('d-m-Y H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Quick Stats -->
                        <div class="mt-6 pt-4" style="border-top: 1px solid var(--third-color);">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold" style="color: var(--primary-color);">
                                        {{ $car->checklists->count() }}
                                    </div>
                                    <div class="text-xs" style="color: var(--secondary-color);">Totaal taken</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold" style="color: var(--primary-color);">
                                        {{ $car->checklists->where('repair_id', '!=', null)->count() }}
                                    </div>
                                    <div class="text-xs" style="color: var(--secondary-color);">Reparaties</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold" style="color: var(--primary-color);">
                                        {{ $car->completed_tasks_by_stage->count() }}
                                    </div>
                                    <div class="text-xs" style="color: var(--secondary-color);">Fases doorlopen</div>
                                </div>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('sales-ready.pdf', $car) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition duration-200 shadow-sm hover:shadow-md"
                                       style="background-color: var(--primary-color); color: var(--text-kleur-white);"
                                       onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                                       onmouseout="this.style.backgroundColor='var(--primary-color)'">
                                        <i class="fas fa-file-pdf mr-2"></i>
                                        PDF
                                    </a>
                                
                                </div>
                                <div></div>
                                <div>
                                    <form method="POST" action="{{ route('sales-ready.email', $car) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition duration-200 shadow-sm hover:shadow-md"
                                                style="background-color: var(--secondary-color); color: var(--text-kleur-white);"
                                                onmouseover="this.style.backgroundColor='var(--primary-color)'"
                                                onmouseout="this.style.backgroundColor='var(--secondary-color)'"
                                                onclick="return confirm('Weet je zeker dat je het rapport wilt e-mailen?')">
                                            <i class="fas fa-envelope mr-2"></i>
                                            E-mail
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
