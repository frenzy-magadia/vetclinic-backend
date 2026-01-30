@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Appointment Details</h1>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.appointments') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        @elseif(Auth::user()->isDoctor())
            <a href="{{ route('doctor.appointments') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        @elseif(Auth::user()->isPetOwner())
            <a href="{{ route('pet-owner.appointments') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        @endif
    </div>

    <!-- Single Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Status Header -->
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Appointment Information</h2>
            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg
                @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                @elseif($appointment->status === 'pending') bg-purple-100 text-purple-800
                @else bg-red-100 text-red-800
                @endif">
                @if($appointment->status === 'scheduled')
                    <i class="fas fa-check-circle"></i>
                @elseif($appointment->status === 'completed')
                    <i class="fas fa-check-double"></i>
                @elseif($appointment->status === 'pending')
                    <i class="fas fa-clock"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Date & Time -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Schedule</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Date</p>
                                <p class="text-base font-semibold text-gray-800">{{ $appointment->appointment_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Time</p>
                                <p class="text-base font-semibold text-gray-800">{{ $appointment->appointment_time }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Service -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Service</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-stethoscope text-teal-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-gray-800">{{ $appointment->service->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Pet Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Pet Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Pet Name</p>
                                <p class="text-base font-semibold text-gray-800">{{ $appointment->pet->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Species</p>
                                <p class="text-base text-gray-700">{{ $appointment->pet->species }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Owner</p>
                                <p class="text-base text-gray-700">{{ $appointment->pet->owner->user->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Doctor</h3>
                        <div>
                            <p class="text-base font-semibold text-gray-800">{{ $appointment->doctor->user->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes (Full Width) -->
            @if($appointment->notes)
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes</h3>
                <p class="text-gray-700 leading-relaxed">{{ $appointment->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection