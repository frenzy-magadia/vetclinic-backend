@extends('layouts.app')

@section('title', 'Pet Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#2d3748]">
            <i class="fas fa-info-circle text-[#fcd34d] mr-2"></i>Pet Details
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('pet-owner.pets') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
       <!-- Header -->
        <div class="bg-[#1e3a5f] px-8 py-8">
            <div>
                <h2 class="text-4xl font-bold mb-2 text-white">
                    <i class="fas fa-paw text-[#fcd34d] mr-3"></i>{{ $pet->name }}
                </h2>
                <p class="text-gray-200 text-lg">{{ ucfirst($pet->species) }} - {{ $pet->breed ?? 'Mixed' }}</p>
                
                <!-- Approval Status Badge -->
                <div class="mt-3">
                    @if($pet->approval_status === 'approved')
                        <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm">
                            <i class="fas fa-check-circle mr-1"></i>Approved
                        </span>
                    @elseif($pet->approval_status === 'pending')
                        <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-sm">
                            <i class="fas fa-clock mr-1"></i>Pending Approval
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm">
                            <i class="fas fa-times-circle mr-1"></i>Rejected
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pet Information -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-[#2d3748]">
                        <i class="fas fa-clipboard-list text-[#fcd34d] mr-2"></i>Basic Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-list text-[#d4931d] mr-1"></i>Species
                            </label>
                            <p class="font-medium">{{ ucfirst($pet->species) }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-dog text-[#fcd34d] mr-1"></i>Breed
                            </label>
                            <p class="font-medium">{{ $pet->breed ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-birthday-cake text-[#d4931d] mr-1"></i>Age
                            </label>
                            <p class="font-medium">{{ $pet->age }} years old</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-venus-mars text-[#fcd34d] mr-1"></i>Gender
                            </label>
                            <p class="font-medium">{{ ucfirst($pet->gender) }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-[#2d3748]">
                        <i class="fas fa-ruler text-[#fcd34d] mr-2"></i>Physical Details
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-weight text-[#d4931d] mr-1"></i>Weight
                            </label>
                            <p class="font-medium">{{ $pet->weight ? $pet->weight . ' kg' : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-palette text-[#fcd34d] mr-1"></i>Color
                            </label>
                            <p class="font-medium">{{ $pet->color ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">
                                <i class="fas fa-calendar text-[#d4931d] mr-1"></i>Registration Date
                            </label>
                            <p class="font-medium">{{ $pet->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Notes -->
            @if($pet->medical_notes)
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-[#2d3748]">
                    <i class="fas fa-notes-medical text-[#fcd34d] mr-2"></i>Medical Notes
                </h3>
                <div class="bg-[#fcd34d] bg-opacity-20 border border-[#fcd34d] p-4 rounded-lg">
                    <p class="text-gray-700">{{ $pet->medical_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Appointments History -->
            @if($pet->appointments && $pet->appointments->count() > 0)
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4 text-[#2d3748]">
                    <i class="fas fa-calendar-check text-[#fcd34d] mr-2"></i>Recent Appointments
                </h3>
                <div class="space-y-2">
                    @foreach($pet->appointments->take(5) as $appointment)
                    <div class="bg-gray-50 p-3 rounded flex justify-between items-center hover:bg-gray-100 transition">
                        <div>
                            <p class="font-medium text-[#2d3748]">{{ $appointment->service->name ?? 'General Checkup' }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('M d, Y') }} - Dr. {{ $appointment->doctor->user->name }}</p>
                        </div>
                        <span class="px-3 py-1 rounded text-xs font-semibold
                            @if($appointment->status === 'completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($appointment->status === 'scheduled') bg-[#0066cc] bg-opacity-20 text-[#003d82] border border-[#0066cc]
                            @else bg-[#fcd34d] bg-opacity-30 text-[#2d3748] border border-[#d4931d]
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Medical Records Section -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-[#1e3a5f] px-6 py-4">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-file-medical text-[#fcd34d] mr-2"></i>Medical Records
            </h3>
        </div>

        <div class="p-6">
            @if(isset($medicalRecords) && $medicalRecords->count() > 0)
                <div class="space-y-4">
                    @foreach($medicalRecords as $record)
                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">
                                    <i class="fas fa-calendar-alt text-[#fcd34d] mr-2"></i>{{ $record->created_at->format('M d, Y') }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user-md text-[#d4931d] mr-1"></i>Dr. {{ $record->doctor->user->name }}
                                </p>
                            </div>
                            <a href="{{ route('medical-records.show', $record->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    <i class="fas fa-diagnoses text-[#d4931d] mr-1"></i>Diagnosis
                                </label>
                                <p class="text-sm text-gray-800">{{ Str::limit($record->diagnosis, 100) }}</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    <i class="fas fa-prescription text-[#d4931d] mr-1"></i>Treatment
                                </label>
                                <p class="text-sm text-gray-800">{{ Str::limit($record->treatment, 100) }}</p>
                            </div>
                        </div>

                        @if($record->prescriptions->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                <i class="fas fa-pills text-[#fcd34d] mr-1"></i>Medications
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($record->prescriptions as $prescription)
                                <span class="px-3 py-1 bg-[#fcd34d] bg-opacity-30 text-[#2d3748] text-xs rounded-full border border-[#d4931d]">
                                    <i class="fas fa-capsules mr-1"></i>{{ $prescription->medication_name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $medicalRecords->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-medical text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 text-lg">No medical records yet</p>
                    <p class="text-gray-500 text-sm mt-2">Medical records will appear here after your pet's appointments</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection