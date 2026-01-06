@extends('layouts.app')

@section('title', 'Edit Medical Record')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4" style="background-color: #34495e;">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-edit mr-2" style="color: #f4d03f;"></i>Edit Medical Record
                </h1>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.medical-records') }}" class="px-4 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                @elseif(Auth::user()->isDoctor())
                    <a href="{{ route('doctor.medical-records') }}" class="px-4 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                @else
                    <a href="{{ route('pet-owner.medical-records') }}" class="px-4 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                @endif
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

        <form method="POST" action="{{ route('medical-records.update', $medicalRecord->id) }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Pet Selection (Read-only) -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-paw mr-1" style="color: #f4d03f;"></i>Pet
                </label>
                <div class="p-4 rounded-lg border-2" style="background-color: #f3f4f6; border-color: #d1d5db;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold text-lg" style="color: #2c3e50;">
                                <i class="fas fa-paw mr-2" style="color: #3498db;"></i>
                                {{ $medicalRecord->pet->name }}
                            </p>
                            <p class="text-sm mt-1" style="color: #5d6d7e;">
                                {{ ucfirst($medicalRecord->pet->species) }} 
                                @if($medicalRecord->pet->breed)
                                    - {{ $medicalRecord->pet->breed }}
                                @endif
                                | Owner: {{ $medicalRecord->pet->owner->user->name }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #95a5a6; color: #ffffff;">
                            <i class="fas fa-lock mr-1"></i>Locked
                        </span>
                    </div>
                </div>
                <input type="hidden" name="pet_id" value="{{ $medicalRecord->pet_id }}">
                <p class="mt-2 text-sm" style="color: #5d6d7e;">
                    <i class="fas fa-info-circle mr-1"></i>Pet cannot be changed after record creation
                </p>
            </div>

            <!-- Doctor Info (Read-only) -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-user-md mr-1" style="color: #f4d03f;"></i>Attending Doctor
                </label>
                <div class="p-4 rounded-lg" style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-md" style="color: #3498db;"></i>
                        <span class="font-medium" style="color: #2c3e50;">{{ $doctor->user->name }}</span>
                        @if($doctor->specialization)
                            <span class="text-sm" style="color: #5d6d7e;">({{ $doctor->specialization }})</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Appointment Selection with Search -->
            <div class="mb-6">
                <label for="appointment_search" class="block text-sm font-medium mb-2" style="color: #2c3e50;">
                    <i class="fas fa-calendar-alt mr-1" style="color: #f4d03f;"></i>Related Appointment (Optional)
                </label>
                
                <!-- Search Container -->
                <div class="relative" id="appointment_search_container">
                    <input 
                        type="text" 
                        id="appointment_search" 
                        class="w-full px-4 py-3 pr-10 border rounded-lg focus:ring-2 focus:border-blue-500" 
                        style="border-color: #d1d5db;"
                        placeholder="Search appointment by pet, service, or date..."
                        autocomplete="off"
                    >
                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    
                    <!-- Search Results Dropdown -->
                    <div id="appointment_results" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg hidden max-h-64 overflow-y-auto" style="border-color: #d1d5db;">
                        <!-- Results will be inserted here -->
                    </div>
                </div>

                <!-- Hidden input for selected appointment ID -->
                <input type="hidden" id="appointment_id" name="appointment_id" value="{{ old('appointment_id', $medicalRecord->appointment_id) }}">

                <!-- Selected Appointment Display -->
                <div id="selected_appointment_display" class="mt-3 p-4 rounded-lg border-2 {{ $medicalRecord->appointment_id ? '' : 'hidden' }}" style="background-color: #d6eaf8; border-color: #0d5cb6;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold" style="color: #2c3e50;">
                                <i class="fas fa-calendar-check mr-2" style="color: #0d5cb6;"></i>
                                <span id="selected_appointment_name">
                                    @if($medicalRecord->appointment)
                                        {{ $medicalRecord->appointment->pet->name }} - {{ $medicalRecord->appointment->service->name }}
                                    @endif
                                </span>
                            </p>
                            <p class="text-sm mt-1" style="color: #5d6d7e;" id="selected_appointment_details">
                                @if($medicalRecord->appointment)
                                    {{ $medicalRecord->appointment->appointment_date->format('M d, Y') }} at {{ $medicalRecord->appointment->appointment_date->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                        <button type="button" id="clear_appointment_selection" class="px-3 py-1 rounded text-sm transition" style="background-color: #95a5a6; color: #ffffff;">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </div>
                </div>
                
                @error('appointment_id')
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
                          placeholder="Enter detailed diagnosis...">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
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
                          placeholder="Enter treatment plan and procedures...">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
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
                          placeholder="Enter general prescription notes...">{{ old('prescription', $medicalRecord->prescription) }}</textarea>
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
                    @forelse($medicalRecord->prescriptions as $index => $prescription)
                    <div class="medication-row mb-4 p-4 rounded-lg" style="background-color: #ffffff; border: 1px solid #d1d5db;">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium" style="color: #2c3e50;">Medication {{ $loop->iteration }}</span>
                            <button type="button" onclick="this.closest('.medication-row').remove()" 
                                    class="px-3 py-1 rounded text-sm transition" 
                                    style="background-color: #d32f2f; color: #ffffff;">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Medication Name</label>
                                <input type="text" name="medications[{{ $index }}][name]" value="{{ old("medications.$index.name", $prescription->medication_name) }}"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Amoxicillin">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Dosage</label>
                                <input type="text" name="medications[{{ $index }}][dosage]" value="{{ old("medications.$index.dosage", $prescription->dosage) }}"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., 250mg">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Frequency</label>
                                <input type="text" name="medications[{{ $index }}][frequency]" value="{{ old("medications.$index.frequency", $prescription->frequency) }}"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Twice daily">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Duration (days)</label>
                                <input type="number" name="medications[{{ $index }}][duration_days]" value="{{ old("medications.$index.duration_days", $prescription->duration_days) }}" min="1"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="7">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium mb-1" style="color: #5d6d7e;">Instructions</label>
                                <input type="text" name="medications[{{ $index }}][instructions]" value="{{ old("medications.$index.instructions", $prescription->instructions) }}"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:border-blue-500" 
                                       style="border-color: #d1d5db;"
                                       placeholder="e.g., Take with food">
                            </div>
                        </div>
                    </div>
                    @empty
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
                    @endforelse
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t" style="border-color: #e5e7eb;">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.medical-records') }}" class="px-6 py-3 rounded-lg transition font-medium" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                @elseif(Auth::user()->isDoctor())
                    <a href="{{ route('doctor.medical-records') }}" class="px-6 py-3 rounded-lg transition font-medium" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                @else
                    <a href="{{ route('pet-owner.medical-records') }}" class="px-6 py-3 rounded-lg transition font-medium" style="background-color: #95a5a6; color: #ffffff;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                @endif
                <button type="submit" class="px-6 py-3 text-white rounded-lg transition font-medium" style="background-color: #0d5cb6;">
                    <i class="fas fa-save mr-2"></i>Update Medical Record
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Appointment search data
const appointments = @json($appointments);

// Medication management
let medicationIndex = {{ $medicalRecord->prescriptions->count() }};

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

// Appointment search functionality
const appointmentSearchInput = document.getElementById('appointment_search');
const appointmentResultsDiv = document.getElementById('appointment_results');
const appointmentIdInput = document.getElementById('appointment_id');
const selectedAppointmentDisplay = document.getElementById('selected_appointment_display');
const clearAppointmentSelectionBtn = document.getElementById('clear_appointment_selection');

if (appointmentSearchInput) {
    appointmentSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm.length < 1) {
            appointmentResultsDiv.classList.add('hidden');
            return;
        }
        
        // Filter appointments
        const filteredAppointments = appointments.filter(appointment => {
            const petName = appointment.pet.name.toLowerCase();
            const serviceName = appointment.service.name.toLowerCase();
            const appointmentDate = appointment.appointment_date.toLowerCase();
            
            return petName.includes(searchTerm) || 
                   serviceName.includes(searchTerm) || 
                   appointmentDate.includes(searchTerm);
        });
        
        // Display results
        if (filteredAppointments.length > 0) {
            appointmentResultsDiv.innerHTML = filteredAppointments.map(appointment => {
                const date = new Date(appointment.appointment_date);
                const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                
                return `
                    <div class="appointment-result-item p-2 hover:bg-blue-50 cursor-pointer border-b transition" 
                         data-appointment-id="${appointment.id}"
                         data-appointment-pet="${appointment.pet.name}"
                         data-appointment-service="${appointment.service.name}"
                         data-appointment-date="${formattedDate}"
                         data-appointment-time="${formattedTime}"
                         style="border-color: #e5e7eb;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold" style="color: #2c3e50;">
                                    <i class="fas fa-calendar-check mr-2 text-xs" style="color: #3498db;"></i>${appointment.pet.name} - ${appointment.service.name}
                                </p>
                                <p class="text-xs" style="color: #5d6d7e;">
                                    ${formattedDate} at ${formattedTime}
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-xs" style="color: #95a5a6;"></i>
                        </div>
                    </div>
                `;
            }).join('');
            appointmentResultsDiv.classList.remove('hidden');
            
            // Add click handlers
            document.querySelectorAll('.appointment-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectAppointment(
                        this.dataset.appointmentId,
                        this.dataset.appointmentPet,
                        this.dataset.appointmentService,
                        this.dataset.appointmentDate,
                        this.dataset.appointmentTime
                    );
                });
            });
        } else {
            appointmentResultsDiv.innerHTML = `
                <div class="p-4 text-center" style="color: #5d6d7e;">
                    <i class="fas fa-search text-2xl mb-2" style="color: #d1d5db;"></i>
                    <p class="text-sm">No appointments found</p>
                </div>
            `;
            appointmentResultsDiv.classList.remove('hidden');
        }
    });
    
    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!appointmentSearchInput.contains(e.target) && !appointmentResultsDiv.contains(e.target)) {
            appointmentResultsDiv.classList.add('hidden');
        }
    });
}

function selectAppointment(id, petName, serviceName, date, time) {
    appointmentIdInput.value = id;
    appointmentSearchInput.value = '';
    appointmentResultsDiv.classList.add('hidden');
    
    document.getElementById('selected_appointment_name').textContent = `${petName} - ${serviceName}`;
    document.getElementById('selected_appointment_details').textContent = `${date} at ${time}`;
    
    selectedAppointmentDisplay.classList.remove('hidden');
}

if (clearAppointmentSelectionBtn) {
    clearAppointmentSelectionBtn.addEventListener('click', function() {
        appointmentIdInput.value = '';
        selectedAppointmentDisplay.classList.add('hidden');
        appointmentSearchInput.focus();
    });
}
</script>

<style>
button:hover, a:hover {
    opacity: 0.9;
}

.appointment-result-item:last-child {
    border-bottom: none;
}
</style>
@endsection