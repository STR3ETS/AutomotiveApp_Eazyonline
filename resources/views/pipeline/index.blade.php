@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Pipeline - Kanban Board</h1>
    
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if(session('error') || $errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-{{ count($stages) }} gap-6" id="kanban-board">
        @foreach($stages as $stage)
            <div class="kanban-column bg-gray-100 rounded-xl p-4 min-h-[500px]" 
                 data-stage-id="{{ $stage->id }}"
                 ondrop="drop(event)" 
                 ondragover="allowDrop(event)">
                 
                <h2 class="font-semibold text-lg mb-4 text-gray-800 flex items-center justify-between">
                    {{ $stage->name }}
                    <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        {{ $stage->cars->count() }}
                    </span>
                </h2>
                
                <div class="space-y-3">
                    @forelse($stage->cars as $car)
                        @php
                            $totalTasks = $car->checklists->where('stage_id', $stage->id)->count();
                            $completedTasks = $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count();
                            $allTasksCompleted = $totalTasks > 0 && $completedTasks === $totalTasks;
                            $progressPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                        @endphp
                        
                        <div class="kanban-card bg-white rounded-lg shadow-md p-4 cursor-move border-2 {{ $allTasksCompleted ? 'border-green-400 bg-green-50' : 'border-gray-200' }}" 
                             draggable="true" 
                             data-car-id="{{ $car->id }}"
                             data-can-move="{{ $allTasksCompleted ? 'true' : 'false' }}"
                             ondragstart="dragStart(event)">
                             
                            <div class="font-bold text-gray-900 mb-1">
                                {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                € {{ number_format($car->price, 2, ',', '.') }}
                            </div>
                            
                            <!-- Progress bar -->
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-700 mb-1">
                                    <span>Checklist</span>
                                    <span>{{ $completedTasks }}/{{ $totalTasks }} ({{ round($progressPercentage) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $allTasksCompleted ? 'bg-green-500' : 'bg-blue-500' }}" 
                                         style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                            
                            @if($allTasksCompleted)
                                <div class="text-xs text-green-700 font-semibold mb-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Klaar voor verplaatsing
                                </div>
                            @else
                                <div class="text-xs text-orange-600 font-semibold mb-2">
                                    Checklist nog niet voltooid
                                </div>
                            @endif
                            
                            <div class="flex gap-2">
                                <a href="{{ route('pipeline.checklist', $car->id) }}" 
                                   class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition"
                                   onclick="event.stopPropagation();">
                                   Checklist
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <div class="mt-2">Geen auto's in deze fase</div>
                            <div class="text-xs mt-1">Sleep hier een auto naartoe</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Loading overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 flex items-center">
            <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Auto wordt verplaatst...
        </div>
    </div>
</div>

<style>
.kanban-column.drag-over {
    background-color: #e0f2fe !important;
    border: 2px dashed #0288d1 !important;
}

.kanban-column.drag-over-valid {
    background-color: #e8f5e8 !important;
    border: 2px dashed #4caf50 !important;
}

.kanban-column.drag-over-invalid {
    background-color: #ffebee !important;
    border: 2px dashed #f44336 !important;
}

.kanban-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}
</style>

<script>
let draggedElement = null;
let draggedCarId = null;
let canMoveCard = false;

function dragStart(event) {
    draggedElement = event.target;
    draggedCarId = event.target.getAttribute('data-car-id');
    canMoveCard = event.target.getAttribute('data-can-move') === 'true';
    
    event.target.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', event.target.outerHTML);
    
    console.log('Drag started:', { carId: draggedCarId, canMove: canMoveCard });
}

function allowDrop(event) {
    event.preventDefault();
    
    const column = event.currentTarget;
    const stageId = column.getAttribute('data-stage-id');
    
    // Remove all drag over classes first
    document.querySelectorAll('.kanban-column').forEach(col => {
        col.classList.remove('drag-over', 'drag-over-valid', 'drag-over-invalid');
    });
    
    // Add appropriate class based on whether move is allowed
    if (canMoveCard) {
        column.classList.add('drag-over-valid');
        event.dataTransfer.dropEffect = 'move';
    } else {
        column.classList.add('drag-over-invalid');
        event.dataTransfer.dropEffect = 'none';
    }
}

function drop(event) {
    event.preventDefault();
    
    const column = event.currentTarget;
    const stageId = column.getAttribute('data-stage-id');
    
    // Remove all drag over classes
    document.querySelectorAll('.kanban-column').forEach(col => {
        col.classList.remove('drag-over', 'drag-over-valid', 'drag-over-invalid');
    });
    
    if (draggedElement) {
        draggedElement.classList.remove('dragging');
    }
    
    if (!draggedCarId) {
        console.error('No car ID found');
        return;
    }
    
    if (!canMoveCard) {
        showNotification('Deze auto kan nog niet worden verplaatst. Voltooi eerst alle taken in de huidige fase.', 'error');
        return;
    }
    
    console.log('Dropping car', draggedCarId, 'to stage', stageId);
    
    // Show loading
    document.getElementById('loading-overlay').classList.remove('hidden');
    
    // Send AJAX request
    fetch('/pipeline/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            car_id: draggedCarId,
            stage_id: stageId
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        document.getElementById('loading-overlay').classList.add('hidden');
        
        if (data.success) {
            showNotification('Auto succesvol verplaatst!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Er ging iets mis bij het verplaatsen van de auto.', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('loading-overlay').classList.add('hidden');
        showNotification('Er ging iets mis bij het verplaatsen van de auto.', 'error');
    });
    
    // Reset drag state
    draggedElement = null;
    draggedCarId = null;
    canMoveCard = false;
}

// Handle drag leave to clean up classes
document.addEventListener('dragleave', function(event) {
    if (event.target.classList.contains('kanban-column')) {
        setTimeout(() => {
            if (!event.target.matches(':hover')) {
                event.target.classList.remove('drag-over', 'drag-over-valid', 'drag-over-invalid');
            }
        }, 100);
    }
});

// Handle drag end
document.addEventListener('dragend', function(event) {
    if (event.target.classList.contains('kanban-card')) {
        event.target.classList.remove('dragging');
        
        // Clean up all drag over classes
        document.querySelectorAll('.kanban-column').forEach(col => {
            col.classList.remove('drag-over', 'drag-over-valid', 'drag-over-invalid');
        });
    }
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endsection
