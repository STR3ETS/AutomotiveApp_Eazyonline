@extends('layouts.app')
@section('content')
<div class="min-h-full" style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20);">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Modern Header with Actions -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-4xl font-bold mb-3" style="color: var(--primary-color);">
                    🔧 Werkplaats Dashboard
                </h1>
                <p class="text-lg" style="color: var(--secondary-color);">Moderne reparatie- en onderdeelbeheer</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('repairs.create') }}" 
                   class="inline-flex items-center px-6 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Nieuwe Reparatie
                </a>
                <a href="{{ route('repairs.analytics') }}" 
                   class="inline-flex items-center px-6 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, var(--secondary-color), var(--third-color)); color: var(--text-kleur-black);">
                    <i class="fa-solid fa-chart-line mr-2"></i>
                    Analytics
                </a>
            </div>
        </div>

        {{-- Flash messages with improved styling --}}
        @if(session('success'))
            <div class="mb-8 p-4 rounded-2xl flex items-center shadow-sm" 
                 style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20); border: 1px solid var(--secondary-color); color: var(--text-kleur-black);">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3" style="background-color: var(--primary-color);">
                    <i class="fa-solid fa-check text-sm" style="color: var(--text-kleur-white);"></i>
                </div>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-8 p-4 rounded-2xl shadow-sm" 
                 style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20); border: 1px solid var(--primary-color); color: var(--text-kleur-black);">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3" style="background-color: var(--primary-color);">
                        <i class="fa-solid fa-exclamation text-sm" style="color: var(--text-kleur-white);"></i>
                    </div>
                    <span class="font-semibold">Er zijn fouten opgetreden:</span>
                </div>
                <ul class="list-disc pl-11 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @php
                $totalRepairs = $repairs->count();
                $activeRepairs = $repairs->whereIn('status', ['gepland', 'bezig'])->count();
                $completedRepairs = $repairs->where('status', 'gereed')->count();
                $waitingParts = $repairs->where('status', 'wachten_op_onderdeel')->count();
                $totalValue = $repairs->sum('cost_estimate');
            @endphp
            
            <div class="rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300" 
                 style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-1" style="color: var(--secondary-color);">Totaal Reparaties</p>
                        <p class="text-3xl font-bold" style="color: var(--text-kleur-black);">{{ $totalRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: var(--third-color);">
                        <i class="fa-solid fa-wrench text-xl" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300" 
                 style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-1" style="color: var(--secondary-color);">Actief</p>
                        <p class="text-3xl font-bold" style="color: var(--primary-color);">{{ $activeRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: var(--third-color);">
                        <i class="fa-solid fa-cog text-xl" style="color: var(--secondary-color);"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300" 
                 style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-1" style="color: var(--secondary-color);">Afgerond</p>
                        <p class="text-3xl font-bold" style="color: var(--primary-color);">{{ $completedRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: var(--third-color);">
                        <i class="fa-solid fa-check-circle text-xl" style="color: var(--secondary-color);"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300" 
                 style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-1" style="color: var(--secondary-color);">Totale Waarde</p>
                        <p class="text-2xl font-bold" style="color: var(--primary-color);">€{{ number_format($totalValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: var(--third-color);">
                        <i class="fa-solid fa-euro-sign text-xl" style="color: var(--secondary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Repairs List -->
        <div class="rounded-2xl shadow-sm overflow-hidden" style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
            <div class="px-8 py-6" style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20); border-bottom: 1px solid var(--secondary-color);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-bold mb-4 sm:mb-0" style="color: var(--text-kleur-black);">📋 Alle Reparaties</h2>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center rounded-lg px-3 py-2 shadow-sm" style="background-color: var(--text-kleur-white); border: 1px solid var(--secondary-color);">
                            <i class="fa-solid fa-filter mr-2" style="color: var(--secondary-color);"></i>
                            <select class="border-none focus:ring-0 text-sm bg-transparent" id="statusFilter" style="color: var(--text-kleur-black);">
                                <option value="">Alle statussen</option>
                                <option value="gepland">Gepland</option>
                                <option value="bezig">Bezig</option>
                                <option value="wachten_op_onderdeel">Wachten op onderdeel</option>
                                <option value="gereed">Gereed</option>
                            </select>
                        </div>
                        <div class="flex items-center rounded-lg px-3 py-2 shadow-sm" style="background-color: var(--text-kleur-white); border: 1px solid var(--secondary-color);">
                            <i class="fa-solid fa-search mr-2" style="color: var(--secondary-color);"></i>
                            <input type="text" placeholder="Zoek reparatie..." class="border-none focus:ring-0 text-sm bg-transparent w-32" id="searchInput" style="color: var(--text-kleur-black);">
                        </div>
                    </div>
                </div>
            </div>

            <div style="border-bottom: 1px solid var(--third-color);">
                @forelse($repairs as $repair)
                    @php
                        $statusConfig = [
                            'gepland' => ['bg' => 'var(--third-color)', 'text' => 'var(--text-kleur-black)', 'icon' => 'fa-calendar', 'label' => 'Gepland'],
                            'bezig' => ['bg' => 'var(--secondary-color)', 'text' => 'var(--text-kleur-white)', 'icon' => 'fa-cog', 'label' => 'Bezig'],
                            'wachten_op_onderdeel' => ['bg' => 'var(--primary-color)', 'text' => 'var(--text-kleur-white)', 'icon' => 'fa-clock', 'label' => 'Wachten op onderdeel'],
                            'gereed' => ['bg' => 'var(--secondary-color)', 'text' => 'var(--text-kleur-white)', 'icon' => 'fa-check-circle', 'label' => 'Gereed']
                        ];
                        $config = $statusConfig[$repair->status] ?? $statusConfig['gepland'];
                    @endphp
                    
                    <div class="repair-item hover:shadow-md transition-all duration-300" 
                         data-status="{{ $repair->status }}"
                         style="hover:background: linear-gradient(135deg, var(--third-color)30, transparent);"
                         onmouseover="this.style.background='linear-gradient(135deg, var(--third-color), transparent)'"
                         onmouseout="this.style.background='transparent'">
                        <div class="p-8">
                            <!-- Header Row -->
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-6">
                                <div class="flex-1 mb-4 lg:mb-0">
                                    <div class="flex items-center space-x-4 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold" 
                                              style="background-color: {{ $config['bg'] }}; color: {{ $config['text'] }};">
                                            <i class="fa-solid {{ $config['icon'] }} mr-2"></i>
                                            {{ $config['label'] }}
                                        </span>
                                        <span class="text-sm px-2 py-1 rounded-md" style="background-color: var(--third-color); color: var(--text-kleur-black);">
                                            #{{ $repair->id }}
                                        </span>
                                        <span class="text-sm" style="color: var(--secondary-color);">
                                            {{ $repair->created_at->format('d-m-Y H:i') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-bold mb-2" style="color: var(--text-kleur-black);">{{ $repair->description }}</h3>
                                    
                                    <div class="flex items-center space-x-6 text-sm" style="color: var(--secondary-color);">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-car mr-2" style="color: var(--secondary-color);"></i>
                                            <span class="font-medium">{{ $repair->car->license_plate ?? '—' }}</span>
                                            <span class="ml-2">{{ ($repair->car->brand ?? '') . ' ' . ($repair->car->model ?? '') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-euro-sign mr-2" style="color: var(--secondary-color);"></i>
                                            <span class="font-bold text-lg" style="color: var(--text-kleur-black);">
                                                {{ $repair->cost_estimate ? '€' . number_format($repair->cost_estimate, 2, ',', '.') : '—' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-puzzle-piece mr-2" style="color: var(--secondary-color);"></i>
                                            <span>{{ $repair->parts->count() }} onderdelen</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                    <a href="{{ route('repairs.show', $repair->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                                       style="background-color: var(--primary-color); color: var(--text-kleur-white);"
                                       onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                                       onmouseout="this.style.backgroundColor='var(--primary-color)'">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        Bekijken
                                    </a>
                                    <a href="{{ route('repairs.edit', $repair->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                                       style="background-color: var(--secondary-color); color: var(--text-kleur-white);"
                                       onmouseover="this.style.backgroundColor='var(--primary-color)'"
                                       onmouseout="this.style.backgroundColor='var(--secondary-color)'">
                                        <i class="fa-solid fa-edit mr-2"></i>
                                        Bewerken
                                    </a>
                                    <a href="{{ route('repairs.parts.index', $repair->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                                       style="background-color: var(--third-color); color: var(--text-kleur-black);"
                                       onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='var(--text-kleur-white)'"
                                       onmouseout="this.style.backgroundColor='var(--third-color)'; this.style.color='var(--text-kleur-black)'">
                                        <i class="fa-solid fa-puzzle-piece mr-2"></i>
                                        Onderdelen
                                    </a>
                                </div>
                            </div>

                            <!-- Parts Preview (if any) -->
                            @if($repair->parts->count() > 0)
                                <div class="rounded-xl p-4" style="background-color: var(--third-color); border: 1px solid var(--secondary-color);">
                                    <h4 class="text-sm font-semibold mb-3 flex items-center" style="color: var(--text-kleur-black);">
                                        <i class="fa-solid fa-list mr-2"></i>
                                        Onderdelen ({{ $repair->parts->count() }})
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($repair->parts->take(3) as $part)
                                            @php
                                                $partStatusConfig = [
                                                    'besteld' => ['bg' => 'var(--third-color)', 'text' => 'var(--text-kleur-black)', 'border' => 'var(--secondary-color)'],
                                                    'geleverd' => ['bg' => 'var(--secondary-color)', 'text' => 'var(--text-kleur-white)', 'border' => 'var(--primary-color)'],
                                                    'gemonteerd' => ['bg' => 'var(--primary-color)', 'text' => 'var(--text-kleur-white)', 'border' => 'var(--secondary-color)']
                                                ];
                                                $partConfig = $partStatusConfig[$part->status] ?? $partStatusConfig['besteld'];
                                            @endphp
                                            <div class="flex items-center justify-between p-3 border rounded-lg" 
                                                 style="background-color: {{ $partConfig['bg'] }}; border-color: {{ $partConfig['border'] }};">
                                                <div class="flex-1">
                                                    <div class="font-medium text-sm" style="color: {{ $partConfig['text'] }};">{{ $part->name }}</div>
                                                    <div class="text-xs capitalize" style="color: var(--secondary-color);">{{ $part->status }}</div>
                                                </div>
                                                <div class="text-sm font-semibold" style="color: {{ $partConfig['text'] }};">
                                                    {{ $part->price ? '€' . number_format($part->price, 2, ',', '.') : '—' }}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($repair->parts->count() > 3)
                                            <div class="flex items-center justify-center p-3 border rounded-lg" 
                                                 style="background-color: var(--third-color); border-color: var(--secondary-color);">
                                                <span class="text-sm" style="color: var(--secondary-color);">+{{ $repair->parts->count() - 3 }} meer...</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: var(--third-color);">
                            <i class="fa-solid fa-wrench text-3xl" style="color: var(--secondary-color);"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2" style="color: var(--text-kleur-black);">Nog geen reparaties</h3>
                        <p class="mb-6" style="color: var(--secondary-color);">Begin met het toevoegen van je eerste reparatie.</p>
                        <a href="{{ route('repairs.create') }}" 
                           class="inline-flex items-center px-6 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                           style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Eerste Reparatie Toevoegen
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
/* Modern animations and effects with CSS variables */
@keyframes slideInUp {
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes colorPulse {
    0%, 100% { 
        box-shadow: 0 0 0 0 var(--primary-color); 
    }
    50% { 
        box-shadow: 0 0 0 8px transparent; 
    }
}

/* Card animations */
.repair-item {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
    border-bottom: 1px solid var(--third-color);
}

.repair-item:nth-child(1) { animation-delay: 0.1s; }
.repair-item:nth-child(2) { animation-delay: 0.2s; }
.repair-item:nth-child(3) { animation-delay: 0.3s; }
.repair-item:nth-child(4) { animation-delay: 0.4s; }
.repair-item:nth-child(5) { animation-delay: 0.5s; }

/* Hover effects */
.repair-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button hover effects */
.repair-item a:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    animation: colorPulse 1.5s infinite;
}

/* Stats cards animation */
.stats-card {
    animation: fadeIn 0.8s ease-out;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
}

/* Smooth transitions for all interactive elements */
* {
    transition: all 0.2s ease;
}

/* Custom scrollbar with CSS variables */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--third-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-color);
}

/* Filter and search animations */
#statusFilter, #searchInput {
    transition: all 0.3s ease;
    color: var(--text-kleur-black) !important;
}

#statusFilter:focus, #searchInput:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px var(--primary-color)20 !important;
    border-color: var(--primary-color) !important;
}

/* Gradient text effects using CSS variables */
.gradient-text {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Parts preview hover effect */
.parts-preview:hover {
    background: linear-gradient(135deg, var(--third-color) 0%, var(--secondary-color)20 100%);
    border-color: var(--secondary-color);
}

/* Loading animation for buttons */
.loading {
    animation: pulse 2s infinite;
    background: var(--secondary-color) !important;
}

/* Enhanced animations for interactive elements */
button:hover, a:hover {
    animation: colorPulse 1.5s infinite;
}

/* Form inputs styling */
select, input[type="text"] {
    background-color: var(--text-kleur-white) !important;
    border-color: var(--third-color) !important;
    color: var(--text-kleur-black) !important;
}

select:focus, input[type="text"]:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 2px var(--primary-color)30 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const repairItems = document.querySelectorAll('.repair-item');
    
    function filterRepairs() {
        const statusValue = statusFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();
        
        repairItems.forEach(item => {
            const status = item.dataset.status.toLowerCase();
            const text = item.textContent.toLowerCase();
            
            const statusMatch = !statusValue || status === statusValue;
            const searchMatch = !searchValue || text.includes(searchValue);
            
            if (statusMatch && searchMatch) {
                item.style.display = 'block';
                item.style.animation = 'slideInUp 0.3s ease-out';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        const visibleItems = Array.from(repairItems).filter(item => item.style.display !== 'none');
        // Add no results handling here if needed
    }
    
    statusFilter.addEventListener('change', filterRepairs);
    searchInput.addEventListener('input', filterRepairs);
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading state to buttons
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
            this.disabled = true;
            
            // Re-enable after 3 seconds as fallback
            setTimeout(() => {
                this.classList.remove('loading');
                this.disabled = false;
            }, 3000);
        });
    });
});
</script>
@endsection