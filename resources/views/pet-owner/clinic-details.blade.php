@extends('layouts.app')

@section('title', 'Clinic Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-[#1e3a5f] shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-white flex items-center">
            <i class="fas fa-hospital-alt mr-3"></i>Clinic Information
        </h1>
        <p class="text-gray-100 mt-1">Contact us and visit our clinic</p>
    </div>

    <!-- Clinic Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Main Information -->
        <div class="bg-white shadow-lg rounded-lg p-6 border-2 border-gray-200">
            <h2 class="text-xl font-bold text-[#1e3a5f] mb-6 flex items-center pb-4 border-b-2 border-gray-200">
                <i class="fas fa-paw text-[#d4931d] mr-2"></i>{{ $clinicDetails->clinic_name }}
            </h2>
            
            <div class="space-y-6">
                <!-- Phone -->
                @if($clinicDetails->phone)
                <div class="flex items-start hover:bg-blue-50 p-3 rounded-lg transition-colors">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-[#1e3a5f] shadow-md">
                            <i class="fas fa-phone text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Phone Number</h3>
                        <p class="mt-2 text-lg font-semibold text-gray-900">
                            <a href="tel:{{ str_replace('-', '', $clinicDetails->phone) }}" class="text-[#1e3a5f] hover:text-[#d4931d] transition-colors">{{ $clinicDetails->phone }}</a>
                        </p>
                    </div>
                </div>
                @endif

                <!-- Email -->
                @if($clinicDetails->email)
                <div class="flex items-start hover:bg-green-50 p-3 rounded-lg transition-colors">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-[#d4931d] shadow-md">
                            <i class="fas fa-envelope text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Email Address</h3>
                        <p class="mt-2 text-lg font-semibold text-gray-900">
                            <a href="mailto:{{ $clinicDetails->email }}" class="text-[#1e3a5f] hover:text-[#d4931d] transition-colors">{{ $clinicDetails->email }}</a>
                        </p>
                    </div>
                </div>
                @endif

                <!-- Address -->
                @if($clinicDetails->address)
                <div class="flex items-start hover:bg-yellow-50 p-3 rounded-lg transition-colors">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-[#fdb913] shadow-md">
                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Address</h3>
                        <p class="mt-2 text-base font-medium text-gray-900">
                            {{ $clinicDetails->address }}
                        </p>
                    </div>
                </div>
                @endif

                <!-- Facebook -->
                @if($clinicDetails->facebook)
                <div class="flex items-start hover:bg-blue-50 p-3 rounded-lg transition-colors">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-[#1e3a5f] shadow-md">
                            <i class="fab fa-facebook-f text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Follow Us on Facebook</h3>
                        <p class="mt-2 text-lg font-semibold text-[#1e3a5f]">
                            {{ $clinicDetails->facebook }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Business Hours Card -->
        <div class="bg-blue-50 shadow-lg rounded-lg p-6 border-2 border-blue-200">
            <h2 class="text-xl font-bold text-[#1e3a5f] mb-6 flex items-center pb-4 border-b-2 border-blue-300">
                <i class="fas fa-clock mr-2 text-[#d4931d]"></i>Business Hours
            </h2>
            
            <div class="space-y-4">
                @php
                    $hours = $clinicDetails->business_hours;
                @endphp

                <!-- Weekdays -->
                @if(isset($hours['weekdays']))
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border-2 border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold flex items-center">
                            <i class="fas fa-calendar-week text-[#1e3a5f] mr-2"></i>{{ $hours['weekdays']['label'] ?? 'Monday - Friday' }}
                        </span>
                        <span class="text-gray-900 font-semibold">
                            {{ date('g:i A', strtotime($hours['weekdays']['start'])) }} - {{ date('g:i A', strtotime($hours['weekdays']['end'])) }}
                        </span>
                    </div>
                </div>
                @endif
                
                <!-- Saturday -->
                @if(isset($hours['saturday']))
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border-2 border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold flex items-center">
                            <i class="fas fa-calendar-day text-[#d4931d] mr-2"></i>{{ $hours['saturday']['label'] ?? 'Saturday' }}
                        </span>
                        <span class="text-gray-900 font-semibold">
                            {{ date('g:i A', strtotime($hours['saturday']['start'])) }} - {{ date('g:i A', strtotime($hours['saturday']['end'])) }}
                        </span>
                    </div>
                </div>
                @endif
                
                <!-- Sunday -->
                @if(isset($hours['sunday']))
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border-2 border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold flex items-center">
                            <i class="fas fa-calendar text-[#fdb913] mr-2"></i>{{ $hours['sunday']['label'] ?? 'Sunday' }}
                        </span>
                        <span class="text-gray-900 font-semibold">
                            {{ date('g:i A', strtotime($hours['sunday']['start'])) }} - {{ date('g:i A', strtotime($hours['sunday']['end'])) }}
                        </span>
                    </div>
                </div>
                @endif
                
                <!-- Emergency -->
                @if(isset($hours['emergency']))
                <div class="bg-red-50 rounded-lg p-4 shadow-md border-2 border-red-300">
                    <div class="flex justify-between items-center">
                        <span class="text-red-700 font-bold flex items-center">
                            <i class="fas fa-ambulance text-red-600 mr-2 animate-pulse"></i>Emergency
                        </span>
                        <span class="text-red-700 font-bold text-lg">{{ $hours['emergency'] }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Additional Info -->
            <div class="mt-6 bg-[#fdb913] rounded-lg p-4 shadow-md border-2 border-[#d4931d]">
                <p class="text-gray-900 font-semibold text-center flex items-center justify-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Walk-ins welcome during business hours
                </p>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    @if($services->count() > 0)
    <div class="bg-white shadow-lg rounded-lg p-6 border-2 border-gray-200">
        <h2 class="text-2xl font-bold text-[#1e3a5f] mb-6 flex items-center pb-4 border-b-2 border-gray-200">
            <i class="fas fa-concierge-bell text-[#d4931d] mr-3"></i>Our Services
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $index => $service)
            <div class="bg-gradient-to-br {{ $index % 2 == 0 ? 'from-blue-50 to-blue-100' : 'from-yellow-50 to-yellow-100' }} rounded-lg p-4 border-l-4 hover:shadow-lg transition-shadow" style="border-color: {{ $index % 2 == 0 ? '#d4931d' : '#fdb913' }};">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $index % 2 == 0 ? '#d4931d' : '#fdb913' }};">
                        <i class="{{ $service->icon }} text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-[#1e3a5f]">{{ $service->name }}</h3>
                </div>
                @if($service->description)
                <p class="text-sm text-gray-600">{{ $service->description }}</p>
                @endif
                <p class="text-base font-bold mt-2" style="color: {{ $index % 2 == 0 ? '#d4931d' : '#fdb913' }};">{{ $service->price_range }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Location/Map Info -->
    @if($clinicDetails->address)
    <div class="bg-white shadow-lg rounded-lg p-6 border-2 border-gray-200">
        <h2 class="text-xl font-bold text-[#1e3a5f] mb-4 flex items-center pb-4 border-b-2 border-gray-200">
            <i class="fas fa-map-marked-alt text-[#d4931d] mr-2"></i>Visit Us
        </h2>
        
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-4">
            <p class="text-gray-700 text-base leading-relaxed">
                We're conveniently located at <span class="font-bold text-[#1e3a5f]">{{ $clinicDetails->address }}</span>. Our modern facilities and experienced team are ready to provide the best care for your pets.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border-2 border-green-200 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-check-circle text-3xl text-green-600 mb-2"></i>
                <h3 class="font-bold text-gray-900">Modern Facilities</h3>
                <p class="text-sm text-gray-600 mt-1">State-of-the-art equipment</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border-2 border-blue-200 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-user-md text-3xl text-[#1e3a5f] mb-2"></i>
                <h3 class="font-bold text-gray-900">Expert Veterinarians</h3>
                <p class="text-sm text-gray-600 mt-1">Experienced and caring staff</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border-2 border-yellow-200 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-heart text-3xl text-[#d4931d] mb-2"></i>
                <h3 class="font-bold text-gray-900">Compassionate Care</h3>
                <p class="text-sm text-gray-600 mt-1">Your pet's health is our priority</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-[#1e3a5f] to-[#2b5a8e] shadow-lg rounded-lg p-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">Ready to Book an Appointment?</h2>
        <p class="text-lg text-blue-100 mb-6">Schedule your pet's next visit today!</p>
        <a href="{{ route('pet-owner.appointments') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-lg transition hover:opacity-90 shadow-lg" style="background-color: #d4931d; color: white;">
            <i class="fas fa-calendar-check"></i>
            <span>Book Appointment</span>
        </a>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection