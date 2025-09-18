@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Modern Header with Actions -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-3">
                    🔧 Werkplaats Dashboard
                </h1>
                <p class="text-gray-600 text-lg">Moderne reparatie- en onderdeelbeheer</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('repairs.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Nieuwe Reparatie
                </a>
                <a href="{{ route('repairs.analytics') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fa-solid fa-chart-line mr-2"></i>
                    Analytics
                </a>
            </div>
        </div>

        {{-- Flash messages with improved styling --}}
        @if(session('success'))
            <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 rounded-2xl flex items-center shadow-sm">
                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fa-solid fa-check text-white text-sm"></i>
                </div>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-8 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 rounded-2xl shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fa-solid fa-exclamation text-white text-sm"></i>
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
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totaal Reparaties</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-wrench text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Actief</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $activeRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-cog text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Afgerond</p>
                        <p class="text-3xl font-bold text-green-600">{{ $completedRepairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totale Waarde</p>
                        <p class="text-2xl font-bold text-indigo-600">€{{ number_format($totalValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-euro-sign text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Repairs List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 sm:mb-0">📋 Alle Reparaties</h2>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200">
                            <i class="fa-solid fa-filter text-gray-400 mr-2"></i>
                            <select class="border-none focus:ring-0 text-sm bg-transparent" id="statusFilter">
                                <option value="">Alle statussen</option>
                                <option value="gepland">Gepland</option>
                                <option value="bezig">Bezig</option>
                                <option value="wachten_op_onderdeel">Wachten op onderdeel</option>
                                <option value="gereed">Gereed</option>
                            </select>
                        </div>
                        <div class="flex items-center bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200">
                            <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                            <input type="text" placeholder="Zoek reparatie..." class="border-none focus:ring-0 text-sm bg-transparent w-32" id="searchInput">
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($repairs as $repair)
                    @php
                        $statusConfig = [
                            'gepland' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-calendar', 'label' => 'Gepland'],
                            'bezig' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-cog', 'label' => 'Bezig'],
                            'wachten_op_onderdeel' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-clock', 'label' => 'Wachten op onderdeel'],
                            'gereed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Gereed']
                        ];
                        $config = $statusConfig[$repair->status] ?? $statusConfig['gepland'];
                    @endphp
                    
                    <div class="repair-item hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300" data-status="{{ $repair->status }}">
                        <div class="p-8">
                            <!-- Header Row -->
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-6">
                                <div class="flex-1 mb-4 lg:mb-0">
                                    <div class="flex items-center space-x-4 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                            <i class="fa-solid {{ $config['icon'] }} mr-2"></i>
                                            {{ $config['label'] }}
                                        </span>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                                            #{{ $repair->id }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $repair->created_at->format('d-m-Y H:i') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $repair->description }}</h3>
                                    
                                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-car text-gray-400 mr-2"></i>
                                            <span class="font-medium">{{ $repair->car->license_plate ?? '—' }}</span>
                                            <span class="ml-2">{{ ($repair->car->brand ?? '') . ' ' . ($repair->car->model ?? '') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-euro-sign text-gray-400 mr-2"></i>
                                            <span class="font-bold text-lg text-gray-900">
                                                {{ $repair->cost_estimate ? '€' . number_format($repair->cost_estimate, 2, ',', '.') : '—' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-puzzle-piece text-gray-400 mr-2"></i>
                                            <span>{{ $repair->parts->count() }} onderdelen</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                    <a href="{{ route('repairs.show', $repair->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        Bekijken
                                    </a>
                                    <a href="{{ route('repairs.edit', $repair->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fa-solid fa-edit mr-2"></i>
                                        Bewerken
                                    </a>
                                    <a href="{{ route('repairs.parts.index', $repair->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fa-solid fa-puzzle-piece mr-2"></i>
                                        Onderdelen
                                    </a>
                                </div>
                            </div>

                            <!-- Parts Preview (if any) -->
                            @if($repair->parts->count() > 0)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fa-solid fa-list mr-2"></i>
                                        Onderdelen ({{ $repair->parts->count() }})
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($repair->parts->take(3) as $part)
                                            @php
                                                $partStatusConfig = [
                                                    'besteld' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                                                    'geleverd' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                                                    'gemonteerd' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200']
                                                ];
                                                $partConfig = $partStatusConfig[$part->status] ?? $partStatusConfig['besteld'];
                                            @endphp
                                            <div class="flex items-center justify-between p-3 {{ $partConfig['bg'] }} {{ $partConfig['border'] }} border rounded-lg">
                                                <div class="flex-1">
                                                    <div class="font-medium {{ $partConfig['text'] }} text-sm">{{ $part->name }}</div>
                                                    <div class="text-xs text-gray-500 capitalize">{{ $part->status }}</div>
                                                </div>
                                                <div class="text-sm font-semibold {{ $partConfig['text'] }}">
                                                    {{ $part->price ? '€' . number_format($part->price, 2, ',', '.') : '—' }}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($repair->parts->count() > 3)
                                            <div class="flex items-center justify-center p-3 bg-gray-100 border border-gray-200 rounded-lg">
                                                <span class="text-sm text-gray-600">+{{ $repair->parts->count() - 3 }} meer...</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-wrench text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Nog geen reparaties</h3>
                        <p class="text-gray-600 mb-6">Begin met het toevoegen van je eerste reparatie.</p>
                        <a href="{{ route('repairs.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
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
/* Modern animations and effects */
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

/* Card animations */
.repair-item {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
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

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Filter and search animations */
#statusFilter, #searchInput {
    transition: all 0.3s ease;
}

#statusFilter:focus, #searchInput:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Gradient text effects */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Parts preview hover effect */
.repair-item .bg-gray-50:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-color: #cbd5e1;
}

/* Loading animation for buttons */
.loading {
    animation: pulse 2s infinite;
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