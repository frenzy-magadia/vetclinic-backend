@extends('layouts.app')

@section('title', 'New Message')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#1e3a5f] px-4 sm:px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-bold text-white">New Message</h1>
            <a href="{{ route('messages.inbox') }}" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl sm:text-2xl"></i>
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('messages.store') }}" class="p-4 sm:p-6">
            @csrf

            <!-- To -->
            <div class="mb-4 sm:mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                
                @if(auth()->user()->role === 'pet_owner')
                    <!-- Regular dropdown for pet owners (only admins/doctors) -->
                    <select name="receiver_id" required 
                            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm @error('receiver_id') border-red-500 @enderror">
                        <option value="">Select recipient...</option>
                        @foreach($users as $user)
                            @if(in_array($user->role, ['admin', 'doctor']))
                                <option value="{{ $user->id }}" 
                                    {{ (old('receiver_id') == $user->id || request('receiver_id') == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <!-- Searchable dropdown for admin/doctor (all users) -->
                    <div class="relative">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="receiver_search" 
                                placeholder="Search recipient by name or role..."
                                class="w-full px-3 sm:px-4 py-2 sm:py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm"
                                autocomplete="off"
                            >
                            <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>

                        <!-- Results Dropdown -->
                        <div id="receiver_results" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

              
                    <select name="receiver_id" id="receiver_id" class="hidden" required>
                        <option value="">Select recipient...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    data-name="{{ strtolower($user->name) }}"
                                    data-role="{{ strtolower($user->role) }}"
                                    {{ (old('receiver_id') == $user->id || request('receiver_id') == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Selected Recipient Display -->
                    <div id="selected_receiver" class="hidden mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 text-sm" id="selected_receiver_name"></p>
                                <p class="text-xs text-gray-600" id="selected_receiver_role"></p>
                            </div>
                            <button type="button" onclick="clearReceiverSelection()" class="text-red-600 hover:text-red-800 ml-2">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @error('receiver_id')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="mb-4 sm:mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject', request('subject')) }}" required
                       placeholder="Enter subject..."
                       class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm @error('subject') border-red-500 @enderror">
                @error('subject')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="8" required
                          placeholder="Type your message here..."
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm resize-y @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('messages.inbox') }}" 
                   class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition font-semibold text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send</span>
                </button>
            </div>
        </form>
    </div>
</div>

@if(auth()->user()->role !== 'pet_owner')
<script>
// Recipient Search Functionality (only for admin/doctor)
const receiverSearchInput = document.getElementById('receiver_search');
const receiverSelect = document.getElementById('receiver_id');
const receiverResultsDiv = document.getElementById('receiver_results');
const selectedReceiverDiv = document.getElementById('selected_receiver');
const selectedReceiverNameEl = document.getElementById('selected_receiver_name');
const selectedReceiverRoleEl = document.getElementById('selected_receiver_role');

// Show all results when focusing on search
receiverSearchInput.addEventListener('focus', function() {
    if (!receiverSelect.value) {
        performReceiverSearch('');
    }
});

// Search as user types
receiverSearchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    performReceiverSearch(query);
});

// Perform receiver search
function performReceiverSearch(query) {
    const options = Array.from(receiverSelect.options).slice(1); 
    let results = options;

    if (query) {
        results = options.filter(option => {
            const name = option.dataset.name || '';
            const role = option.dataset.role || '';
            return name.includes(query) || role.includes(query);
        });
    }

    displayReceiverResults(results);
}

// Display receiver search results
function displayReceiverResults(results) {
    if (results.length === 0) {
        receiverResultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No recipients found</div>';
        receiverResultsDiv.classList.remove('hidden');
        return;
    }

    receiverResultsDiv.innerHTML = results.map(option => {
        const fullText = option.text;
        const parts = fullText.match(/^(.+?)\s*\((.+?)\)$/);
        const name = parts ? parts[1].trim() : fullText;
        let role = parts ? parts[2].trim() : '';
        
        role = role.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        
        return `
            <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
                 onclick="selectReceiver('${option.value}', \`${fullText.replace(/`/g, '\\`')}\`)">
                <p class="font-medium text-gray-900 text-sm">${name}</p>
                <p class="text-xs text-gray-500 mt-0.5">${role}</p>
            </div>
        `;
    }).join('');
    
    receiverResultsDiv.classList.remove('hidden');
}

// Select a receiver
function selectReceiver(value, fullText) {
    receiverSelect.value = value;
    const parts = fullText.match(/^(.+?)\s*\((.+?)\)$/);
    const name = parts ? parts[1].trim() : fullText;
    let role = parts ? parts[2].trim() : '';
    
   
    role = role.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    
    selectedReceiverNameEl.textContent = name;
    selectedReceiverRoleEl.textContent = role;
    
    receiverSearchInput.value = '';
    receiverResultsDiv.classList.add('hidden');
    selectedReceiverDiv.classList.remove('hidden');
    receiverSearchInput.parentElement.classList.add('hidden');
}

// Clear receiver selection
function clearReceiverSelection() {
    receiverSelect.value = '';
    selectedReceiverDiv.classList.add('hidden');
    receiverSearchInput.parentElement.classList.remove('hidden');
    receiverSearchInput.value = '';
    receiverSearchInput.focus();
}

// Close results when clicking outside
document.addEventListener('click', function(e) {
    if (!receiverSearchInput.contains(e.target) && !receiverResultsDiv.contains(e.target)) {
        receiverResultsDiv.classList.add('hidden');
    }
});


@if(old('receiver_id') || request('receiver_id'))
    const selectedOption = receiverSelect.options[receiverSelect.selectedIndex];
    if (selectedOption.value) {
        selectReceiver(selectedOption.value, selectedOption.text);
    }
@endif
</script>
@endif
@endsection