@extends('layouts.app')

@section('title', 'Create Medical Record')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4" style="background-color: #34495e;">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-file-medical mr-2" style="color: #f4d03f;"></i>Create Medical Record
                </h1>
                <a href="{{ Auth::user()->isDoctor() ? route('doctor.medical-records') : route('pet-owner.medical-records') }}" 
                   class="px-4 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="mx-6 mt-6 border-l-4 p-4 rounded" style="background-color: #f8d7da; border-color: #d32f2f; color: #721c24;">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('medical-records.store') }}" class="p-6">
            @csrf

            <!-- Pet Selection with Search -->
            <div class="mb-6">
                <label for="pet_search" class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-paw mr-1" style="color: #f4d03f;"></i>Pet *
                </label>
                
                @if(isset($selectedPetId))
                    @php
                        $selectedPet = $pets->firstWhere('id', $selectedPetId);
                    @endphp
                    <!-- Display selected pet info -->
                    <div class="p-4 rounded-lg border-2" style="background-color: #d6eaf8; border-color: #0d5cb6;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-lg" style="color: #2c3e50;">
                                    <i class="fas fa-paw mr-2" style="color: #0d5cb6;"></i>
                                    {{ $selectedPet->name }}
                                </p>
                                <p class="text-sm mt-1" style="color: #5d6d7e;">
                                    {{ ucfirst($selectedPet->species) }} 
                                    @if($selectedPet->breed)
                                        - {{ $selectedPet->breed }}
                                    @endif
                                    | Owner: {{ $selectedPet->owner->user->name }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #0d5cb6; color: #ffffff;">
                                Selected
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="pet_id" value="{{ $selectedPetId }}">
                    <p class="mt-2 text-sm" style="color: #5d6d7e;">
                        <i class="fas fa-info-circle mr-1"></i>Creating medical record for this pet
                    </p>
                @else
                    <!-- Search Container -->
                    <div class="relative" id="pet_search_container">
                        <input 
                            type="text" 
                            id="pet_search" 
                            class="w-full px-4 py-3 pr-10 border rounded-lg focus:ring-2 focus:border-blue-500" 
                            style="border-color: #d1d5db;"
                            placeholder="Search pet by name, species, breed, or owner..."
                            autocomplete="off"
                        >
                        <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        
                        <!-- Search Results Dropdown -->
                        <div id="pet_results" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg hidden max-h-64 overflow-y-auto" style="border-color: #d1d5db;">
                            <!-- Results will be inserted here -->
                        </div>
                    </div>

                    <!-- Hidden input for selected pet ID -->
                    <input type="hidden" id="pet_id" name="pet_id" value="{{ old('pet_id') }}" required>

                    <!-- Selected Pet Display -->
                    <div id="selected_pet_display" class="mt-3 p-4 rounded-lg border-2 hidden" style="background-color: #d6eaf8; border-color: #0d5cb6;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold" style="color: #2c3e50;">
                                    <i class="fas fa-paw mr-2" style="color: #0d5cb6;"></i>
                                    <span id="selected_pet_name"></span>
                                </p>
                                <p class="text-sm mt-1" style="color: #5d6d7e;" id="selected_pet_details"></p>
                            </div>
                            <button type="button" id="clear_selection" class="px-3 py-1 rounded text-sm transition" style="background-color: #95a5a6; color: #ffffff;">
                                <i class="fas fa-times mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                @endif
                
                @error('pet_id')
                    <p class="mt-1 text-sm" style="color: #d32f2f;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Diagnosis -->
            <div class="mb-6">
                <label for="diagnosis" class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-diagnoses mr-1" style="color: #f4d03f;"></i>Diagnosis *
                </label>
                <textarea id="diagnosis" name="diagnosis" rows="4" required 
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:border-blue-500" 
                          style="border-color: #d1d5db;"
                          placeholder="Enter detailed diagnosis...">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <p class="mt-1 text-sm" style="color: #d32f2f;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Treatment -->
            <div class="mb-6">
                <label for="treatment" class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-prescription mr-1" style="color: #f4d03f;"></i>Treatment *
                </label>
                <textarea id="treatment" name="treatment" rows="4" required 
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:border-blue-500" 
                          style="border-color: #d1d5db;"
                          placeholder="Enter treatment plan and procedures...">{{ old('treatment') }}</textarea>
                @error('treatment')
                    <p class="mt-1 text-sm" style="color: #d32f2f;">{{ $message }}</p>
                @enderror
            </div>

            <!-- General Prescription Notes -->
            <div class="mb-6">
                <label for="prescription" class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-notes-medical mr-1" style="color: #f4d03f;"></i>General Prescription Notes
                </label>
                <textarea id="prescription" name="prescription" rows="3" 
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:border-blue-500" 
                          style="border-color: #d1d5db;"
                          placeholder="Enter general prescription notes...">{{ old('prescription') }}</textarea>
            </div>

            <!-- Medications Section -->
            <div class="mb-6 p-6 rounded-lg" style="background-color: #f9fafb; border: 2px solid #e5e7eb;">
                <div class="flex justify-between items-center mb-4">
                    <label class="text-sm font-medium" style="color: #2c3e50;">
                        <i class="fas fa-pills mr-1" style="color: #f4d03f;"></i>Medications
                    </label>
                    <button type="button" id="add-medication" 
                            class="px-4 py-2 text-white rounded-lg transition" 
                            style="background-color: #0d5cb6;">
                        <i class="fas fa-plus mr-2"></i>Add Medication
                    </button>
                </div>
                
                <div id="medications-container">
                    <!-- Initial medication row -->
                    <div class="medication-row mb-4 p-4 rounded-lg" style="background-color: #ffffff; border: 1px solid #d1d5db;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Medication Name</label>
                                <input type="text" name="medications[0][name]" 
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Amoxicillin">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Dosage</label>
                                <input type="text" name="medications[0][dosage]" 
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., 250mg">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Frequency</label>
                                <input type="text" name="medications[0][frequency]" 
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Twice daily">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Duration (days)</label>
                                <input type="number" name="medications[0][duration_days]" min="1" 
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="7">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Instructions</label>
                                <input type="text" name="medications[0][instructions]" 
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Take with food">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t" style="border-color: #e5e7eb;">
                <a href="{{ Auth::user()->isDoctor() ? route('doctor.medical-records') : route('pet-owner.medical-records') }}" 
                   class="px-6 py-3 rounded-lg transition font-medium" 
                   style="background-color: #95a5a6; color: #ffffff;">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 text-white rounded-lg transition font-medium" 
                        style="background-color: #0d5cb6;">
                    <i class="fas fa-save mr-2"></i>Create Medical Record
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Pet search data
const pets = @json($pets);

// Medication management
let medicationIndex = 1;

document.getElementById('add-medication').addEventListener('click', function() {
    const container = document.getElementById('medications-container');
    const newRow = document.createElement('div');
    newRow.className = 'medication-row mb-4 p-4 rounded-lg';
    newRow.style.backgroundColor = '#ffffff';
    newRow.style.border = '1px solid #d1d5db';
    newRow.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-medium" style="color: #2c3e50;">Medication ${medicationIndex + 1}</span>
            <button type="button" onclick="this.closest('.medication-row').remove()" 
                    class="px-3 py-1 rounded text-sm transition" 
                    style="background-color: #d32f2f; color: #ffffff;">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Medication Name</label>
                <input type="text" name="medications[${medicationIndex}][name]" 
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                       style="border-color: #d1d5db;"
                       placeholder="e.g., Amoxicillin">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Dosage</label>
                <input type="text" name="medications[${medicationIndex}][dosage]" 
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                       style="border-color: #d1d5db;"
                       placeholder="e.g., 250mg">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Frequency</label>
                <input type="text" name="medications[${medicationIndex}][frequency]" 
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                       style="border-color: #d1d5db;"
                       placeholder="e.g., Twice daily">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Duration (days)</label>
                <input type="number" name="medications[${medicationIndex}][duration_days]" min="1" 
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                       style="border-color: #d1d5db;"
                       placeholder="7">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Instructions</label>
                <input type="text" name="medications[${medicationIndex}][instructions]" 
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                       style="border-color: #d1d5db;"
                       placeholder="e.g., Take with food">
            </div>
        </div>
    `;
    container.appendChild(newRow);
    medicationIndex++;
});

// Pet search functionality
const petSearchInput = document.getElementById('pet_search');
const petResultsDiv = document.getElementById('pet_results');
const petIdInput = document.getElementById('pet_id');
const selectedPetDisplay = document.getElementById('selected_pet_display');
const clearSelectionBtn = document.getElementById('clear_selection');

if (petSearchInput) {
    petSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm.length < 1) {
            petResultsDiv.classList.add('hidden');
            return;
        }
        
        // Filter pets
        const filteredPets = pets.filter(pet => {
            const petName = pet.name.toLowerCase();
            const petSpecies = pet.species.toLowerCase();
            const petBreed = pet.breed ? pet.breed.toLowerCase() : '';
            const ownerName = pet.owner.user.name.toLowerCase();
            
            return petName.includes(searchTerm) || 
                   petSpecies.includes(searchTerm) || 
                   petBreed.includes(searchTerm) || 
                   ownerName.includes(searchTerm);
        });
        
        // Display results
        if (filteredPets.length > 0) {
            petResultsDiv.innerHTML = filteredPets.map(pet => `
                <div class="pet-result-item p-2 hover:bg-blue-50 cursor-pointer border-b transition" 
                     data-pet-id="${pet.id}"
                     data-pet-name="${pet.name}"
                     data-pet-species="${pet.species}"
                     data-pet-breed="${pet.breed || ''}"
                     data-owner-name="${pet.owner.user.name}"
                     style="border-color: #e5e7eb;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold" style="color: #2c3e50;">
                                <i class="fas fa-paw mr-2 text-xs" style="color: #3498db;"></i>${pet.name}
                            </p>
                            <p class="text-xs" style="color: #5d6d7e;">
                                ${pet.species.charAt(0).toUpperCase() + pet.species.slice(1)}
                                ${pet.breed ? ' - ' + pet.breed : ''}
                                <span class="mx-1">|</span>
                                Owner: ${pet.owner.user.name}
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-xs" style="color: #95a5a6;"></i>
                    </div>
                </div>
            `).join('');
            petResultsDiv.classList.remove('hidden');
            
            // Add click handlers
            document.querySelectorAll('.pet-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectPet(
                        this.dataset.petId,
                        this.dataset.petName,
                        this.dataset.petSpecies,
                        this.dataset.petBreed,
                        this.dataset.ownerName
                    );
                });
            });
        } else {
            petResultsDiv.innerHTML = `
                <div class="p-4 text-center" style="color: #5d6d7e;">
                    <i class="fas fa-search text-2xl mb-2" style="color: #d1d5db;"></i>
                    <p class="text-sm">No pets found</p>
                </div>
            `;
            petResultsDiv.classList.remove('hidden');
        }
    });
    
    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!petSearchInput.contains(e.target) && !petResultsDiv.contains(e.target)) {
            petResultsDiv.classList.add('hidden');
        }
    });
}

function selectPet(id, name, species, breed, ownerName) {
    petIdInput.value = id;
    petSearchInput.value = '';
    petResultsDiv.classList.add('hidden');
    
    document.getElementById('selected_pet_name').textContent = name;
    document.getElementById('selected_pet_details').textContent = 
        `${species.charAt(0).toUpperCase() + species.slice(1)}${breed ? ' - ' + breed : ''} | Owner: ${ownerName}`;
    
    selectedPetDisplay.classList.remove('hidden');
}

if (clearSelectionBtn) {
    clearSelectionBtn.addEventListener('click', function() {
        petIdInput.value = '';
        selectedPetDisplay.classList.add('hidden');
        petSearchInput.focus();
    });
}
</script>

<style>
button:hover, a:hover {
    opacity: 0.9;
}

.pet-result-item:last-child {
    border-bottom: none;
}
</style>
@endsection