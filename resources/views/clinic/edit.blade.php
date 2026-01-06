@extends('layouts.app')

@section('title', 'Clinic Settings')

@section('content')
<div class="container mx-auto px-4 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#2d3748]">
            <i class="fas fa-hospital-alt mr-2"></i>Clinic Settings
        </h1>
        <p class="text-gray-600 mt-1">Manage your clinic information and business hours</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-6">
        <form method="POST" action="{{ route('clinic.update') }}">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-[#1e3a5f] mb-4 pb-2 border-b-2 border-gray-200">
                    <i class="fas fa-info-circle mr-2"></i>Basic Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="clinic_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Clinic Name *
                        </label>
                        <input 
                            type="text" 
                            name="clinic_name" 
                            id="clinic_name" 
                            value="{{ old('clinic_name', $clinicDetails->clinic_name) }}" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] @error('clinic_name') border-red-500 @enderror"
                        >
                        @error('clinic_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone mr-1"></i>Phone Number
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                id="phone" 
                                value="{{ old('phone', $clinicDetails->phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-1"></i>Email Address
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email', $clinicDetails->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>Address
                        </label>
                        <textarea 
                            name="address" 
                            id="address" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                        >{{ old('address', $clinicDetails->address) }}</textarea>
                    </div>

                    <div>
                        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-facebook mr-1"></i>Facebook Page Name
                        </label>
                        <input 
                            type="text" 
                            name="facebook" 
                            id="facebook" 
                            value="{{ old('facebook', $clinicDetails->facebook) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                        >
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-[#1e3a5f] mb-4 pb-2 border-b-2 border-gray-200">
                    <i class="fas fa-clock mr-2"></i>Business Hours
                </h2>

                <div class="space-y-4">
                    <!-- Weekdays -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-calendar-week mr-1"></i>Monday - Friday
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                                <input 
                                    type="time" 
                                    name="weekdays_start" 
                                    value="{{ old('weekdays_start', $clinicDetails->business_hours['weekdays']['start'] ?? '08:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">End Time</label>
                                <input 
                                    type="time" 
                                    name="weekdays_end" 
                                    value="{{ old('weekdays_end', $clinicDetails->business_hours['weekdays']['end'] ?? '18:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Saturday -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-calendar-day mr-1"></i>Saturday
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                                <input 
                                    type="time" 
                                    name="saturday_start" 
                                    value="{{ old('saturday_start', $clinicDetails->business_hours['saturday']['start'] ?? '09:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">End Time</label>
                                <input 
                                    type="time" 
                                    name="saturday_end" 
                                    value="{{ old('saturday_end', $clinicDetails->business_hours['saturday']['end'] ?? '16:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Sunday -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-calendar mr-1"></i>Sunday
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                                <input 
                                    type="time" 
                                    name="sunday_start" 
                                    value="{{ old('sunday_start', $clinicDetails->business_hours['sunday']['start'] ?? '10:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">End Time</label>
                                <input 
                                    type="time" 
                                    name="sunday_end" 
                                    value="{{ old('sunday_end', $clinicDetails->business_hours['sunday']['end'] ?? '14:00') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Emergency -->
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <label for="emergency" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-ambulance mr-1"></i>Emergency Availability
                        </label>
                        <input 
                            type="text" 
                            name="emergency" 
                            id="emergency" 
                            value="{{ old('emergency', $clinicDetails->business_hours['emergency'] ?? '24/7 Available') }}"
                            placeholder="e.g., 24/7 Available"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                        >
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('clinic.services') }}" class="text-[#0066cc] hover:text-[#003d82] font-medium">
                    <i class="fas fa-concierge-bell mr-2"></i>Manage Services
                </a>
                <button type="submit" class="px-6 py-3 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection