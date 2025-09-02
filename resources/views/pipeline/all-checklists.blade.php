@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Alle Checklists: {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                </h1>
                <p class="text-gray-600">Huidige fase: {{ $car->stage->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">{{ $car->stage_completion }}%</div>
                <div class="text-sm text-gray-500">Huidige fase voltooid</div>
            </div>
        </div>

        <!-- Alle fases met hun checklists -->
        <div class="space-y-8" x-data="checklistManager()">
            @foreach($stages as $stage)
                @php
                    $stageChecklists = $car->checklists->where('stage_id', $stage->id);
                    $completed = $stageChecklists->where('is_completed', true)->count();
                    $total = $stageChecklists->count();
                    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                    $isCurrent = $car->stage_id === $stage->id;
                @endphp
                
                <div class="border rounded-lg p-6 {{ $isCurrent ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <h3 class="text-lg font-semibold {{ $isCurrent ? 'text-blue-900' : 'text-gray-900' }}">
                                {{ $stage->name }}
                                @if($isCurrent)
                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-500 text-white rounded-full">Huidige fase</span>
                                @endif
                            </h3>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold {{ $percentage === 100 ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $percentage }}%
                            </div>
                            <div class="text-sm text-gray-500">{{ $completed }}/{{ $total }} voltooid</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ $percentage === 100 ? 'bg-green-500' : ($isCurrent ? 'bg-blue-500' : 'bg-gray-400') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Checklist Items -->
                    <div class="space-y-3">
                        @forelse($stageChecklists as $item)
                            <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors {{ $item->is_completed ? 'bg-green-50 border-green-200' : 'border-gray-200' }}">
                                <label class="flex items-center cursor-pointer w-full {{ !$isCurrent ? 'opacity-60' : '' }}">
                                    <input type="checkbox" 
                                           class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                           {{ $item->is_completed ? 'checked' : '' }}
                                           {{ !$isCurrent ? 'disabled' : '' }}
                                           @if($isCurrent) @change="updateChecklistItem({{ $item->id }}, $event.target.checked)" @endif>
                                    <span class="ml-3 text-gray-900 flex-1 {{ $item->is_completed ? 'line-through text-gray-500' : '' }}">
                                        {{ $item->task }}
                                    </span>
                                    @if($item->is_completed)
                                        <span class="text-green-600 text-sm">✓ Voltooid</span>
                                    @elseif(!$isCurrent)
                                        <span class="text-gray-400 text-sm">🔒 Vergrendeld</span>
                                    @endif
                                </label>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <p>Geen checklist items voor deze fase.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(!$isCurrent && $percentage < 100)
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-800 text-sm">
                                <strong>Let op:</strong> Deze fase kan alleen worden bewerkt wanneer de auto zich in deze fase bevindt.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('pipeline.index') }}" 
               class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                ← Terug naar Pipeline
            </a>
            
            @if($car->canMoveToNextStage())
                <div class="text-green-600 font-semibold flex items-center">
                    <span class="mr-2">✓</span> Klaar om naar volgende fase te verplaatsen
                </div>
            @else
                <div class="text-orange-600 font-semibold flex items-center">
                    <span class="mr-2">⚠</span> Voltooi alle taken in de huidige fase om door te gaan
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function checklistManager() {
    return {
        async updateChecklistItem(checklistId, isCompleted) {
            try {
                const response = await fetch(`/pipeline/checklist/${checklistId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_completed: isCompleted
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update progress bar en reload page voor betere UX
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } catch (error) {
                console.error('Error updating checklist item:', error);
                alert('Er is een fout opgetreden bij het bijwerken van de checklist.');
            }
        }
    }
}
</script>
@endsection
