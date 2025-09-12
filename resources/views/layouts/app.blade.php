<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://kit.fontawesome.com/4180a39c11.js" crossorigin="anonymous"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    {{-- fallback css --}}
    @endif
</head>

<body class="flex min-h-screen">

    <!-- Sidebar -->
    <div class="px-[1rem] py-[1.5rem] flex flex-col gap-16 shadow-lg" style="background-color: var(--primary-color);">
        <div class="flex items-center gap-4 pl-2">
            <div
                class="w-8 h-8 rounded-[var(--border-radius)] flex items-center justify-center shadow-md" 
                style="background-color: var(--secondary-color);">
                <i class="fa-solid fa-car-side fa-sm" style="color: var(--text-kleur-white);"></i>
            </div>
            <div>
                <h5 class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">
                    {{ Auth::check() && isset($currentCompany) ? $currentCompany->name : 'Bedrijfsnaam' }}
                </h5>
                <h6 class="text-xs font-medium tracking-tighter" style="color: var(--third-color);">Operations Center</h6>
            </div>
        </div>

        <!-- Navigatie -->
        <div>
            <h4 class="text-xs font-semibold tracking-tighter pl-2 mb-2" style="color: var(--third-color);">Navigatie</h4>
            <div class="flex flex-col">

                <!-- Altijd zichtbaar -->
                <a href="/"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md" 
                    style="hover:background-color: var(--secondary-color);"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-house fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Overzicht</p>
                    </div>
                </a>
                <a href="/agenda"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-calendar-days fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Agenda</p>
                    </div>
                </a>
                <a href="/pipeline"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-box fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Voorraad Pipeline</p>
                    </div>
                </a>
                <a href="/repairs"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-wrench fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Reparaties</p>
                    </div>
                </a>

                <!-- Alleen medewerkers -->
                @if(Auth::check() && Auth::user()->employee?->position === 'medewerker')
                <a href="{{ route('employees.show', Auth::user()->employee) }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-user fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Mijn Werk</p>
                    </div>
                </a>
                @endif

                <!-- Voor iedereen -->
                <a href="{{ route('autos.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-car fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Auto Beheer</p>
                    </div>
                </a>
                <a href="{{ route('sales-ready.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-check fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Verkoop Klaar</p>
                    </div>
                </a>
                <a href="{{ route('customers.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-users fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Klanten</p>
                    </div>
                </a>
                <a href="{{ route('active-sales.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-handshake fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Klaar voor Oplevering
                        </p>
                    </div>
                </a>

                <!-- Alleen owners -->
                @if(!Auth::check() || Auth::user()->employee?->position !== 'medewerker')
                <a href="{{ route('employees.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-user-hard-hat fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Medewerkers</p>
                    </div>
                </a>
                <a href="{{ route('reports.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-clipboard fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Rapportage</p>
                    </div>
                </a>
                <a href="{{ route('company-settings.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:shadow-md"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-gear fa-sm" style="color: var(--text-kleur-white);"></i>
                        <p class="text-sm font-semibold tracking-tighter" style="color: var(--text-kleur-white);">Bedrijfsinstellingen
                        </p>
                    </div>
                </a>
                @endif

            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 h-full flex flex-col">
        <div class="p-[1rem] flex justify-between items-center shadow-sm" style="border-bottom: 1px solid var(--third-color);">
            <div>
                <h3 class="font-bold tracking-tighter text-lg leading-tight" style="color: var(--text-kleur-black);">
                    Goeiedag {{ Auth::user()?->name }}
                </h3>
                <h4 class="font-semibold tracking-tighter text-sm leading-tight" style="color: var(--secondary-color);">
                    {{ now()->format('l j F Y') }}
                </h4>
            </div>
            @if(Auth::check())
            <div class="rounded-lg p-3 mb-4 shadow-sm" style="background-color: var(--third-color); border: 1px solid var(--secondary-color);">
                <div class="flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-user" style="color: var(--primary-color);"></i>
                    <span class="font-medium" style="color: var(--text-kleur-black);">{{ Auth::user()->name }}</span>
                    <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: var(--secondary-color); color: var(--text-kleur-white);">
                        {{ ucfirst(Auth::user()->display_role) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded text-sm font-medium transition-colors duration-200 shadow-sm hover:shadow-md"
                    style="background-color: var(--primary-color); color: var(--text-kleur-white);"
                    onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                    onmouseout="this.style.backgroundColor='var(--primary-color)'">
                    <i class="fa-solid fa-sign-out-alt mr-2"></i>Uitloggen
                </button>
            </form>
            @endif
        </div>

        <div class="flex-1">
            @yield('content')
        </div>
    </div>
</body>

</html>