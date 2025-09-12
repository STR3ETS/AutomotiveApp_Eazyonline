@extends('layouts.app')

@section('content')
<div class="min-h-full" style="background-color: var(--third-color);">
    <div class="max-w-full mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 p-6 rounded-xl shadow-sm" style="background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)20); border: 1px solid var(--secondary-color);">
            <h1 class="text-3xl font-bold mb-2" style="color: var(--primary-color);">🏭 Voorraad Pipeline</h1>
            <p style="color: var(--secondary-color);">Sleep auto's tussen de verschillende fases om je voorraad te beheren</p>
        </div>
        
        <div class="space-y-8" x-data="pipelineDrag()">
            @foreach($stages as $stage)
                <div class="rounded-xl shadow-sm p-6 hover-stage" 
                     style="background-color: var(--text-kleur-white); border: 1px solid var(--secondary-color);"
                     x-data="{ stageId: {{ $stage->id }} }" 
                     @dragover.prevent 
                     @drop="onDrop($event, stageId)"
                     :class="{ 'ring-2': draggedCarId && hoveredStage === stageId }"
                     :style="draggedCarId && hoveredStage === stageId ? 'background-color: var(--third-color);' : ''"
                     @dragenter="hoveredStage = stageId"
                     @dragleave="if ($event.target === $el) hoveredStage = null">
                    
                    <!-- Stage Header -->
                    <div class="flex items-center justify-between mb-6 pb-4" style="border-bottom: 1px solid var(--third-color);">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                                @if($stage->name === 'Intake')
                                    <i class="fa-solid fa-clipboard-list text-lg" style="color: var(--text-kleur-white);"></i>
                                @elseif($stage->name === 'Technische controle')
                                    <i class="fa-solid fa-tools text-lg" style="color: var(--text-kleur-white);"></i>
                                @elseif($stage->name === 'Herstel & Onderhoud')
                                    <i class="fa-solid fa-wrench text-lg" style="color: var(--text-kleur-white);"></i>
                                @elseif($stage->name === 'Commercieel gereed')
                                    <i class="fa-solid fa-camera text-lg" style="color: var(--text-kleur-white);"></i>
                                @elseif($stage->name === 'Verkoop klaar')
                                    <i class="fa-solid fa-handshake text-lg" style="color: var(--text-kleur-white);"></i>
                                @else
                                    <i class="fa-solid fa-car text-lg" style="color: var(--text-kleur-white);"></i>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold" style="color: var(--text-kleur-black);">{{ $stage->name }}</h2>
                                <p class="text-sm" style="color: var(--secondary-color);">{{ $stage->description ?? 'Fase in het productieproces' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold px-4 py-2 rounded-full shadow-sm" 
                                  style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20); color: var(--text-kleur-black);">
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
                        <div class="rounded-xl shadow-sm p-4 cursor-move transition-all duration-300 w-72 flex-shrink-0 car-card hover:shadow-lg" 
                             style="background: linear-gradient(135deg, var(--text-kleur-white), var(--third-color)20); border: 1px solid var(--third-color);"
                             draggable="true" 
                             @dragstart="onDragStart($event, {{ $car->id }})"
                             @dragend="onDragEnd()"
                             :class="{ 'opacity-50 transform rotate-1': draggedCarId === {{ $car->id }} }"
                             onmouseover="this.style.borderColor='var(--primary-color)'"
                             onmouseout="this.style.borderColor='var(--third-color)'">
                            
                            <!-- Car Image -->
                            @if($car->images->where('is_primary', true)->first())
                                <div class="mb-3 relative overflow-hidden rounded-lg">
                                    <a href="{{ route('autos.show', $car) }}" @click.stop>
                                        <img src="{{ $car->images->where('is_primary', true)->first()->thumbnail_url }}" 
                                             alt="{{ $car->license_plate }}"
                                             class="w-full h-32 object-cover hover:scale-105 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                        <div class="absolute bottom-2 right-2 text-xs px-2 py-1 rounded" 
                                             style="background-color: var(--primary-color); color: var(--text-kleur-white);">
                                            Bekijk details
                                        </div>
                                    </a>
                                </div>
                            @else
                                <div class="mb-3 rounded-lg h-32 flex items-center justify-center" style="background-color: var(--third-color);">
                                    <a href="{{ route('autos.show', $car) }}" @click.stop class="text-center">
                                        <i class="fa-solid fa-image text-2xl mb-2 block" style="color: var(--secondary-color);"></i>
                                        <span class="text-xs" style="color: var(--secondary-color);">Geen foto</span>
                                    </a>
                                </div>
                            @endif
                            
                            <!-- Car Header -->
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20);">
                                    <i class="fa-solid fa-car" style="color: var(--primary-color);"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-sm truncate" style="color: var(--text-kleur-black);">
                                        {{ $car->license_plate }}
                                    </div>
                                    <div class="text-xs" style="color: var(--secondary-color);">
                                        {{ $car->brand }} {{ $car->model }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <div class="text-lg font-bold" style="color: var(--primary-color);">
                                    €{{ number_format($car->price, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            <!-- Progress Section -->
                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-medium mb-2" style="color: var(--text-kleur-black);">
                                    <span>Checklist voortgang</span>
                                    <span style="color: var(--primary-color);">{{ $completion }}%</span>
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
                                    <div style="display: inline-flex; align-items: center; padding: 4px 8px; font-size: 12px; font-weight: 600; background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.3)); color: #16a34a; border-radius: 20px;">
                                        <span style="width: 8px; height: 8px; background-color: #22c55e; border-radius: 50%; margin-right: 4px;"></span>
                                        Klaar voor volgende fase
                                    </div>
                                @else
                                    <div style="display: inline-flex; align-items: center; padding: 4px 8px; font-size: 12px; font-weight: 600; background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(249, 115, 22, 0.3)); color: #ea580c; border-radius: 20px;">
                                        <span style="width: 8px; height: 8px; background-color: #f97316; border-radius: 50%; margin-right: 4px;"></span>
                                        In bewerking
                                    </div>
                                @endif
                                
                                <a href="{{ route('pipeline.checklist', $car) }}" 
                                   style="display: inline-flex; align-items: center; padding: 4px 12px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); color: var(--primary-color); border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='var(--text-kleur-white)'"
                                   onmouseout="this.style.background='linear-gradient(135deg, var(--third-color), var(--text-kleur-white))'; this.style.color='var(--primary-color)'"
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
                        successDiv.style.cssText = 'position: fixed; top: 16px; right: 16px; background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: rgba(22, 163, 74, 1); padding: 12px 16px; border-radius: 8px; z-index: 50;';
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
                    errorDiv.style.cssText = 'position: fixed; top: 16px; right: 16px; background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: rgba(220, 38, 38, 1); padding: 12px 16px; border-radius: 8px; z-index: 50; max-width: 400px;';
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
                errorDiv.style.cssText = 'position: fixed; top: 16px; right: 16px; background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: rgba(220, 38, 38, 1); padding: 12px 16px; border-radius: 8px; z-index: 50;';
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
