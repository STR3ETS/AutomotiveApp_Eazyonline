<div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-start">
    <div>
        <h3 class="text-sm font-medium text-gray-500">{{ $title }}</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
        <p class="text-sm text-gray-500">{{ $subtitle }}</p>

        @if($change !== null)
            <p class="text-sm mt-1 {{ $change > 0 ? 'text-green-600' : 'text-red-600' }}">
                <span class="inline-flex items-center">
                    @if($change > 0)
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    @elseif($change < 0)
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    @endif
                    {{ $change > 0 ? '+' : '' }}{{ $change }}
                    deze week
                </span>
            </p>
        @endif
    </div>

    <div class="bg-gray-100 text-gray-600 p-2 rounded-md">
        <i class="fa-solid fa-car text-xl"></i>
    </div>
</div>