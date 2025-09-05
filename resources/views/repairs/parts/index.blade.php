@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-violet-50 via-purple-50 to-fuchsia-50 min-h-full">
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
                    <a href="{{ route('repairs.show', $repair->id) }}" class="hover:text-purple-600 transition-colors">
                        Reparatie #{{ $repair->id }}
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-900 font-medium">Onderdelen</span>
                </nav>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-violet-600 to-purple-600 bg-clip-text text-transparent">
                    🧩 Onderdelen Beheer
                </h1>
                <p class="text-gray-600 mt-2">Beheer onderdelen voor: <span class="font-semibold">{{ $repair->description }}</span></p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('repairs.show', $repair->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-eye mr-2"></i>
                    Reparatie
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

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @php
                $totalParts = $repair->parts->count();
                $totalValue = $repair->parts->sum('price');
                $completedParts = $repair->parts->where('status', 'gemonteerd')->count();
            @endphp
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totaal Onderdelen</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalParts }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-puzzle-piece text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Totale Waarde</p>
                        <p class="text-3xl font-bold text-emerald-600">€{{ number_format($totalValue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-euro-sign text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Gemonteerd</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $completedParts }}/{{ $totalParts }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Part Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-8 py-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-plus-circle mr-3"></i>
                    Nieuw Onderdeel Toevoegen
                </h2>
                <p class="text-violet-100 mt-1">Voeg een nieuw onderdeel toe aan deze reparatie</p>
            </div>

            <form method="POST" action="{{ route('repairs.parts.store', $repair) }}" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-tag text-purple-600 mr-2"></i>
                            Onderdeelnaam
                        </label>
                        <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white" placeholder="Bijvoorbeeld: Remblok set vooras">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-tasks text-blue-600 mr-2"></i>
                            Status
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="besteld" {{ old('status') == 'besteld' ? 'selected' : '' }}>📦 Besteld</option>
                            <option value="geleverd" {{ old('status') == 'geleverd' ? 'selected' : '' }}>🚚 Geleverd</option>
                            <option value="gemonteerd" {{ old('status') == 'gemonteerd' ? 'selected' : '' }}>✅ Gemonteerd</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-euro-sign text-emerald-600 mr-2"></i>
                            Prijs (€)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">€</span>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white" placeholder="49.95">
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
                        <i class="fa-solid fa-plus mr-3"></i>
                        Onderdeel Toevoegen
                    </button>
                </div>
            </form>
        </div>

        <!-- Parts List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fa-solid fa-list mr-3"></i>
                        Onderdelenlijst ({{ $repair->parts->count() }})
                    </h2>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <i class="fa-solid fa-filter text-white mr-2"></i>
                            <select class="border-none focus:ring-0 text-sm bg-transparent text-white" id="statusFilter">
                                <option value="">Alle statussen</option>
                                <option value="besteld">Besteld</option>
                                <option value="geleverd">Geleverd</option>
                                <option value="gemonteerd">Gemonteerd</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($repair->parts as $part)
                    @php
                        $partStatusConfig = [
                            'besteld' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-shopping-cart', 'label' => 'Besteld'],
                            'geleverd' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'fa-truck', 'label' => 'Geleverd'],
                            'gemonteerd' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => 'fa-check-circle', 'label' => 'Gemonteerd']
                        ];
                        $partConfig = $partStatusConfig[$part->status] ?? $partStatusConfig['besteld'];
                    @endphp
                    
                    <div class="part-item p-8 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-300" data-status="{{ $part->status }}">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 {{ $partConfig['bg'] }} {{ $partConfig['border'] }} border rounded-xl flex items-center justify-center">
                                    <i class="fa-solid {{ $partConfig['icon'] }} {{ $partConfig['text'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $part->name }}</h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $partConfig['bg'] }} {{ $partConfig['text'] }} {{ $partConfig['border'] }} border">
                                        <i class="fa-solid {{ $partConfig['icon'] }} mr-2"></i>
                                        {{ $partConfig['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $part->price ? '€' . number_format($part->price, 2, ',', '.') : '—' }}
                                </div>
                                <div class="text-sm text-gray-500">Toegevoegd {{ $part->created_at->format('d-m-Y') }}</div>
                            </div>
                        </div>

                        <!-- Edit Form (Initially Hidden) -->
                        <div id="edit-form-{{ $part->id }}" class="bg-gray-50 rounded-xl p-6 border border-gray-200" style="display: none;">
                            <form method="POST" action="{{ route('parts.update', $part) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Naam</label>
                                        <input type="text" name="name" value="{{ $part->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                            <option value="besteld" {{ $part->status == 'besteld' ? 'selected' : '' }}>📦 Besteld</option>
                                            <option value="geleverd" {{ $part->status == 'geleverd' ? 'selected' : '' }}>🚚 Geleverd</option>
                                            <option value="gemonteerd" {{ $part->status == 'gemonteerd' ? 'selected' : '' }}>✅ Gemonteerd</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prijs (€)</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                            <input type="number" step="0.01" min="0" name="price" value="{{ $part->price }}" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-3">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center">
                                        <i class="fa-solid fa-save mr-2"></i>
                                        Opslaan
                                    </button>
                                    <button type="button" onclick="hideEditForm({{ $part->id }})" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center">
                                        <i class="fa-solid fa-times mr-2"></i>
                                        Annuleren
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Action Buttons -->
                        <div id="action-buttons-{{ $part->id }}" class="flex space-x-3">
                            <button onclick="showEditForm({{ $part->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center">
                                <i class="fa-solid fa-edit mr-2"></i>
                                Bewerken
                            </button>
                            <button onclick="showDeleteModal({{ $part->id }})" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center">
                                <i class="fa-solid fa-trash mr-2"></i>
                                Verwijderen
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-puzzle-piece text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Geen onderdelen</h3>
                        <p class="text-gray-600 mb-6">Er zijn nog geen onderdelen toegevoegd aan deze reparatie.</p>
                        <p class="text-gray-500">Gebruik het formulier hierboven om je eerste onderdeel toe te voegen.</p>
                    </div>
                @endforelse
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
                <h3 class="text-xl font-bold text-gray-900 mb-2">Onderdeel Verwijderen</h3>
                <p class="text-gray-600 mb-6">Weet je zeker dat je dit onderdeel permanent wilt verwijderen?</p>
                <div class="flex space-x-4">
                    <button onclick="hideDeleteModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                        Annuleren
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
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

.part-item {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.part-item:nth-child(1) { animation-delay: 0.1s; }
.part-item:nth-child(2) { animation-delay: 0.2s; }
.part-item:nth-child(3) { animation-delay: 0.3s; }
.part-item:nth-child(4) { animation-delay: 0.4s; }

.part-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

input:focus, select:focus {
    transform: scale(1.01);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}
</style>

<script>
function showEditForm(partId) {
    document.getElementById('edit-form-' + partId).style.display = 'block';
    document.getElementById('action-buttons-' + partId).style.display = 'none';
}

function hideEditForm(partId) {
    document.getElementById('edit-form-' + partId).style.display = 'none';
    document.getElementById('action-buttons-' + partId).style.display = 'flex';
}

function showDeleteModal(partId) {
    document.getElementById('deleteForm').action = '{{ url("parts") }}/' + partId;
    document.getElementById('deleteModal').style.display = 'block';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const filterValue = this.value.toLowerCase();
    const partItems = document.querySelectorAll('.part-item');
    
    partItems.forEach(item => {
        const status = item.dataset.status.toLowerCase();
        
        if (!filterValue || status === filterValue) {
            item.style.display = 'block';
            item.style.animation = 'slideInUp 0.3s ease-out';
        } else {
            item.style.display = 'none';
        }
    });
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
@endsection
