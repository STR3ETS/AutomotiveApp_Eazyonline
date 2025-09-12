@extends('layouts.app')

@section('content')
<div style="background-color: var(--text-kleur-white); min-height: 100vh;">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: var(--text-kleur-black); margin-bottom: 8px;">👷 Medewerkers</h1>
                <p style="color: var(--secondary-color);">Beheer je teamleden en hun werkzaamheden</p>
            </div>
            <a href="{{ route('employees.create') }}" 
               style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); font-weight: 600; padding: 12px 16px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Medewerker
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: rgba(22, 163, 74, 1); padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: rgba(220, 38, 38, 1); padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Employees Grid -->
        @if($employees->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($employees as $employee)
                    <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); overflow: hidden; transition: all 0.3s ease;"
                         onmouseover="this.style.boxShadow='0 12px 36px rgba(0,0,0,0.15)'; this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'">
                        <!-- Header -->
                        <div style="padding: 24px; border-bottom: 1px solid var(--third-color); background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--text-kleur-white), var(--third-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-weight: 700; font-size: 18px;">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 700; color: var(--text-kleur-white); font-size: 18px;">{{ $employee->name }}</h3>
                                        <p style="color: rgba(255,255,255,0.8); font-size: 14px;">{{ $employee->position }}</p>
                                    </div>
                                </div>
                                @if($employee->active)
                                    <span style="display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; background-color: rgba(34, 197, 94, 0.2); color: rgba(34, 197, 94, 1);">
                                        <i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px; color: rgba(34, 197, 94, 1);"></i>
                                        Actief
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; background-color: rgba(156, 163, 175, 0.2); color: rgba(156, 163, 175, 1);">
                                        <i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px; color: rgba(156, 163, 175, 1);"></i>
                                        Inactief
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div style="padding: 24px;">
                            <!-- Contact Info -->
                            @if($employee->email || $employee->phone)
                                <div style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 8px;">
                                    @if($employee->email)
                                        <div style="display: flex; align-items: center; font-size: 14px; color: var(--secondary-color);">
                                            <i class="fa-solid fa-envelope" style="width: 16px; margin-right: 8px;"></i>
                                            <span>{{ $employee->email }}</span>
                                        </div>
                                    @endif
                                    @if($employee->phone)
                                        <div style="display: flex; align-items: center; font-size: 14px; color: var(--secondary-color);">
                                            <i class="fa-solid fa-phone" style="width: 16px; margin-right: 8px;"></i>
                                            <span>{{ $employee->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Specializations -->
                            @if($employee->specializations && count($employee->specializations) > 0)
                                <div style="margin-bottom: 16px;">
                                    <h4 style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Specialisaties:</h4>
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach(array_slice($employee->specializations, 0, 3) as $spec)
                                            <span style="display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);">
                                                {{ $spec }}
                                            </span>
                                        @endforeach
                                        @if(count($employee->specializations) > 3)
                                            <span style="display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; background-color: var(--third-color); color: var(--secondary-color);">
                                                +{{ count($employee->specializations) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Current Assignments -->
                            <div style="margin-bottom: 16px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <h4 style="font-size: 14px; font-weight: 500; color: var(--text-kleur-black);">Huidige werkzaamheden:</h4>
                                    <span style="font-size: 12px; color: var(--secondary-color);">{{ $employee->getActiveAssignmentsCount() }}/3</span>
                                </div>
                                
                                @if($employee->currentAssignments->count() > 0)
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        @foreach($employee->currentAssignments->take(2) as $assignment)
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-radius: 8px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <i class="fa-solid fa-car" style="color: var(--primary-color);"></i>
                                                    <span style="font-size: 14px; font-weight: 500;">{{ $assignment->car->license_plate }}</span>
                                                </div>
                                                <span style="font-size: 12px; color: var(--secondary-color);">
                                                    {{ $assignment->assigned_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($employee->currentAssignments->count() > 2)
                                            <div style="text-align: center;">
                                                <span style="font-size: 12px; color: var(--secondary-color);">
                                                    +{{ $employee->currentAssignments->count() - 2 }} meer
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div style="text-align: center; padding: 12px 0; color: var(--secondary-color); font-size: 14px;">
                                        <i class="fa-solid fa-coffee" style="color: var(--third-color); margin-bottom: 8px; display: block;"></i>
                                        <p>Geen actieve werkzaamheden</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="padding: 16px 24px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); border-top: 1px solid var(--third-color);">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('employees.show', $employee) }}" 
                                   style="flex: 1; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); text-align: center; padding: 10px 12px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'">
                                    <i class="fa-solid fa-eye mr-2"></i>
                                    Bekijken
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" 
                                   style="background-color: var(--secondary-color); color: var(--text-kleur-white); padding: 10px 12px; border-radius: 8px; text-decoration: none; font-size: 14px; transition: all 0.3s ease;"
                                   onmouseover="this.style.backgroundColor='var(--primary-color)'"
                                   onmouseout="this.style.backgroundColor='var(--secondary-color)'">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div style="background-color: var(--text-kleur-white); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: 1px solid var(--third-color); padding: 32px;">
                <div style="text-align: center;">
                    <div style="margin: 0 auto 16px; height: 96px; width: 96px; color: var(--third-color);">
                        <i class="fa-solid fa-users" style="font-size: 72px;"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 500; color: var(--text-kleur-black); margin-bottom: 8px;">Nog geen medewerkers</h3>
                    <p style="color: var(--secondary-color); margin-bottom: 24px;">Begin met het toevoegen van je eerste medewerker om werkzaamheden te kunnen toewijzen.</p>
                    <a href="{{ route('employees.create') }}" 
                       style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white); font-weight: 600; padding: 12px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0,0,0,0.2);"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'">
                        <i class="fa-solid fa-plus"></i>
                        Eerste Medewerker Toevoegen
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
