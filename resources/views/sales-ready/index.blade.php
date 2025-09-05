@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-[var(--text-primary)]">Verkoop Klaar</h1>
        <div class="bg-[var(--status-info-light)] text-[var(--status-info-text)] text-sm font-medium px-3 py-1 rounded-full">
            {{ $cars->count() }} auto{{ $cars->count() !== 1 ? "'s" : '' }} verkoop klaar
        </div>
    </div>

    @if($cars->count() === 0)
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🚗</div>
            <h3 class="text-xl font-semibold text-[var(--text-primary)] mb-2">Geen auto's verkoop klaar</h3>
            <p class="text-[var(--text-secondary)]">Er staan momenteel geen auto's in de "Verkoop klaar" fase.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($cars as $car)
                <div class="bg-[var(--background-card)] rounded-xl shadow-lg border border-[var(--border-light)] overflow-hidden">
                    <!-- Car Header -->
                    <div class="bg-gradient-to-r from-[var(--gradient-green-start)] to-[var(--gradient-green-end)] p-6 border-b border-[var(--status-success-border)]">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-[var(--status-success-text)] mb-1">
                                    {{ $car->license_plate }}
                                </h2>
                                <p class="text-[var(--status-success-dark)] font-medium">
                                    {{ $car->brand }} {{ $car->model }} ({{ $car->year }})
                                </p>
                                <p class="text-[var(--status-success)] text-sm mt-1">
                                    {{ number_format($car->mileage) }} km
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-[var(--status-success-text)]">
                                    € {{ number_format($car->price, 2, ',', '.') }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1 text-xs font-medium bg-[var(--status-success-light)] text-[var(--status-success-text)] rounded-full mt-2">
                                    <span class="w-2 h-2 bg-[var(--status-success)] rounded-full mr-2"></span>
                                    Verkoop klaar
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Tasks Overview -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Uitgevoerde Werkzaamheden
                        </h3>

                        @if($car->completed_tasks_by_stage->count() === 0)
                            <p class="text-gray-500 italic">Geen voltooide taken gevonden.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($car->completed_tasks_by_stage as $stageName => $tasks)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                            @if($stageName === 'Intake')
                                                <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                                            @elseif($stageName === 'Technische controle')
                                                <i class="fas fa-tools text-orange-500 mr-2"></i>
                                            @elseif($stageName === 'Herstel & Onderhoud')
                                                <i class="fas fa-wrench text-red-500 mr-2"></i>
                                            @elseif($stageName === 'Commercieel gereed')
                                                <i class="fas fa-camera text-purple-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-check text-gray-500 mr-2"></i>
                                            @endif
                                            {{ $stageName }}
                                            <span class="ml-2 bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                                {{ $tasks->count() }} taken
                                            </span>
                                        </h4>

                                        <div class="space-y-2">
                                            @foreach($tasks as $task)
                                                <div class="flex items-start">
                                                    <i class="fas fa-check text-green-500 mr-3 mt-1 text-sm"></i>
                                                    <div class="flex-1">
                                                        <span class="text-gray-700">{{ $task->task }}</span>
                                                        @if($task->repair)
                                                            <div class="flex items-center mt-1">
                                                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full mr-2">
                                                                    <i class="fas fa-wrench mr-1"></i>
                                                                    Reparatie
                                                                </span>
                                                                <span class="text-xs text-gray-500">
                                                                    Status: {{ ucfirst($task->repair->status) }}
                                                                    @if($task->repair->cost_estimate)
                                                                        • €{{ number_format($task->repair->cost_estimate, 2, ',', '.') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div class="text-xs text-gray-400 mt-1">
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
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">
                                        {{ $car->checklists->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Totaal taken</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $car->checklists->where('repair_id', '!=', null)->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Reparaties</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ $car->completed_tasks_by_stage->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Fases doorlopen</div>
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
