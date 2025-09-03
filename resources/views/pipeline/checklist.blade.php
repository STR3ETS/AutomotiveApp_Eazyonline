@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="mb-6">
        <a href="{{ route('pipeline.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            ← Terug naar Pipeline
        </a>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}</h1>
                    <p class="text-gray-600">Huidige fase: <span class="font-semibold text-blue-600">{{ $car->stage->name }}</span></p>
                    <p class="text-gray-600">Prijs: <span class="font-semibold">€ {{ number_format($car->price, 2, ',', '.') }}</span></p>
                </div>
                
                @php
                    $totalTasks = $currentStageChecklists->count();
                    $completedTasks = $currentStageChecklists->where('is_completed', true)->count();
                    $progressPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                @endphp
                
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-2">Voortgang huidige fase</div>
                    <div class="text-2xl font-bold {{ $progressPercentage === 100 ? 'text-green-600' : 'text-blue-600' }}">
                        {{ $completedTasks }}/{{ $totalTasks }}
                    </div>
                    <div class="w-32 bg-gray-200 rounded-full h-2 mt-2">
                        <div class="{{ $progressPercentage === 100 ? 'bg-green-600' : 'bg-blue-600' }} h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <!-- Phase Progress Overview -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Fase voortgang</h2>
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    @foreach($stages as $stage)
                        @php
                            $stageTasks = $car->checklists->where('stage_id', $stage->id);
                            $stageCompleted = $stageTasks->count() > 0 && $stageTasks->where('is_completed', true)->count() === $stageTasks->count();
                            $isCurrent = $stage->id === $car->stage_id;
                        @endphp
                        
                        <div class="flex-shrink-0 text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold mb-2 
                                {{ $isCurrent ? 'bg-blue-600 text-white' : ($stageCompleted ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600') }}">
                                {{ $loop->iteration }}
                            </div>
                            <div class="text-xs text-gray-600 w-20">{{ $stage->name }}</div>
                        </div>
                        
                        @if(!$loop->last)
                            <div class="flex items-center pt-6">
                                <div class="w-8 h-1 {{ $stageCompleted ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Current Stage Checklist -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Checklist voor {{ $car->stage->name }}</h2>
                
                @if($currentStageChecklists->count() > 0)
                    <div class="space-y-3">
                        @foreach($currentStageChecklists as $checklist)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <input type="checkbox" 
                                       id="task-{{ $checklist->id }}"
                                       {{ $checklist->is_completed ? 'checked' : '' }}
                                       onchange="toggleTask({{ $checklist->id }})"
                                       class="h-5 w-5 text-blue-600 rounded border-2 border-gray-300 focus:ring-blue-500">
                                       
                                <label for="task-{{ $checklist->id }}" 
                                       class="ml-3 flex-1 text-gray-900 {{ $checklist->is_completed ? 'line-through text-gray-500' : '' }} cursor-pointer">
                                    {{ $checklist->task }}
                                </label>
                                
                                @if($checklist->is_completed)
                                    <span class="text-green-600 text-sm">✓ Voltooid</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-500 text-center py-8">
                        Nog geen taken voor deze fase. Voeg hieronder taken toe.
                    </div>
                @endif
            </div>

            <!-- Add New Task -->
            <div class="mb-8 bg-gray-50 rounded-lg p-4">
                <h3 class="text-md font-semibold mb-3 text-gray-900">Nieuwe taak toevoegen</h3>
                <form method="POST" action="{{ route('pipeline.checklist.add-task', $car->id) }}" class="flex gap-3">
                    @csrf
                    <input type="text" 
                           name="task" 
                           required 
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Beschrijf de nieuwe taak...">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        Toevoegen
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('pipeline.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    Terug naar Pipeline
                </a>
                
                @if($progressPercentage === 100)
                    @php
                        $nextStage = $stages->where('order', '>', $car->stage->order)->first();
                    @endphp
                    
                    @if($nextStage)
                        <form method="POST" action="{{ route('pipeline.move') }}" class="inline">
                            @csrf
                            <input type="hidden" name="car_id" value="{{ $car->id }}">
                            <input type="hidden" name="stage_id" value="{{ $nextStage->id }}">
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                                Verplaats naar {{ $nextStage->name }}
                            </button>
                        </form>
                    @else
                        <div class="text-green-600 font-semibold">
                            ✓ Auto is klaar voor verkoop!
                        </div>
                    @endif
                @else
                    <div class="text-gray-500">
                        Voltooi alle taken om door te gaan naar de volgende fase
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
async function toggleTask(checklistId) {
    try {
        const response = await fetch('/pipeline/checklist/{{ $car->id }}/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ checklist_id: checklistId })
        });
        
        if (response.ok) {
            // Reload page to update progress
            window.location.reload();
        } else {
            alert('Er ging iets mis bij het bijwerken van de taak.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Er ging iets mis bij het bijwerken van de taak.');
    }
}
</script>
@endsection
