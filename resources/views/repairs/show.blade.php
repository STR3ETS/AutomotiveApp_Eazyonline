@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header with Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                    <a href="{{ route('repairs.index') }}" class="hover:text-purple-600 transition-colors">
                        <i class="fa-solid fa-wrench mr-1"></i>
                        Reparaties
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-900 font-medium">Reparatie #{{ $repair->id }}</span>
                </nav>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                    🔍 Reparatie Details
                </h1>
                <p class="text-gray-600 mt-2">Volledig overzicht van reparatie #{{ $repair->id }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('repairs.edit', $repair->id) }}" class="inline-flex items-center px-4 py-2 bg-[var(--button-edit)] hover:bg-[var(--button-edit)]/50 text-white font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-edit mr-2"></i>
                    Bewerken
                </a>
                <a href="{{ route('repairs.parts.index', $repair->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-puzzle-piece mr-2"></i>
                    Onderdelen
                </a>
                <a href="{{ route('repairs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Terug
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 rounded-2xl flex items-center shadow-sm">
                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fa-solid fa-check text-white text-sm"></i>
                </div>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Repair Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fa-solid fa-info-circle mr-3"></i>
                            Reparatie Informatie
                        </h2>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Omschrijving</label>
                                <p class="text-xl font-semibold text-gray-900 mt-2">{{ $repair->description }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Status</label>
                                @php
                                    $statusConfig = [
                                        'gepland' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-calendar', 'label' => 'Gepland'],
                                        'bezig' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-cog', 'label' => 'Bezig'],
                                        'wachten_op_onderdeel' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-clock', 'label' => 'Wachten op onderdeel'],
                                        'gereed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Gereed']
                                    ];
                                    $config = $statusConfig[$repair->status] ?? $statusConfig['gepland'];
                                @endphp
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                        <i class="fa-solid {{ $config['icon'] }} mr-2"></i>
                                        {{ $config['label'] }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Kostenraming</label>
                                <p class="text-2xl font-bold text-purple-600 mt-2">
                                    {{ $repair->cost_estimate ? '€' . number_format($repair->cost_estimate, 2, ',', '.') : 'Niet ingesteld' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Aangemaakt</label>
                                <p class="text-lg font-medium text-gray-900 mt-2">{{ $repair->created_at->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($repair->planned_at)
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Geplande Datum</label>
                                <p class="text-lg font-medium text-gray-900 mt-2">{{ $repair->planned_at->format('d-m-Y') }}</p>
                            </div>
                        @endif
                        
                        @if($repair->notes)
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Notities</label>
                                <p class="text-gray-700 mt-2 leading-relaxed">{{ $repair->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Car Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-6">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fa-solid fa-car mr-3"></i>
                            Auto Informatie
                        </h2>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Kenteken</label>
                                <p class="text-xl font-bold text-gray-900 mt-2">{{ $repair->car->license_plate ?? '—' }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Merk & Model</label>
                                <p class="text-lg font-semibold text-gray-900 mt-2">
                                    {{ ($repair->car->brand ?? '') . ' ' . ($repair->car->model ?? '') }}
                                </p>
                            </div>
                            
                            @if($repair->car->year)
                                <div>
                                    <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Bouwjaar</label>
                                    <p class="text-lg font-medium text-gray-900 mt-2">{{ $repair->car->year }}</p>
                                </div>
                            @endif
                            
                            @if($repair->car->status)
                                <div>
                                    <label class="text-sm font-bold text-gray-500 uppercase tracking-wider">Status</label>
                                    <p class="text-lg font-medium text-gray-900 mt-2">{{ ucfirst($repair->car->status) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Parts List Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fa-solid fa-puzzle-piece mr-3"></i>
                                Onderdelen ({{ $repair->parts->count() }})
                            </h2>
                            <a href="{{ route('repairs.parts.index', $repair->id) }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-black px-4 py-2 rounded-lg font-semibold transition-all duration-200">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Beheren
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        @forelse($repair->parts as $part)
                            @php
                                $partStatusConfig = [
                                    'besteld' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-shopping-cart'],
                                    'geleverd' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'fa-truck'],
                                    'gemonteerd' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => 'fa-check-circle']
                                ];
                                $partConfig = $partStatusConfig[$part->status] ?? $partStatusConfig['besteld'];
                            @endphp
                            <div class="flex items-center justify-between p-4 {{ $partConfig['bg'] }} {{ $partConfig['border'] }} border rounded-xl mb-4 last:mb-0 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center flex-1">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                        <i class="fa-solid {{ $partConfig['icon'] }} {{ $partConfig['text'] }}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold {{ $partConfig['text'] }} text-lg">{{ $part->name }}</div>
                                        <div class="text-sm {{ $partConfig['text'] }} opacity-75 capitalize">{{ $part->status }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold {{ $partConfig['text'] }}">
                                        {{ $part->price ? '€' . number_format($part->price, 2, ',', '.') : '—' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-puzzle-piece text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Geen onderdelen</h3>
                                <p class="text-gray-600 mb-4">Er zijn nog geen onderdelen toegevoegd aan deze reparatie.</p>
                                <a href="{{ route('repairs.parts.index', $repair->id) }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200">
                                    <i class="fa-solid fa-plus mr-2"></i>
                                    Eerste Onderdeel Toevoegen
                                </a>
                            </div>
                        @endforelse
                        
                        @if($repair->parts->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-700">Totaal Onderdelen:</span>
                                    <span class="text-2xl font-bold text-emerald-600">
                                        €{{ number_format($repair->parts->sum('price'), 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fa-solid fa-chart-bar text-blue-600 mr-2"></i>
                        Samenvatting
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Totaal Raming</span>
                            <span class="text-lg font-bold text-blue-600">
                                €{{ number_format(($repair->cost_estimate ?? 0) + $repair->parts->sum('price'), 2, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Onderdelen</span>
                            <span class="text-lg font-bold text-purple-600">{{ $repair->parts->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Onderdelen Waarde</span>
                            <span class="text-lg font-bold text-emerald-600">
                                €{{ number_format($repair->parts->sum('price'), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fa-solid fa-bolt text-yellow-500 mr-2"></i>
                        Acties
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('repairs.edit', $repair->id) }}" class="block w-full bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-center">
                            <i class="fa-solid fa-edit mr-2"></i>
                            Bewerken
                        </a>
                        
                        <a href="{{ route('repairs.parts.index', $repair->id) }}" class="block w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-center">
                            <i class="fa-solid fa-puzzle-piece mr-2"></i>
                            Onderdelen Beheren
                        </a>
                        
                        <button onclick="window.print()" class="block w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-center">
                            <i class="fa-solid fa-print mr-2"></i>
                            Printen
                        </button>
                        
                        <button onclick="showDeleteModal()" class="block w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-center">
                            <i class="fa-solid fa-trash mr-2"></i>
                            Verwijderen
                        </button>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fa-solid fa-clock text-indigo-600 mr-2"></i>
                        Tijdlijn
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <div class="font-medium text-gray-900">Reparatie aangemaakt</div>
                                <div class="text-sm text-gray-500">{{ $repair->created_at->format('d-m-Y H:i') }}</div>
                            </div>
                        </div>
                        
                        @if($repair->updated_at->ne($repair->created_at))
                            <div class="flex items-start">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3"></div>
                                <div>
                                    <div class="font-medium text-gray-900">Laatst bijgewerkt</div>
                                    <div class="text-sm text-gray-500">{{ $repair->updated_at->format('d-m-Y H:i') }}</div>
                                </div>
                            </div>
                        @endif
                        
                        @if($repair->status === 'gereed')
                            <div class="flex items-start">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full mt-2 mr-3"></div>
                                <div>
                                    <div class="font-medium text-gray-900">Reparatie voltooid</div>
                                    <div class="text-sm text-gray-500">Status: Gereed</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Reparatie Verwijderen</h3>
                <p class="text-gray-600 mb-6">Weet je zeker dat je deze reparatie permanent wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.</p>
                <div class="flex space-x-4">
                    <button onclick="hideDeleteModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                        Annuleren
                    </button>
                    <form method="POST" action="{{ route('repairs.destroy', $repair) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                            Verwijderen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.bg-white {
    animation: slideInUp 0.6s ease-out;
}

.bg-white:nth-child(1) { animation-delay: 0.1s; }
.bg-white:nth-child(2) { animation-delay: 0.2s; }
.bg-white:nth-child(3) { animation-delay: 0.3s; }

@media print {
    .print:hidden {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-gradient-to-br {
        background: white !important;
    }
}
</style>

<script>
function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'block';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
@endsection
