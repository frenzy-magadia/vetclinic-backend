@extends('layouts.app')

@section('title', 'Pet Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold" style="color: #1e3a5f;">
                <i class="fas fa-paw mr-3" style="color: #0d47a1;"></i>{{ $pet->name }}
            </h1>
            <p class="text-gray-600 mt-2">Pet Details and Medical History</p>
        </div>
        <div class="flex gap-3">
            @if(!Auth::user()->isAdmin())
            <a href="{{ route('pets.edit', $pet->id) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-medium shadow-md hover:shadow-lg transition-all" style="background-color: #0d47a1;">
                <i class="fas fa-edit mr-2"></i>
                Edit Pet
            </a>
            @endif
            <a href="{{ Auth::user()->isAdmin() ? route('admin.pets') : route('pet-owner.pets') }}" class="inline-flex items-center px-4 py-2 border-2 rounded-lg font-medium hover:bg-gray-50 transition-all" style="border-color: #1e3a5f; color: #1e3a5f;">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Approval Status Badge -->
    @if($pet->approval_status !== 'approved')
    <div class="mb-8 p-5 rounded-xl shadow-lg border-l-4 
        @if($pet->approval_status === 'pending') bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-500
        @else bg-gradient-to-r from-red-50 to-red-100 border-red-500 @endif">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                @if($pet->approval_status === 'pending') bg-yellow-200
                @else bg-red-200 @endif">
                <i class="fas fa-{{ $pet->approval_status === 'pending' ? 'clock' : 'times-circle' }} 
                    @if($pet->approval_status === 'pending') text-yellow-700
                    @else text-red-700 @endif text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="font-bold text-lg
                    @if($pet->approval_status === 'pending') text-yellow-900
                    @else text-red-900 @endif">
                    Status: {{ ucfirst($pet->approval_status) }}
                </p>
                @if($pet->approval_status === 'rejected' && $pet->rejection_reason)
                <p class="text-sm mt-1 text-red-800 font-medium">
                    <i class="fas fa-info-circle mr-1"></i>Reason: {{ $pet->rejection_reason }}
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Pet Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex justify-center mb-6">
                        <div class="w-28 h-28 rounded-full flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <i class="fas fa-paw text-5xl" style="color: #0d47a1;"></i>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-signature" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Name</p>
                                <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->name }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-paw" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Species</p>
                                <p class="text-base font-bold capitalize" style="color: #1e3a5f;">{{ $pet->species }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-dna" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Breed</p>
                                <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->breed ?? 'Not specified' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-birthday-cake" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Age</p>
                                <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->age }} {{ $pet->age == 1 ? 'year' : 'years' }} old</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-venus-mars" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Gender</p>
                                <p class="text-base font-bold capitalize" style="color: #1e3a5f;">{{ $pet->gender }}</p>
                            </div>
                        </div>
                        
                        @if($pet->weight)
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-weight" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Weight</p>
                                <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->weight }} kg</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($pet->color)
                        <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-palette" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Color</p>
                                <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->color }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Owner Information -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Owner Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                            <i class="fas fa-user-circle" style="color: #0d47a1;"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Owner Name</p>
                            <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->owner->user->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                            <i class="fas fa-envelope" style="color: #0d47a1;"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Email</p>
                            <p class="text-sm font-medium text-gray-700 break-all">{{ $pet->owner->user->email }}</p>
                        </div>
                    </div>
                    
                    @if($pet->owner->user->phone)
                    <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                            <i class="fas fa-phone" style="color: #0d47a1;"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Phone</p>
                            <p class="text-base font-bold" style="color: #1e3a5f;">{{ $pet->owner->user->phone }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Medical Records & Appointments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Medical Notes -->
            @if($pet->medical_notes)
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-notes-medical mr-2"></i>
                        Medical Notes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="p-4 rounded-lg" style="background-color: #f8f9fa; border-left: 4px solid #0d47a1;">
                        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $pet->medical_notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                <i class="fas fa-calendar-check text-3xl" style="color: #0d47a1;"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Appointments</p>
                                <p class="text-3xl font-bold mt-1" style="color: #1e3a5f;">{{ $pet->appointments->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #c8e6c9 0%, #a5d6a7 100%);">
                                <i class="fas fa-file-medical text-3xl text-green-700"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Medical Records</p>
                                <p class="text-3xl font-bold mt-1" style="color: #1e3a5f;">{{ $pet->medicalRecords->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Records -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5 flex justify-between items-center" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-file-medical-alt mr-2"></i>
                        Medical Records
                    </h2>
                    @if(Auth::user()->isDoctor() && $pet->approval_status === 'approved')
                    <a href="{{ route('medical-records.create', ['pet_id' => $pet->id]) }}" class="inline-flex items-center px-4 py-2 text-sm bg-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all" style="color: #0d47a1;">
                        <i class="fas fa-plus mr-2"></i>
                        Add Record
                    </a>
                    @endif
                </div>
                <div class="p-6">
                    @if($pet->medicalRecords->count() > 0)
                    <div class="space-y-4">
                        @foreach($pet->medicalRecords as $record)
                        <div class="border-2 rounded-xl p-5 hover:shadow-lg transition-all" style="border-color: #e3f2fd; background-color: #fafafa;">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="font-bold text-lg" style="color: #1e3a5f;">
                                        <i class="fas fa-calendar-day mr-2" style="color: #0d47a1;"></i>
                                        {{ $record->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1 flex items-center">
                                        <i class="fas fa-user-md mr-2" style="color: #0d47a1;"></i>
                                        Dr. {{ $record->doctor->user->name }}
                                    </p>
                                </div>
                                <a href="{{ route('medical-records.show', $record->id) }}" class="inline-flex items-center px-4 py-2 text-sm rounded-lg font-semibold shadow-md hover:shadow-lg transition-all text-white" style="background-color: #0d47a1;">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 rounded-lg" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
                                    <p class="text-xs font-bold text-gray-700 uppercase mb-2">
                                        <i class="fas fa-diagnoses mr-1"></i>Diagnosis
                                    </p>
                                    <p class="text-sm text-gray-800 leading-relaxed">{{ Str::limit($record->diagnosis, 120) }}</p>
                                </div>
                                <div class="p-4 rounded-lg" style="background-color: #d1ecf1; border-left: 4px solid #17a2b8;">
                                    <p class="text-xs font-bold text-gray-700 uppercase mb-2">
                                        <i class="fas fa-pills mr-1"></i>Treatment
                                    </p>
                                    <p class="text-sm text-gray-800 leading-relaxed">{{ Str::limit($record->treatment, 120) }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <i class="fas fa-file-medical text-5xl" style="color: #0d47a1;"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-700 mb-2">No medical records yet</p>
                        <p class="text-gray-500 mb-4">Medical history will appear here once records are created</p>
                        @if(Auth::user()->isDoctor() && $pet->approval_status === 'approved')
                        <a href="{{ route('medical-records.create', ['pet_id' => $pet->id]) }}" class="inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold shadow-lg hover:shadow-xl transition-all" style="background-color: #0d47a1;">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Record
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Appointments -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Appointment History
                    </h2>
                </div>
                <div class="p-6">
                    @if($pet->appointments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">Service</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">Doctor</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($pet->appointments as $appointment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold" style="color: #1e3a5f;">
                                        <i class="fas fa-calendar mr-2" style="color: #0d47a1;"></i>
                                        {{ $appointment->appointment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $appointment->service->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <i class="fas fa-user-md mr-1" style="color: #0d47a1;"></i>
                                        {{ $appointment->doctor->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1.5 text-xs font-bold rounded-full shadow-sm
                                            @if($appointment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <i class="fas fa-calendar text-5xl" style="color: #0d47a1;"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-700 mb-2">No appointments yet</p>
                        <p class="text-gray-500">Appointment history will appear here</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection