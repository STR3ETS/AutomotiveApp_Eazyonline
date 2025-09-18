@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Theme Settings</h1>
        
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.theme.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                        Primary Color
                    </label>
                    <div class="flex items-center space-x-3">
                        <input 
                            type="color" 
                            id="primary_color" 
                            name="primary_color" 
                            value="{{ $company->primary_color }}"
                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                        >
                        <input 
                            type="text" 
                            value="{{ $company->primary_color }}" 
                            class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm"
                            readonly
                        >
                    </div>
                    @error('primary_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Logo
                    </label>
                    
                    @if($company->logo_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $company->logo_path) }}" 
                                 alt="Current logo" 
                                 class="h-16 w-auto border border-gray-200 rounded">
                        </div>
                    @endif
                    
                    <input 
                        type="file" 
                        id="logo" 
                        name="logo" 
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Preview</h3>
                    <div class="space-y-2">
                        <button type="button" class="btn-primary">Primary Button</button>
                        <div class="text-sm text-gray-600">This shows how your primary color will look</div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('primary_color').addEventListener('input', function(e) {
    document.querySelector('input[readonly]').value = e.target.value;
    
    // Live preview update
    document.documentElement.style.setProperty('--primary-color', e.target.value);
});
</script>
@endsection
