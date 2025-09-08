@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Inloggen
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welkom bij uw automotive management systeem
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('auth.login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Gebruikersnaam</label>
                    <input id="username" name="username" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Gebruikersnaam" value="{{ old('username') }}">
                </div>
                <div>
                    <label for="password" class="sr-only">Wachtwoord</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Wachtwoord">
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Inloggen
                </button>
            </div>
        </form>

        <!-- Test credentials voor development -->
        @if(app()->environment('local'))
            <div class="bg-blue-50 border border-blue-200 rounded p-4 mt-4">
                <h4 class="font-semibold text-blue-800 mb-2">Test Inloggegevens:</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <div><strong>AutoGarage Piet:</strong> piet / piet123</div>
                    <div><strong>De Snelle Garage:</strong> snelle / snelle123</div>
                    <div><strong>Premium Motors:</strong> premium / premium123</div>
                    <div><strong>Buurgarage Jan:</strong> jan / jan123</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
