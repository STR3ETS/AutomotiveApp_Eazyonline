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
    <div class="px-[1rem] py-[1.5rem] bg-[var(--primary-color)] flex flex-col gap-16">
        <div class="flex items-center gap-4 pl-2">
            <div
                class="w-8 h-8 bg-[var(--primary-light)] rounded-[var(--border-radius)] flex items-center justify-center">
                <i class="fa-solid fa-car-side fa-sm text-[var(--text-white)]"></i>
            </div>
            <div>
                <h5 class="text-[var(--text-white)] text-sm font-semibold tracking-tighter">
                    {{ Auth::check() && isset($currentCompany) ? $currentCompany->name : 'Bedrijfsnaam' }}
                </h5>
                <h6 class="text-[var(--text-white-dimmed)] text-xs font-medium tracking-tighter">Operations Center</h6>
            </div>
        </div>

        <!-- Navigatie -->
        <div>
            <h4 class="text-[var(--text-white-muted)] text-xs font-semibold tracking-tighter pl-2 mb-2">Navigatie</h4>
            <div class="flex flex-col">

                <!-- Altijd zichtbaar -->
                <a href="/"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-house fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Overzicht</p>
                    </div>
                </a>
                <a href="/agenda"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-calendar-days fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Agenda</p>
                    </div>
                </a>
                <a href="/pipeline"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-box fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Voorraad Pipeline</p>
                    </div>
                </a>
                <a href="/repairs"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-wrench fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Reparaties</p>
                    </div>
                </a>

                <!-- Alleen medewerkers -->
                @if(Auth::check() && Auth::user()->employee?->position === 'medewerker')
                <a href="{{ route('employees.show', Auth::user()->employee) }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-user fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Mijn Werk</p>
                    </div>
                </a>
                @endif

                <!-- Voor iedereen -->
                <a href="{{ route('autos.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-car fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Auto Beheer</p>
                    </div>
                </a>
                <a href="{{ route('sales-ready.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-check fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Verkoop Klaar</p>
                    </div>
                </a>
                <a href="{{ route('customers.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-users fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Klanten</p>
                    </div>
                </a>
                <a href="{{ route('active-sales.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-handshake fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Klaar voor Oplevering
                        </p>
                    </div>
                </a>

                <!-- Alleen owners -->
                @if(!Auth::check() || Auth::user()->employee?->position !== 'medewerker')
                <a href="{{ route('employees.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-user-hard-hat fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Medewerkers</p>
                    </div>
                </a>
                <a href="{{ route('reports.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-clipboard fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Rapportage</p>
                    </div>
                </a>
                <a href="{{ route('company-settings.index') }}"
                    class="flex items-center transition duration-200 rounded-[var(--border-radius)] hover:bg-[var(--primary-lighter)]">
                    <div class="flex items-center gap-2 w-[200px] p-2">
                        <i class="fa-solid fa-gear fa-sm text-[var(--text-white)]"></i>
                        <p class="text-sm text-[var(--text-white)] font-semibold tracking-tighter">Bedrijfsinstellingen
                        </p>
                    </div>
                </a>
                @endif

            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 h-full flex flex-col">
        <div class="p-[1rem] border-b border-[#e2e2e2] flex justify-between items-center">
            <div>
                <h3 class="text-[var(--text-color)] font-bold tracking-tighter text-lg leading-tight">
                    Goeiedag {{ Auth::user()?->name }}
                </h3>
                <h4 class="text-[var(--text-color)]/50 font-semibold tracking-tighter text-sm leading-tight">
                    {{ now()->format('l j F Y') }}
                </h4>
            </div>
            @if(Auth::check())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-user text-blue-600"></i>
                    <span class="font-medium text-blue-900">{{ Auth::user()->name }}</span>
                    <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded text-xs font-medium">
                        {{ ucfirst(Auth::user()->display_role) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors duration-200">
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