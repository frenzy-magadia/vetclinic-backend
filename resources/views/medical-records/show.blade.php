@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold flex items-center" style="color: #1e3a5f;">
                <i class="fas fa-file-medical-alt mr-3" style="color: #0d47a1;"></i>
                Medical Record Details
            </h1>
            <div class="mt-3 flex items-center gap-4">
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold" style="background-color: #e3f2fd; color: #0d47a1;">
                    <i class="fas fa-paw mr-2"></i>
                    {{ $medicalRecord->pet->name }}
                </span>
                <span class="text-gray-600 flex items-center">
                    <i class="fas fa-calendar mr-2" style="color: #0d47a1;"></i>
                    {{ $medicalRecord->created_at->format('M d, Y') }}
                </span>
            </div>
        </div>
        <div class="flex gap-3">
            @if(Auth::user()->isDoctor() && $medicalRecord->doctor_id === Auth::user()->doctor->id)
            <a href="{{ route('medical-records.edit', $medicalRecord->id) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-medium shadow-md hover:shadow-lg transition-all" style="background-color: #0d47a1;">
                <i class="fas fa-edit mr-2"></i>
                Edit Record
            </a>
            @endif
            <a href="{{ route('pets.show', $medicalRecord->pet_id) }}" class="inline-flex items-center px-4 py-2 border-2 rounded-lg font-medium hover:bg-gray-50 transition-all" style="border-color: #1e3a5f; color: #1e3a5f;">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Pet Details
            </a>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-paw mr-2"></i>
                Patient Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-start p-4 rounded-xl hover:shadow-md transition-all" style="background-color: #f8f9fa;">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                        <i class="fas fa-signature" style="color: #0d47a1;"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Pet Name</p>
                        <p class="text-base font-bold mt-1" style="color: #1e3a5f;">{{ $medicalRecord->pet->name }}</p>
                    </div>
                </div>
                <div class="flex items-start p-4 rounded-xl hover:shadow-md transition-all" style="background-color: #f8f9fa;">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                        <i class="fas fa-paw" style="color: #0d47a1;"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Species</p>
                        <p class="text-base font-bold mt-1 capitalize" style="color: #1e3a5f;">{{ $medicalRecord->pet->species ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-start p-4 rounded-xl hover:shadow-md transition-all" style="background-color: #f8f9fa;">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                        <i class="fas fa-dna" style="color: #0d47a1;"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Breed</p>
                        <p class="text-base font-bold mt-1" style="color: #1e3a5f;">{{ $medicalRecord->pet->breed ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-start p-4 rounded-xl hover:shadow-md transition-all" style="background-color: #f8f9fa;">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                        <i class="fas fa-user-circle" style="color: #0d47a1;"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Owner</p>
                        <p class="text-base font-bold mt-1" style="color: #1e3a5f;">{{ $medicalRecord->pet->owner->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosis and Treatment -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-diagnoses mr-2"></i>
                    Diagnosis
                </h2>
            </div>
            <div class="p-6">
                <div class="p-5 rounded-xl leading-relaxed" style="background-color: #fff9e6; border-left: 4px solid #ffc107; color: #1e3a5f;">
                    {{ $medicalRecord->diagnosis }}
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-pills mr-2"></i>
                    Treatment
                </h2>
            </div>
            <div class="p-6">
                <div class="p-5 rounded-xl leading-relaxed" style="background-color: #e8f5f7; border-left: 4px solid #17a2b8; color: #1e3a5f;">
                    {{ $medicalRecord->treatment }}
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription -->
    @if($medicalRecord->prescription)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-prescription mr-2"></i>
                General Prescription Notes
            </h2>
        </div>
        <div class="p-6">
            <div class="p-5 rounded-xl leading-relaxed whitespace-pre-wrap" style="background-color: #f0f4f8; border-left: 4px solid #0d47a1; color: #1e3a5f;">
                {{ $medicalRecord->prescription }}
            </div>
        </div>
    </div>
    @endif

    <!-- Medications -->
    @if($medicalRecord->prescriptions->count() > 0)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-capsules mr-2"></i>
                Medications
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">
                                <i class="fas fa-pills mr-1" style="color: #0d47a1;"></i>
                                Medication
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">
                                <i class="fas fa-syringe mr-1" style="color: #0d47a1;"></i>
                                Dosage
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">
                                <i class="fas fa-clock mr-1" style="color: #0d47a1;"></i>
                                Frequency
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">
                                <i class="fas fa-calendar-day mr-1" style="color: #0d47a1;"></i>
                                Duration
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1e3a5f;">
                                <i class="fas fa-info-circle mr-1" style="color: #0d47a1;"></i>
                                Instructions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($medicalRecord->prescriptions as $prescription)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold" style="color: #1e3a5f;">{{ $prescription->medication_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->dosage ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->frequency ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #e3f2fd; color: #0d47a1;">
                                    {{ $prescription->duration_days ?? '-' }} days
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->instructions ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Follow-up Information -->
    @if($medicalRecord->followUpSchedules->count() > 0)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #6f42c1 0%, #563d7c 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-calendar-check mr-2"></i>
                Follow-up Schedule
            </h2>
        </div>
        <div class="p-6 space-y-4">
            @foreach($medicalRecord->followUpSchedules as $followUp)
            <div class="border-2 rounded-xl p-5 transition-all hover:shadow-lg" style="border-color: #e3f2fd; background-color: #fafafa;">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Scheduled Date</p>
                        <p class="text-lg font-bold flex items-center" style="color: #1e3a5f;">
                            <i class="fas fa-calendar-alt mr-2" style="color: #0d47a1;"></i>
                            {{ $followUp->scheduled_date->format('M d, Y') }}
                        </p>
                    </div>
                    <span class="px-4 py-2 text-sm font-bold rounded-full shadow-sm
                        @if($followUp->status === 'pending') text-yellow-800" style="background-color: #fff3cd;
                        @elseif($followUp->status === 'completed') text-green-800" style="background-color: #d4edda;
                        @else text-gray-800" style="background-color: #e2e3e5; @endif">
                        <i class="fas fa-{{ $followUp->status === 'pending' ? 'clock' : ($followUp->status === 'completed' ? 'check-circle' : 'circle') }} mr-1"></i>
                        {{ ucfirst($followUp->status) }}
                    </span>
                </div>
                @if($followUp->notes)
                <div class="mt-4 p-4 rounded-lg" style="background-color: #f8f9fa; border-left: 4px solid #0d47a1;">
                    <p class="text-xs font-bold text-gray-700 uppercase mb-2">
                        <i class="fas fa-sticky-note mr-1" style="color: #0d47a1;"></i>
                        Notes
                    </p>
                    <p class="text-gray-700 leading-relaxed">{{ $followUp->notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Appointment Link -->
    @if($medicalRecord->appointment)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-calendar-day mr-2"></i>
                Related Appointment
            </h2>
        </div>
        <div class="p-6">
            <div class="p-5 rounded-xl" style="background-color: #f8f9fa; border-left: 4px solid #0d47a1;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">
                            <i class="fas fa-briefcase-medical mr-1" style="color: #0d47a1;"></i>
                            Service
                        </p>
                        <p class="text-base font-bold" style="color: #1e3a5f;">{{ $medicalRecord->appointment->service->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">
                            <i class="fas fa-calendar-alt mr-1" style="color: #0d47a1;"></i>
                            Appointment Date
                        </p>
                        <p class="text-base font-bold" style="color: #1e3a5f;">{{ $medicalRecord->appointment->appointment_date->format('M d, Y - h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Documents -->
    @if($medicalRecord->documents->count() > 0)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1e3a5f 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-paperclip mr-2"></i>
                Attached Documents
            </h2>
        </div>
        <div class="p-6 space-y-3">
            @foreach($medicalRecord->documents as $document)
            <div class="flex justify-between items-center p-4 border-2 rounded-xl hover:shadow-lg transition-all" style="border-color: #e3f2fd; background-color: #fafafa;">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #e3f2fd;">
                        <i class="fas fa-file-alt text-xl" style="color: #0d47a1;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold" style="color: #1e3a5f;">{{ $document->file_name }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $document->description }}</p>
                    </div>
                </div>
                <a href="{{ route('documents.download', $document->id) }}" class="flex-shrink-0 inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all" style="background-color: #0d47a1;">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Delete Button - Only for Doctors who created the record -->
    @if(Auth::user()->isDoctor() && $medicalRecord->doctor_id === Auth::user()->doctor->id)
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border-2 border-red-200">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Danger Zone
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between p-4 rounded-xl" style="background-color: #fff5f5;">
                <div>
                    <p class="font-bold text-red-800 mb-1">Delete Medical Record</p>
                    <p class="text-sm text-red-600">Once you delete this record, there is no going back. Please be certain.</p>
                </div>
                <form method="POST" action="{{ route('medical-records.destroy', $medicalRecord->id) }}" onsubmit="return confirm('Are you sure you want to delete this medical record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-semibold shadow-md hover:shadow-lg transition-all whitespace-nowrap ml-4" style="background-color: #dc3545;">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection