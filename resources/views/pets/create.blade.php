@extends('layouts.app')

@section('title', 'Add Pet')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-[#2d3748] mb-6">
                <i class="fas fa-plus-circle text-[#fcd34d] mr-2"></i>Add New Pet
            </h1>

            <form method="POST" action="{{ route('pets.store') }}" class="space-y-6">
                @csrf

                <!-- Pet Owner with Search -->
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
                <div>
                    <label for="owner_search" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user text-[#d4931d] mr-1"></i>Pet Owner *
                    </label>
                    
                    <!-- Search Input Container -->
                    <div class="relative mt-1">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="owner_search" 
                                placeholder="Search pet owner by name or email..."
                                class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm"
                                autocomplete="off"
                            >
                            <i class="fas fa-search absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                        </div>

                        <!-- Results Dropdown -->
                        <div id="owner_results" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <!-- Hidden Select (stores actual value) -->
                    <select id="owner_id" name="owner_id" required class="hidden @error('owner_id') border-red-500 @enderror">
                        <option value="">Select pet owner</option>
                        @foreach($petOwners as $owner)
                        <option value="{{ $owner->id }}" 
                                data-name="{{ strtolower($owner->user->name) }}"
                                data-email="{{ strtolower($owner->user->email) }}"
                                {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->user->name }} ({{ $owner->user->email }})
                        </option>
                        @endforeach
                    </select>

                    <!-- Selected Owner Display -->
                    <div id="selected_owner" class="hidden mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 text-sm" id="selected_owner_name"></p>
                                <p class="text-xs text-gray-600" id="selected_owner_email"></p>
                            </div>
                            <button type="button" onclick="clearOwnerSelection()" class="text-red-600 hover:text-red-800 ml-2">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>

                    @error('owner_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <!-- Pet Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-paw text-[#fcd34d] mr-1"></i>Pet Name
                    </label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('name') border-red-500 @enderror" 
                           placeholder="Enter pet name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Species and Breed -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-list text-[#d4931d] mr-1"></i>Species
                        </label>
                        <select id="species" name="species" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('species') border-red-500 @enderror">
                            <option value="">Select species</option>
                            <option value="dog" {{ old('species') == 'dog' ? 'selected' : '' }}>Dog</option>
                            <option value="cat" {{ old('species') == 'cat' ? 'selected' : '' }}>Cat</option>
                            <option value="bird" {{ old('species') == 'bird' ? 'selected' : '' }}>Bird</option>
                            <option value="rabbit" {{ old('species') == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                            <option value="hamster" {{ old('species') == 'hamster' ? 'selected' : '' }}>Hamster</option>
                            <option value="fish" {{ old('species') == 'fish' ? 'selected' : '' }}>Fish</option>
                            <option value="other" {{ old('species') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('species')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-dog text-[#fcd34d] mr-1"></i>Breed
                        </label>
                        <input id="breed" name="breed" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('breed') border-red-500 @enderror" 
                               placeholder="Enter breed" value="{{ old('breed') }}">
                        @error('breed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Age and Weight -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-birthday-cake text-[#d4931d] mr-1"></i>Age (years)
                        </label>
                        <input id="age" name="age" type="number" min="0" max="30" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('age') border-red-500 @enderror" 
                               placeholder="0" value="{{ old('age') }}">
                        @error('age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-weight text-[#fcd34d] mr-1"></i>Weight (kg)
                        </label>
                        <input id="weight" name="weight" type="number" step="0.1" min="0" max="1000" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('weight') border-red-500 @enderror" 
                               placeholder="0.0" value="{{ old('weight') }}">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Color and Gender -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-palette text-[#d4931d] mr-1"></i>Color
                        </label>
                        <input id="color" name="color" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('color') border-red-500 @enderror" 
                               placeholder="Enter color" value="{{ old('color') }}">
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-venus-mars text-[#fcd34d] mr-1"></i>Gender
                        </label>
                        <select id="gender" name="gender" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('gender') border-red-500 @enderror">
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="unknown" {{ old('gender') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Medical Notes -->
                <div>
                    <label for="medical_notes" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-notes-medical text-[#d4931d] mr-1"></i>Medical Notes
                    </label>
                    <textarea id="medical_notes" name="medical_notes" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] sm:text-sm @error('medical_notes') border-red-500 @enderror" 
                              placeholder="Enter any medical notes or special requirements">{{ old('medical_notes') }}</textarea>
                    @error('medical_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.pets') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#0066cc] hover:bg-[#003d82]">
                        <i class="fas fa-save mr-2 text-[#fcd34d]"></i>
                        Add Pet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('owner_search');
    const ownerSelect = document.getElementById('owner_id');
    const resultsDiv = document.getElementById('owner_results');
    const selectedDiv = document.getElementById('selected_owner');
    const selectedNameEl = document.getElementById('selected_owner_name');
    const selectedEmailEl = document.getElementById('selected_owner_email');

    // Show all results when focusing on search
    searchInput.addEventListener('focus', function() {
        if (!ownerSelect.value) {
            performSearch('');
        }
    });

    // Search as user types
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        performSearch(query);
    });

    // Perform search
    function performSearch(query) {
        const options = Array.from(ownerSelect.options).slice(1); // Skip first "Select pet owner"
        let results = options;

        if (query) {
            results = options.filter(option => {
                const name = option.dataset.name || '';
                const email = option.dataset.email || '';
                return name.includes(query) || email.includes(query);
            });
        }

        displayResults(results);
    }

    // Display search results
    function displayResults(results) {
        if (results.length === 0) {
            resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No pet owners found</div>';
            resultsDiv.classList.remove('hidden');
            return;
        }

        resultsDiv.innerHTML = results.map(option => {
            const name = option.text.split('(')[0].trim();
            const email = option.text.match(/\((.*?)\)/)?.[1] || '';
            return `
                <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
                     onclick="selectOwner('${option.value}', \`${option.text.replace(/`/g, '\\`')}\`)">
                    <p class="font-medium text-gray-900 text-sm">${name}</p>
                    <p class="text-xs text-gray-500 mt-0.5">${email}</p>
                </div>
            `;
        }).join('');
        
        resultsDiv.classList.remove('hidden');
    }

    // Select an owner
    function selectOwner(value, fullText) {
        ownerSelect.value = value;
        const name = fullText.split('(')[0].trim();
        const email = fullText.match(/\((.*?)\)/)?.[1] || '';
        
        selectedNameEl.textContent = name;
        selectedEmailEl.textContent = email;
        
        searchInput.value = '';
        resultsDiv.classList.add('hidden');
        selectedDiv.classList.remove('hidden');
        searchInput.parentElement.classList.add('hidden');
    }

    // Clear selection
    function clearOwnerSelection() {
        ownerSelect.value = '';
        selectedDiv.classList.add('hidden');
        searchInput.parentElement.classList.remove('hidden');
        searchInput.value = '';
        searchInput.focus();
    }

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });

    // If there's an old value (form validation error), show it
    @if(old('owner_id'))
        const selectedOption = ownerSelect.options[ownerSelect.selectedIndex];
        if (selectedOption.value) {
            selectOwner(selectedOption.value, selectedOption.text);
        }
    @endif
</script>
@endsection