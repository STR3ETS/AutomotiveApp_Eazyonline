@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Checklist: {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                </h1>
                <p class="text-gray-600">Fase: {{ $car->stage->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600" id="completion-percentage">{{ $car->stage_completion }}%</div>
                <div class="text-sm text-gray-500">Voltooid</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                     id="progress-bar"
                     style="width: {{ $car->stage_completion }}%"></div>
            </div>
        </div>

        <!-- Checklist Items -->
        <div class="space-y-4" x-data="checklistManager()">
            @forelse($car->checklists as $item)
                <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors checklist-item"
                     data-item-id="{{ $item->id }}"
                     :class="{ 'bg-green-50 border-green-200': checklistStates[{{ $item->id }}] }">
                    <label class="flex items-center cursor-pointer w-full">
                        <input type="checkbox" 
                               class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                               {{ $item->is_completed ? 'checked' : '' }}
                               @change="updateChecklistItem({{ $item->id }}, $event.target.checked)"
                               :disabled="updating">
                        <span class="ml-3 text-gray-900 flex-1" 
                              :class="{ 'line-through text-gray-500': checklistStates[{{ $item->id }}] }">
                            {{ $item->task }}
                        </span>
                        <span x-show="checklistStates[{{ $item->id }}]" class="text-green-600 text-sm">✓ Voltooid</span>
                        <span x-show="updating && updatingItem === {{ $item->id }}" class="text-blue-600 text-sm">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </label>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Geen checklist items gevonden voor deze fase.</p>
                </div>
            @endforelse
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('pipeline.index') }}" 
               class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                ← Terug naar Pipeline
            </a>
            
            <div id="status-indicator">
                @if($car->canMoveToNextStage())
                    <div class="text-green-600 font-semibold flex items-center">
                        <span class="mr-2">✓</span> Klaar om naar volgende fase te verplaatsen
                    </div>
                @else
                    <div class="text-orange-600 font-semibold flex items-center">
                        <span class="mr-2">⚠</span> Voltooi alle taken om door te gaan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function checklistManager() {
    return {
        updating: false,
        updatingItem: null,
        updateQueue: new Map(),
        debounceTimer: null,
        checklistStates: {
            @foreach($car->checklists as $item)
                {{ $item->id }}: {{ $item->is_completed ? 'true' : 'false' }},
            @endforeach
        },
        
        updateChecklistItem(checklistId, isCompleted) {
            // Prevent multiple rapid clicks
            if (this.updating && this.updatingItem === checklistId) {
                return;
            }
            
            // Update local state immediately for better UX
            this.checklistStates[checklistId] = isCompleted;
            
            // Queue the update
            this.updateQueue.set(checklistId, isCompleted);
            
            // Debounce the actual API call
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }
            
            this.debounceTimer = setTimeout(() => {
                this.processUpdateQueue();
            }, 300); // 300ms debounce
        },
        
        async processUpdateQueue() {
            if (this.updateQueue.size === 0) return;
            
            this.updating = true;
            
            // Process all queued updates
            const updates = Array.from(this.updateQueue.entries());
            this.updateQueue.clear();
            
            try {
                // Process updates sequentially to avoid race conditions
                for (const [checklistId, isCompleted] of updates) {
                    this.updatingItem = checklistId;
                    
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
                        // Update progress bar
                        const progressBar = document.getElementById('progress-bar');
                        const progressText = document.getElementById('completion-percentage');
                        
                        if (progressBar && progressText) {
                            progressBar.style.width = data.completion + '%';
                            progressText.textContent = data.completion + '%';
                        }
                        
                        // Update status indicator
                        const statusIndicator = document.getElementById('status-indicator');
                        if (statusIndicator && data.can_move !== undefined) {
                            if (data.can_move) {
                                statusIndicator.innerHTML = `
                                    <div class="text-green-600 font-semibold flex items-center">
                                        <span class="mr-2">✓</span> Klaar om naar volgende fase te verplaatsen
                                    </div>
                                `;
                            } else {
                                statusIndicator.innerHTML = `
                                    <div class="text-orange-600 font-semibold flex items-center">
                                        <span class="mr-2">⚠</span> Voltooi alle taken om door te gaan
                                    </div>
                                `;
                            }
                        }
                    } else {
                        // Revert local state on error
                        this.checklistStates[checklistId] = !isCompleted;
                        alert('Er is een fout opgetreden bij het bijwerken van de checklist.');
                    }
                }
            } catch (error) {
                console.error('Error updating checklist items:', error);
                alert('Er is een fout opgetreden bij het bijwerken van de checklist.');
                
                // Revert all states on error
                for (const [checklistId] of updates) {
                    this.checklistStates[checklistId] = !this.checklistStates[checklistId];
                }
            } finally {
                this.updating = false;
                this.updatingItem = null;
            }
        }
    }
}
</script>
@endsection
