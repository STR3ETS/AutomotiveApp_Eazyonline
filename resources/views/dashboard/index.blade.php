@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-start border border-gray-100">
            <span class="text-gray-500 mb-2">Voorraad Totaal</span>
            <span class="text-4xl font-extrabold text-black">{{ $total }}</span>
            <span class="text-gray-400 text-xs">Auto's in voorraad</span>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-start border border-gray-100">
            <span class="text-gray-500 mb-2">Inkoop in behandeling</span>
            <span class="text-4xl font-extrabold text-black">{{ $intake }}</span>
            <span class="text-gray-400 text-xs">Nieuwe innames</span>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-start border border-gray-100">
            <span class="text-gray-500 mb-2">Advertenties Live</span>
            <span class="text-4xl font-extrabold text-black">{{ $total }}</span>
            <span class="text-gray-400 text-xs">Auto's in voorraad</span>
        </div>
        </div>
    </div>
</div>
@endsection
