@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('employees.index') }}" 
                   class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-[var(--text-primary)]">👷 {{ $employee->name }}</h1>
                    <p class="text-[var(--text-secondary)]">{{ $employee->position }}</p>
                </div>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('employees.edit', $employee) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-edit"></i>
                    Bewerken
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-[var(--status-success-bg)] border border-[var(--status-success-border)] text-[var(--status-success-text)] px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[var(--status-danger-bg)] border border-[var(--status-danger-light)] text-[var(--status-danger-text)] px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Employee Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Employee Details -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4">📋 Medewerker Gegevens</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Naam</label>
                            <p class="text-[var(--text-primary)] font-medium">{{ $employee->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Functie</label>
                            <p class="text-[var(--text-primary)]">{{ $employee->position }}</p>
                        </div>
                        
                        @if($employee->email)
                            <div>
                                <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Email</label>
                                <p class="text-[var(--text-primary)]">
                                    <a href="mailto:{{ $employee->email }}" class="text-blue-600 hover:underline">
                                        {{ $employee->email }}
                                    </a>
                                </p>
                            </div>
                        @endif
                        
                        @if($employee->phone)
                            <div>
                                <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Telefoon</label>
                                <p class="text-[var(--text-primary)]">
                                    <a href="tel:{{ $employee->phone }}" class="text-blue-600 hover:underline">
                                        {{ $employee->phone }}
                                    </a>
                                </p>
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Status</label>
                            @if($employee->active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fa-solid fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                                    Actief
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="fa-solid fa-circle text-gray-500 mr-1" style="font-size: 6px;"></i>
                                    Inactief
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Specializations -->
                    @if($employee->specializations && count($employee->specializations) > 0)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-[var(--text-secondary)] mb-2">Specialisaties</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($employee->specializations as $spec)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $spec }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Current Assignments -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)]">🚗 Huidige Werkzaamheden</h2>
                        <span class="text-sm text-[var(--text-secondary)]">
                            {{ $employee->currentAssignments->count() }}/3 actief
                        </span>
                    </div>

                    @if($employee->currentAssignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($employee->currentAssignments as $assignment)
                                <div class="border border-[var(--border-light)] rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-car text-blue-600"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-[var(--text-primary)]">
                                                    {{ $assignment->car->license_plate }}
                                                </h3>
                                                <p class="text-sm text-[var(--text-secondary)]">
                                                    {{ $assignment->car->brand }} {{ $assignment->car->model }}
                                                    @if($assignment->car->stage)
                                                        · {{ $assignment->car->stage->name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        @if($assignment->isOverdue())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                                Te laat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                Lopend
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-[var(--text-secondary)]">Gestart:</span>
                                            <span class="text-[var(--text-primary)]">{{ $assignment->assigned_at->format('d-m-Y H:i') }}</span>
                                        </div>
                                        @if($assignment->estimated_completion)
                                            <div>
                                                <span class="text-[var(--text-secondary)]">Verwacht klaar:</span>
                                                <span class="text-[var(--text-primary)]">{{ $assignment->estimated_completion->format('d-m-Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($assignment->notes)
                                        <div class="mt-3 p-3 bg-[var(--background-main)] rounded-lg">
                                            <p class="text-sm text-[var(--text-primary)]">{{ $assignment->notes }}</p>
                                        </div>
                                    @endif

                                    <div class="flex gap-2 mt-4">
                                        <form method="POST" action="{{ route('employees.complete-assignment', [$employee, $assignment]) }}" class="inline-flex">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                                    onclick="return confirm('Werk aan deze auto afronden?')">
                                                <i class="fa-solid fa-check mr-1"></i>
                                                Afronden
                                            </button>
                                        </form>
                                        
                                        <button type="button" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                                onclick="cancelAssignment({{ $assignment->id }})">
                                            <i class="fa-solid fa-times mr-1"></i>
                                            Annuleren
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-[var(--text-secondary)]">
                            <i class="fa-solid fa-coffee text-4xl mb-4 text-gray-300"></i>
                            <p>Geen actieve werkzaamheden</p>
                        </div>
                    @endif
                </div>

                <!-- Assignment History -->
                @if($employee->carAssignments->where('status', '!=', 'active')->count() > 0)
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4">📚 Geschiedenis</h2>
                        
                        <div class="space-y-3">
                            @foreach($employee->carAssignments->where('status', '!=', 'active')->sortByDesc('completed_at')->take(10) as $assignment)
                                <div class="flex items-center justify-between p-3 bg-[var(--background-main)] rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 {{ $assignment->status === 'completed' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                            <i class="fa-solid {{ $assignment->status === 'completed' ? 'fa-check text-green-600' : 'fa-times text-red-600' }} text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-[var(--text-primary)]">{{ $assignment->car->license_plate }}</span>
                                            <span class="text-sm text-[var(--text-secondary)]"> · {{ $assignment->car->brand }} {{ $assignment->car->model }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right text-sm">
                                        <div class="text-[var(--text-primary)]">
                                            {{ $assignment->status === 'completed' ? 'Afgerond' : 'Geannuleerd' }}
                                        </div>
                                        <div class="text-[var(--text-secondary)]">
                                            {{ $assignment->completed_at->format('d-m-Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Quick Actions -->
            <div class="space-y-6">
                <!-- Assign Car -->
                @if($employee->canTakeNewAssignment() && $availableCars->count() > 0)
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                        <h2 class="text-lg font-semibold text-[var(--text-primary)] mb-4">🔧 Auto Toewijzen</h2>
                        
                        <form method="POST" action="{{ route('employees.assign-car', $employee) }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="car_id" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                    Selecteer Auto
                                </label>
                                <select name="car_id" id="car_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Kies een auto --</option>
                                    @foreach($availableCars as $car)
                                        <option value="{{ $car->id }}">
                                            {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                                            @if($car->stage)
                                                ({{ $car->stage->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="estimated_completion" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                    Verwachte Klaar Datum
                                </label>
                                <input type="date" 
                                       name="estimated_completion" 
                                       id="estimated_completion"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                    Notities
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          rows="3"
                                          placeholder="Bijzondere instructies of opmerkingen..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Auto Toewijzen
                            </button>
                        </form>
                    </div>
                @elseif(!$employee->canTakeNewAssignment())
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                        <h2 class="text-lg font-semibold text-[var(--text-primary)] mb-4">🔧 Auto Toewijzen</h2>
                        <div class="text-center py-4">
                            <i class="fa-solid fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                            <p class="text-sm text-[var(--text-secondary)]">
                                Deze medewerker heeft al het maximum aantal auto's (3) toegewezen.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                        <h2 class="text-lg font-semibold text-[var(--text-primary)] mb-4">🔧 Auto Toewijzen</h2>
                        <div class="text-center py-4">
                            <i class="fa-solid fa-info-circle text-blue-500 text-2xl mb-2"></i>
                            <p class="text-sm text-[var(--text-secondary)]">
                                Geen beschikbare auto's om toe te wijzen.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Stats -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-lg font-semibold text-[var(--text-primary)] mb-4">📊 Statistieken</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Totaal afgerond:</span>
                            <span class="font-medium text-[var(--text-primary)]">
                                {{ $employee->carAssignments->where('status', 'completed')->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Actieve opdrachten:</span>
                            <span class="font-medium text-[var(--text-primary)]">
                                {{ $employee->currentAssignments->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Capaciteit:</span>
                            <span class="font-medium text-[var(--text-primary)]">
                                {{ $employee->currentAssignments->count() }}/3
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Assignment Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Toewijzing Annuleren</h3>
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reden voor annulering <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" 
                          id="reason" 
                          rows="3"
                          required
                          placeholder="Waarom wordt deze toewijzing geannuleerd?"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" 
                        onclick="closeCancelModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                    Terug
                </button>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Annuleren
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cancelAssignment(assignmentId) {
    const form = document.getElementById('cancelForm');
    form.action = `{{ route('employees.cancel-assignment', [$employee, ':id']) }}`.replace(':id', assignmentId);
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
    document.getElementById('reason').value = '';
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endsection
