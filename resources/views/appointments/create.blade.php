@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8 form-card">
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-calendar-plus mr-2"></i>Schedule an Appointment
                </h2>
                <p class="text-sm mt-1">Fill in the details below to book an appointment</p>
            </div>
            @php
                $backRoute = Auth::user()->role === 'doctor' ? 'doctor.appointments' : 
                            (Auth::user()->role === 'pet_owner' ? 'pet-owner.appointments' : 'admin.appointments');
            @endphp
            <a href="{{ route($backRoute) }}" class="px-4 py-2 btn-back rounded-lg transition-all" style="text-decoration: none;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="error-box text-red-700 px-4 py-3 rounded-lg mb-4">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Select Pet with Search -->
            <div class="mb-4">
                <label for="pet_search" class="form-label">
                    <i class="fas fa-paw"></i>Select Pet *
                </label>
                
                <!-- Search Input Container -->
                <div class="relative">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="pet_search" 
                            placeholder="Search pet by name, species, or owner..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc]"
                            autocomplete="off"
                        >
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Results Dropdown -->
                    <div id="pet_results" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <!-- Hidden Select (stores actual value) -->
                <select name="pet_id" id="pet_id" class="hidden" required>
                    <option value="">-- Choose Pet --</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" 
                                data-name="{{ strtolower($pet->name) }}"
                                data-species="{{ strtolower($pet->species) }}"
                                data-owner="{{ strtolower($pet->owner->user->name) }}"
                                {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} ({{ $pet->species }}) - Owner: {{ $pet->owner->user->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Selected Pet Display -->
                <div id="selected_pet" class="hidden mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 text-sm" id="selected_pet_name"></p>
                            <p class="text-xs text-gray-600" id="selected_pet_details"></p>
                        </div>
                        <button type="button" onclick="clearPetSelection()" class="text-red-600 hover:text-red-800 ml-2">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>

                @error('pet_id') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>

            <!-- Select Service -->
            <div class="mb-4">
                <label for="service_id" class="form-label">
                    <i class="fas fa-stethoscope"></i>Select Service *
                </label>
                <select name="service_id" id="service_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Choose Service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
                @error('service_id') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Doctor Info -->
        <div class="doctor-card">
            <label class="form-label">
                <i class="fas fa-user-md"></i>Assigned Doctor
            </label>
            <div class="doctor-info">
                <div class="doctor-avatar">
                    {{ strtoupper(substr($doctor->user->name, 0, 1)) }}
                </div>
                <div>
                    <p style="font-weight: 700; color: var(--charcoal); font-size: 16px;">{{ $doctor->user->name }}</p>
                    @if($doctor->specialization)
                        <p style="font-size: 14px; color: #6b7280; font-weight: 500;">{{ $doctor->specialization }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Source Selection (Admin/Doctor Only) -->
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
        <div class="mb-4">
            <label class="form-label">
                <i class="fas fa-map-marker-alt"></i>Appointment Source *
            </label>
            <div class="flex gap-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="source" value="walk-in" 
                        {{ old('source', request('source')) === 'walk-in' ? 'checked' : '' }}
                        class="form-radio h-5 w-5 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 flex items-center">
                        <i class="fas fa-walking mr-2 text-purple-600"></i>
                        <span class="font-semibold">Walk-in</span>
                        <span class="text-xs text-gray-500 ml-1">(Customer arrived without booking)</span>
                    </span>
                </label>
                
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="source" value="online" 
                        {{ old('source', request('source')) === 'online' || old('source', request('source')) === null ? 'checked' : '' }}
                        class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 flex items-center">
                        <i class="fas fa-laptop mr-2 text-blue-600"></i>
                        <span class="font-semibold">Online</span>
                        <span class="text-xs text-gray-500 ml-1">(Pre-scheduled booking)</span>
                    </span>
                </label>
            </div>
            @error('source') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>
        @endif

        <!-- Appointment Date -->
        <div class="mb-4">
            <label for="appointment_date" class="form-label">
                <i class="fas fa-calendar"></i>Appointment Date *
            </label>
            <input type="date" name="appointment_date" id="appointment_date"
                value="{{ old('appointment_date') }}"
                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            @error('appointment_date') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <!-- Appointment Time -->
        <div class="mb-4">
            <div class="section-header">
                <label class="form-label" style="margin-bottom: 0;">
                    <i class="fas fa-clock"></i>Appointment Time *
                </label>
                <span class="clinic-hours-badge">
                    <i class="fas fa-info-circle"></i> Clinic hours: 8:00 AM - 6:00 PM
                </span>
            </div>
            
            <div id="timeSlotContainer" class="time-slot-grid">
                <div class="empty-state">
                    <i class="fas fa-calendar-day"></i>
                    <p style="font-weight: 500; margin-bottom: 4px;">Please select a date first</p>
                    <p style="font-size: 13px; color: #9ca3af;">Available time slots will appear here</p>
                </div>
            </div>
            <input type="hidden" name="appointment_time" id="appointment_time" required>
            @error('appointment_time') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="form-label">
                <i class="fas fa-sticky-note"></i>Additional Notes <span style="font-weight: 400; color: #6b7280;">(Optional)</span>
            </label>
            <textarea name="notes" id="notes" rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Any additional information about the appointment">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3" style="padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route($backRoute) }}" class="px-6 py-2 btn-back rounded-lg font-medium transition-all" style="text-decoration: none;">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-6 py-2 btn-primary text-white rounded-lg font-medium transition-all">
                <i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
            </button>
        </div>
    </form>
</div>

<script>
// Pet Search Functionality
const petSearchInput = document.getElementById('pet_search');
const petSelect = document.getElementById('pet_id');
const petResultsDiv = document.getElementById('pet_results');
const selectedPetDiv = document.getElementById('selected_pet');
const selectedPetNameEl = document.getElementById('selected_pet_name');
const selectedPetDetailsEl = document.getElementById('selected_pet_details');

// Show all results when focusing on search
petSearchInput.addEventListener('focus', function() {
    if (!petSelect.value) {
        performPetSearch('');
    }
});

// Search as user types
petSearchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    performPetSearch(query);
});

// Perform pet search
function performPetSearch(query) {
    const options = Array.from(petSelect.options).slice(1); // Skip first "Choose Pet"
    let results = options;

    if (query) {
        results = options.filter(option => {
            const name = option.dataset.name || '';
            const species = option.dataset.species || '';
            const owner = option.dataset.owner || '';
            return name.includes(query) || species.includes(query) || owner.includes(query);
        });
    }

    displayPetResults(results);
}

// Display pet search results
function displayPetResults(results) {
    if (results.length === 0) {
        petResultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No pets found</div>';
        petResultsDiv.classList.remove('hidden');
        return;
    }

    petResultsDiv.innerHTML = results.map(option => {
        const fullText = option.text;
        const petInfo = fullText.split(' - Owner: ');
        const petName = petInfo[0];
        const ownerName = petInfo[1] || '';
        
        return `
            <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
                 onclick="selectPet('${option.value}', \`${fullText.replace(/`/g, '\\`')}\`)">
                <p class="font-medium text-gray-900 text-sm">${petName}</p>
                <p class="text-xs text-gray-500 mt-0.5">Owner: ${ownerName}</p>
            </div>
        `;
    }).join('');
    
    petResultsDiv.classList.remove('hidden');
}

// Select a pet
function selectPet(value, fullText) {
    petSelect.value = value;
    const parts = fullText.split(' - Owner: ');
    const petName = parts[0];
    const ownerName = parts[1] || '';
    
    selectedPetNameEl.textContent = petName;
    selectedPetDetailsEl.textContent = `Owner: ${ownerName}`;
    
    petSearchInput.value = '';
    petResultsDiv.classList.add('hidden');
    selectedPetDiv.classList.remove('hidden');
    petSearchInput.parentElement.parentElement.querySelector('.relative').classList.add('hidden');
}

// Clear pet selection
function clearPetSelection() {
    petSelect.value = '';
    selectedPetDiv.classList.add('hidden');
    petSearchInput.parentElement.parentElement.querySelector('.relative').classList.remove('hidden');
    petSearchInput.value = '';
    petSearchInput.focus();
}

// Close results when clicking outside
document.addEventListener('click', function(e) {
    if (!petSearchInput.contains(e.target) && !petResultsDiv.contains(e.target)) {
        petResultsDiv.classList.add('hidden');
    }
});

// If there's an old value (form validation error), show it
@if(old('pet_id'))
    const selectedOption = petSelect.options[petSelect.selectedIndex];
    if (selectedOption.value) {
        selectPet(selectedOption.value, selectedOption.text);
    }
@endif

// Appointment Date and Time Slot Functionality
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    const timeInput = document.getElementById('appointment_time');

    function showNotification(message, isError = false) {
        const notification = document.createElement('div');
        notification.className = 'notification' + (isError ? ' error' : '');
        notification.innerHTML = `<i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i>${message}`;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        
        if (!selectedDate) {
            timeSlotContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-day"></i>
                    <p style="font-weight: 500; margin-bottom: 4px;">Please select a date first</p>
                    <p style="font-size: 13px; color: #9ca3af;">Available time slots will appear here</p>
                </div>
            `;
            return;
        }

        timeSlotContainer.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner"></i>
                <p style="font-weight: 500;">Loading available time slots...</p>
            </div>
        `;

        fetch(`/appointments/available-slots?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotContainer.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>${data.error}</p></div>`;
                    return;
                }

                let html = '';
                data.forEach(slot => {
                    if (slot.available) {
                        html += `
                            <button type="button" class="time-slot-btn" data-time="${slot.time}">
                                <i class="fas fa-clock slot-icon"></i>
                                <span class="slot-time">${slot.display}</span>
                                <span class="slot-status">Available</span>
                            </button>
                        `;
                    } else {
                        html += `
                            <div class="time-slot-disabled">
                                <i class="fas fa-ban" style="color: #ef4444; margin-bottom: 2px;"></i>
                                <span class="slot-time" style="text-decoration: line-through;">${slot.display}</span>
                                <span style="font-size: 11px; color: #ef4444; font-weight: 600;">Booked</span>
                            </div>
                        `;
                    }
                });
                
                timeSlotContainer.innerHTML = html;

                document.querySelectorAll('.time-slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        timeInput.value = this.dataset.time;
                        showNotification('Time slot selected: ' + this.querySelector('.slot-time').textContent);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotContainer.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error loading time slots</p></div>`;
            });
    });

    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        if (!timeInput.value) {
            e.preventDefault();
            showNotification('Please select a time slot', true);
            timeSlotContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>

<style>
/* Appointment create */
:root {
    --navy: #1e3a5f;
    --gold: #d4931d;
    --yellow: #fcd34d;
    --charcoal: #2d3748;
    --blue-primary: #003d82;
    --blue-bright: #0066cc;
}

/* Time Slot Styling */
.time-slot-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    min-height: 200px;
    border: 2px solid #d1d5db;
}

@media (max-width: 768px) {
    .time-slot-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.time-slot-btn {
    position: relative;
    padding: 16px 12px;
    border: 2px solid #0066cc;
    background: white;
    color: var(--charcoal);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.time-slot-btn:hover {
    background: #e3f2fd;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 102, 204, 0.15);
}

.time-slot-btn.selected {
    background: #0066cc !important;
    color: white !important;
    border-color: #0066cc !important;
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
}

.time-slot-btn.selected .slot-icon,
.time-slot-btn.selected .slot-status {
    color: white !important;
}

.time-slot-disabled {
    padding: 16px 12px;
    border: 2px solid #e5e7eb;
    background: #f9fafb;
    color: #9ca3af;
    border-radius: 8px;
    cursor: not-allowed;
    text-align: center;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    opacity: 0.6;
}

.slot-time {
    font-weight: 700;
    font-size: 16px;
}

.slot-status {
    font-size: 11px;
    font-weight: 600;
    color: #0066cc;
}

.slot-icon {
    font-size: 14px;
    color: #0066cc;
    margin-bottom: 2px;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #718096;
}

.empty-state i {
    font-size: 48px;
    color: #93c5fd;
    margin-bottom: 16px;
}

.loading-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #0066cc;
}

.loading-state i {
    font-size: 48px;
    margin-bottom: 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Doctor Card */
.doctor-card {
    background: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.doctor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #1e3a5f;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    margin-right: 16px;
}

.doctor-info {
    display: flex;
    align-items: center;
}

/* Form Enhancements */
.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
}

.form-label i {
    color: #f59e0b;
    margin-right: 8px;
}

.clinic-hours-badge {
    display: inline-block;
    background: #fef3c7;
    color: var(--charcoal);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #f59e0b;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

/* Success Notification */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #0066cc;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0, 102, 204, 0.3);
    z-index: 1000;
    animation: slideIn 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification.error {
    background: #dc2626;
    box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Input Focus Effects */
select:focus,
input:focus,
textarea:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

/* Card styling */
.form-card {
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

/* Header styling */
.page-header {
    background: #2c3e50;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}

.page-header h2 {
    color: white;
}

.page-header h2 i {
    color: #fbbf24;
}

.page-header p {
    color: rgba(255, 255, 255, 0.9);
}

/* Input styling */
input[type="date"],
select,
textarea {
    border: 1px solid #d1d5db;
    transition: all 0.2s;
}

input[type="date"]:hover,
select:hover,
textarea:hover {
    border-color: #9ca3af;
}

/* Button styling */
.btn-back {
    background: #f3f4f6;
    color: var(--charcoal);
    border: 1px solid #d1d5db;
}

.btn-back:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: #0066cc;
    border: none;
}

.btn-primary:hover {
    background: #0052a3;
}

.btn-primary i {
    color: white;
}

/* Error messages */
.error-box {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
}
</style>

@endsection