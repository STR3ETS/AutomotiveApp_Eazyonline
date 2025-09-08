@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-2">👷 Medewerkers</h1>
                <p class="text-[var(--text-secondary)]">Beheer je teamleden en hun werkzaamheden</p>
            </div>
            <a href="{{ route('employees.create') }}" 
               class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] font-semibold py-2 px-4 rounded-lg transition duration-[var(--transition-normal)] flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Medewerker
            </a>
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

        <!-- Employees Grid -->
        @if($employees->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($employees as $employee)
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden hover:shadow-lg transition-all duration-[var(--transition-normal)]">
                        <!-- Header -->
                        <div class="p-6 border-b border-[var(--border-light)]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-[var(--text-primary)] text-lg">{{ $employee->name }}</h3>
                                        <p class="text-[var(--text-secondary)] text-sm">{{ $employee->position }}</p>
                                    </div>
                                </div>
                                @if($employee->active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fa-solid fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                                        Actief
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fa-solid fa-circle text-gray-500 mr-1" style="font-size: 6px;"></i>
                                        Inactief
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Contact Info -->
                            @if($employee->email || $employee->phone)
                                <div class="mb-4 space-y-2">
                                    @if($employee->email)
                                        <div class="flex items-center text-sm text-[var(--text-secondary)]">
                                            <i class="fa-solid fa-envelope w-4 mr-2"></i>
                                            <span>{{ $employee->email }}</span>
                                        </div>
                                    @endif
                                    @if($employee->phone)
                                        <div class="flex items-center text-sm text-[var(--text-secondary)]">
                                            <i class="fa-solid fa-phone w-4 mr-2"></i>
                                            <span>{{ $employee->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Specializations -->
                            @if($employee->specializations && count($employee->specializations) > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-[var(--text-primary)] mb-2">Specialisaties:</h4>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($employee->specializations, 0, 3) as $spec)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $spec }}
                                            </span>
                                        @endforeach
                                        @if(count($employee->specializations) > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                +{{ count($employee->specializations) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Current Assignments -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-[var(--text-primary)]">Huidige werkzaamheden:</h4>
                                    <span class="text-xs text-[var(--text-secondary)]">{{ $employee->getActiveAssignmentsCount() }}/3</span>
                                </div>
                                
                                @if($employee->currentAssignments->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($employee->currentAssignments->take(2) as $assignment)
                                            <div class="flex items-center justify-between p-2 bg-[var(--background-main)] rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-car text-blue-600"></i>
                                                    <span class="text-sm font-medium">{{ $assignment->car->license_plate }}</span>
                                                </div>
                                                <span class="text-xs text-[var(--text-secondary)]">
                                                    {{ $assignment->assigned_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($employee->currentAssignments->count() > 2)
                                            <div class="text-center">
                                                <span class="text-xs text-[var(--text-secondary)]">
                                                    +{{ $employee->currentAssignments->count() - 2 }} meer
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-3 text-[var(--text-secondary)] text-sm">
                                        <i class="fa-solid fa-coffee text-gray-400 mb-2"></i>
                                        <p>Geen actieve werkzaamheden</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 py-4 bg-[var(--background-main)] border-t border-[var(--border-light)]">
                            <div class="flex gap-2">
                                <a href="{{ route('employees.show', $employee) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg transition duration-200 text-sm font-medium">
                                    <i class="fa-solid fa-eye mr-2"></i>
                                    Bekijken
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" 
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-3 rounded-lg transition duration-200 text-sm">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-8">
                <div class="text-center">
                    <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                        <i class="fa-solid fa-users text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-[var(--text-primary)] mb-2">Nog geen medewerkers</h3>
                    <p class="text-[var(--text-secondary)] mb-6">Begin met het toevoegen van je eerste medewerker om werkzaamheden te kunnen toewijzen.</p>
                    <a href="{{ route('employees.create') }}" 
                       class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] font-semibold py-2 px-4 rounded-lg transition duration-[var(--transition-normal)] inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Eerste Medewerker Toevoegen
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
