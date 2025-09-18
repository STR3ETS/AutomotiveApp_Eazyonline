@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Klantenbeheer</h1>
        <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
            <i class="fas fa-plus mr-2"></i>Nieuwe Klant
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Klanten Tabel -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefoon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $customer->email ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $customer->phone ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ $customer->address ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editCustomer({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->email }}', '{{ $customer->phone }}', '{{ $customer->address }}')" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Bewerken
                                </button>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Weet je zeker dat je deze klant wilt verwijderen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Verwijderen
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p>Nog geen klanten toegevoegd.</p>
                                <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:text-blue-800">
                                    Voeg je eerste klant toe
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
            <div class="px-6 py-3 bg-gray-50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="customerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Nieuwe Klant Toevoegen</h3>
            
            <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Naam *</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefoon</label>
                    <input type="text" id="phone" name="phone" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
                    <textarea id="address" name="address" rows="3"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annuleren
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Opslaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Nieuwe Klant Toevoegen';
    document.getElementById('customerForm').action = '{{ route("customers.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('customerForm').reset();
    document.getElementById('customerModal').classList.remove('hidden');
}

function editCustomer(id, name, email, phone, address) {
    document.getElementById('modalTitle').textContent = 'Klant Bewerken';
    document.getElementById('customerForm').action = '/customers/' + id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = name;
    document.getElementById('email').value = email || '';
    document.getElementById('phone').value = phone || '';
    document.getElementById('address').value = address || '';
    
    document.getElementById('customerModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('customerModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('customerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
