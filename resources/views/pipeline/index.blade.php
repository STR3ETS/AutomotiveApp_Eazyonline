@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-full mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏭 Voorraad Pipeline</h1>
            <p class="text-gray-600">Sleep auto's tussen de verschillende fases om je voorraad te beheren</p>
        </div>
        
        <div class="space-y-8" x-data="pipelineDrag()">
            @foreach($stages as $stage)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-stage" 
                     x-data="{ stageId: {{ $stage->id }} }" 
                     @dragover.prevent 
                     @drop="onDrop($event, stageId)"
                     :class="{ 'ring-2 ring-blue-300 bg-blue-50': draggedCarId && hoveredStage === stageId }"
                     @dragenter="hoveredStage = stageId"
                     @dragleave="if ($event.target === $el) hoveredStage = null">
                    
                    <!-- Stage Header -->
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                @if($stage->name === 'Intake')
                                    <i class="fa-solid fa-clipboard-list text-white text-lg"></i>
                                @elseif($stage->name === 'Technische controle')
                                    <i class="fa-solid fa-tools text-white text-lg"></i>
                                @elseif($stage->name === 'Herstel & Onderhoud')
                                    <i class="fa-solid fa-wrench text-white text-lg"></i>
                                @elseif($stage->name === 'Commercieel gereed')
                                    <i class="fa-solid fa-camera text-white text-lg"></i>
                                @elseif($stage->name === 'Verkoop klaar')
                                    <i class="fa-solid fa-handshake text-white text-lg"></i>
                                @else
                                    <i class="fa-solid fa-car text-white text-lg"></i>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $stage->name }}</h2>
                                <p class="text-sm text-gray-600">{{ $stage->description ?? 'Fase in het productieproces' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-sm font-semibold px-4 py-2 rounded-full shadow-sm">
                                {{ $stage->cars->count() }} auto{{ $stage->cars->count() !== 1 ? "'s" : '' }}
                            </span>
                        </div>
                    </div>
                
                <!-- Horizontal scrolling car cards -->
                <div class="overflow-x-auto">
                    <div class="flex gap-4 pb-4" style="min-width: max-content;">
                    @forelse($stage->cars as $car)
                        @php
                            $completion = $car->stage_completion;
                            $canMove = $car->canMoveToNextStage();
                        @endphp
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-4 cursor-move border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-300 w-72 flex-shrink-0 car-card" 
                             draggable="true" 
                             @dragstart="onDragStart($event, {{ $car->id }})"
                             @dragend="onDragEnd()"
                             :class="{ 'opacity-50 transform rotate-1': draggedCarId === {{ $car->id }} }">
                            
                            <!-- Car Image -->
                            @if($car->images->where('is_primary', true)->first())
                                <div class="mb-3 relative overflow-hidden rounded-lg">
                                    <a href="{{ route('autos.show', $car) }}" @click.stop>
                                        <img src="{{ $car->images->where('is_primary', true)->first()->thumbnail_url }}" 
                                             alt="{{ $car->license_plate }}"
                                             class="w-full h-32 object-cover hover:scale-105 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                            Bekijk details
                                        </div>
                                    </a>
                                </div>
                            @else
                                <div class="mb-3 bg-gray-100 rounded-lg h-32 flex items-center justify-center">
                                    <a href="{{ route('autos.show', $car) }}" @click.stop class="text-center">
                                        <i class="fa-solid fa-image text-gray-400 text-2xl mb-2 block"></i>
                                        <span class="text-xs text-gray-500">Geen foto</span>
                                    </a>
                                </div>
                            @endif
                            
                            <!-- Car Header -->
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-car text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm truncate">
                                        {{ $car->license_plate }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $car->brand }} {{ $car->model }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <div class="text-lg font-bold text-green-600">
                                    €{{ number_format($car->price, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            <!-- Progress Section -->
                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-medium text-gray-700 mb-2">
                                    <span>Checklist voortgang</span>
                                    <span class="text-blue-600">{{ $completion }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500 {{ $completion === 100 ? 'bg-gradient-to-r from-green-400 to-green-500' : 'bg-gradient-to-r from-blue-400 to-blue-500' }}" 
                                         style="width: {{ $completion }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count() }} / 
                                    {{ $car->checklists->where('stage_id', $stage->id)->count() }} taken voltooid
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex justify-between items-center">
                                @if($canMove)
                                    <div class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Klaar voor volgende fase
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-gradient-to-r from-yellow-100 to-orange-200 text-orange-800 rounded-full">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                                        In bewerking
                                    </div>
                                @endif
                                
                                <a href="{{ route('pipeline.checklist', $car) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200 transition-all duration-200"
                                   @click.stop>
                                    <i class="fa-solid fa-list-check mr-1"></i>
                                    Checklist
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-12 px-8 border-2 border-dashed border-gray-200 rounded-xl hover:border-gray-300 transition-colors duration-300 min-w-[300px]">
                            <div class="text-3xl mb-2">📋</div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Geen auto's in deze fase</div>
                            <div class="text-xs text-gray-500">Sleep een auto hiernaartoe om te beginnen</div>
                        </div>
                    @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideInDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInRight {
    from { transform: translateX(-30px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(1deg); }
    75% { transform: rotate(-1deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Stage row animations */
.hover-stage {
    animation: slideInDown 0.6s ease-out;
}

.hover-stage:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Car card animations */
.car-card {
    animation: slideInRight 0.5s ease-out;
}

.car-card:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Drag effects */
.car-card[draggable="true"]:active {
    animation: wiggle 0.2s ease-in-out infinite;
}

/* Staggered animations for stages */
.hover-stage:nth-child(1) { animation-delay: 0.1s; }
.hover-stage:nth-child(2) { animation-delay: 0.2s; }
.hover-stage:nth-child(3) { animation-delay: 0.3s; }
.hover-stage:nth-child(4) { animation-delay: 0.4s; }
.hover-stage:nth-child(5) { animation-delay: 0.5s; }

/* Staggered animations for cards */
.car-card:nth-child(1) { animation-delay: 0.1s; }
.car-card:nth-child(2) { animation-delay: 0.15s; }
.car-card:nth-child(3) { animation-delay: 0.2s; }
.car-card:nth-child(4) { animation-delay: 0.25s; }
.car-card:nth-child(5) { animation-delay: 0.3s; }

/* Progress bar animation */
.car-card .h-2 {
    transition: width 0.8s ease-in-out;
}

/* Success/Error message styling */
.fixed.top-4.right-4 {
    animation: slideInDown 0.3s ease-out;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Drag hover effects */
.ring-2.ring-blue-300 {
    animation: pulse 1s ease-in-out infinite;
}

/* Horizontal scrolling enhancement */
.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .car-card {
        width: 260px !important;
    }
}
</style>

<script>
function pipelineDrag() {
    return {
        draggedCarId: null,
        hoveredStage: null,
        scrollInterval: null,
        
        onDragStart(e, carId) {
            this.draggedCarId = carId;
            e.dataTransfer.effectAllowed = 'move';
            this.startAutoScroll();
        },
        
        onDragEnd() {
            this.draggedCarId = null;
            this.hoveredStage = null;
            this.stopAutoScroll();
        },
        
        startAutoScroll() {
            this.scrollInterval = setInterval(() => {
                if (!this.draggedCarId) return;
                
                const mouseY = window.dragY || 0;
                const scrollContainer = document.documentElement;
                const scrollSpeed = 10;
                const scrollZone = 100; // pixels from edge
                
                // Scroll up
                if (mouseY < scrollZone) {
                    scrollContainer.scrollTop -= scrollSpeed;
                }
                // Scroll down
                else if (mouseY > window.innerHeight - scrollZone) {
                    scrollContainer.scrollTop += scrollSpeed;
                }
            }, 16); // ~60fps
        },
        
        stopAutoScroll() {
            if (this.scrollInterval) {
                clearInterval(this.scrollInterval);
                this.scrollInterval = null;
            }
        },
        
        async onDrop(e, stageId) {
            e.preventDefault();
            this.hoveredStage = null;
            this.stopAutoScroll();
            
            if (!this.draggedCarId) return;
            
            try {
                const response = await fetch('/pipeline/move', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        car_id: this.draggedCarId, 
                        stage_id: stageId 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Toon success bericht
                    if (data.message) {
                        // Voeg een tijdelijke success melding toe
                        const successDiv = document.createElement('div');
                        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                        successDiv.innerHTML = `<i class="fa-solid fa-check-circle mr-2"></i>${data.message}`;
                        document.body.appendChild(successDiv);
                        
                        // Verwijder melding na 3 seconden
                        setTimeout(() => {
                            successDiv.remove();
                        }, 3000);
                    }
                    
                    // Herlaad de pagina om de nieuwe positie te tonen
                    window.location.reload();
                } else {
                    // Toon error bericht in een mooie modal
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50 max-w-md';
                    errorDiv.innerHTML = `<i class="fa-solid fa-exclamation-circle mr-2"></i>${data.message}`;
                    document.body.appendChild(errorDiv);
                    
                    // Verwijder melding na 5 seconden
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            } catch (error) {
                console.error('Error moving car:', error);
                
                // Toon error bericht
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
                errorDiv.innerHTML = '<i class="fa-solid fa-exclamation-circle mr-2"></i>Er is een fout opgetreden bij het verplaatsen van de auto.';
                document.body.appendChild(errorDiv);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
            
            this.draggedCarId = null;
        }
    }
}

// Global mouse tracking for auto-scroll
document.addEventListener('dragover', (e) => {
    window.dragY = e.clientY;
});
</script>
@endsection
