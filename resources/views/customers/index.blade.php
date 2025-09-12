@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold" style="color: var(--text-kleur-black);">Klantenbeheer</h1>
        <button onclick="openAddModal()" 
                style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); border: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer; transition: all 0.3s ease;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'">
            <i class="fas fa-plus mr-2"></i>Nieuwe Klant
        </button>
    </div>

    @if(session('success'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: rgba(22, 163, 74, 1); padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Klanten Tabel -->
    <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); overflow: hidden;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                    <tr>
                        <th style="padding: 16px 24px; text-align: left; color: var(--text-kleur-white); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Naam</th>
                        <th style="padding: 16px 24px; text-align: left; color: var(--text-kleur-white); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Email</th>
                        <th style="padding: 16px 24px; text-align: left; color: var(--text-kleur-white); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Telefoon</th>
                        <th style="padding: 16px 24px; text-align: left; color: var(--text-kleur-white); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Adres</th>
                        <th style="padding: 16px 24px; text-align: left; color: var(--text-kleur-white); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Acties</th>
                    </tr>
                </thead>
                <tbody style="background-color: var(--text-kleur-white);">
                    @forelse($customers as $customer)
                        <tr style="border-bottom: 1px solid var(--third-color);">
                            <td style="padding: 16px 24px; white-space: nowrap;">
                                <div style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">{{ $customer->name }}</div>
                            </td>
                            <td style="padding: 16px 24px; white-space: nowrap;">
                                <div style="font-size: 14px; color: var(--secondary-color);">{{ $customer->email ?: '-' }}</div>
                            </td>
                            <td style="padding: 16px 24px; white-space: nowrap;">
                                <div style="font-size: 14px; color: var(--secondary-color);">{{ $customer->phone ?: '-' }}</div>
                            </td>
                            <td style="padding: 16px 24px;">
                                <div style="font-size: 14px; color: var(--secondary-color); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $customer->address ?: '-' }}</div>
                            </td>
                            <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; font-weight: 500;">
                                <button onclick="editCustomer({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->email }}', '{{ $customer->phone }}', '{{ $customer->address }}')" 
                                        style="color: var(--primary-color); background: none; border: none; margin-right: 12px; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: all 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='var(--third-color)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                    <i class="fas fa-edit"></i> Bewerken
                                </button>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Weet je zeker dat je deze klant wilt verwijderen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            style="color: #dc2626; background: none; border: none; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: all 0.3s ease;"
                                            onmouseover="this.style.backgroundColor='rgba(220, 38, 38, 0.1)'"
                                            onmouseout="this.style.backgroundColor='transparent'">
                                        <i class="fas fa-trash"></i> Verwijderen
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 48px 24px; text-align: center; color: var(--secondary-color);">
                                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; color: var(--third-color);"></i>
                                <p>Nog geen klanten toegevoegd.</p>
                                <button onclick="openAddModal()" 
                                        style="margin-top: 8px; color: var(--primary-color); background: none; border: none; cursor: pointer; text-decoration: underline; transition: all 0.3s ease;"
                                        onmouseover="this.style.color='var(--secondary-color)'"
                                        onmouseout="this.style.color='var(--primary-color)'">
                                    Voeg je eerste klant toe
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
            <div style="padding: 12px 24px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white));">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="customerModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); overflow-y: auto; height: 100%; width: 100%; z-index: 50;" class="hidden">
    <div style="position: relative; top: 80px; margin: 0 auto; padding: 20px; border: 1px solid var(--third-color); width: 400px; box-shadow: 0 12px 36px rgba(0,0,0,0.2); border-radius: 12px; background-color: var(--text-kleur-white);">
        <div style="margin-top: 12px;">
            <h3 style="font-size: 18px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 16px;" id="modalTitle">Nieuwe Klant Toevoegen</h3>
            
            <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div style="margin-bottom: 16px;">
                    <label for="name" style="display: block; font-size: 14px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Naam *</label>
                    <input type="text" id="name" name="name" required 
                           style="width: 100%; border: 1px solid var(--third-color); border-radius: 8px; padding: 12px; outline: none; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0,0,0,0.1)'"
                           onblur="this.style.borderColor='var(--third-color)'; this.style.boxShadow='none'">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Email</label>
                    <input type="email" id="email" name="email" 
                           style="width: 100%; border: 1px solid var(--third-color); border-radius: 8px; padding: 12px; outline: none; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0,0,0,0.1)'"
                           onblur="this.style.borderColor='var(--third-color)'; this.style.boxShadow='none'">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label for="phone" style="display: block; font-size: 14px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Telefoon</label>
                    <input type="text" id="phone" name="phone" 
                           style="width: 100%; border: 1px solid var(--third-color); border-radius: 8px; padding: 12px; outline: none; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0,0,0,0.1)'"
                           onblur="this.style.borderColor='var(--third-color)'; this.style.boxShadow='none'">
                </div>
                
                <div style="margin-bottom: 24px;">
                    <label for="address" style="display: block; font-size: 14px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Adres</label>
                    <textarea id="address" name="address" rows="3"
                              style="width: 100%; border: 1px solid var(--third-color); border-radius: 8px; padding: 12px; outline: none; transition: all 0.3s ease; resize: vertical;"
                              onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0,0,0,0.1)'"
                              onblur="this.style.borderColor='var(--third-color)'; this.style.boxShadow='none'"></textarea>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeModal()" 
                            style="padding: 10px 16px; background-color: var(--third-color); color: var(--text-kleur-black); border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;"
                            onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                            onmouseout="this.style.backgroundColor='var(--third-color)'">
                        Annuleren
                    </button>
                    <button type="submit" 
                            style="padding: 10px 16px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'">
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
