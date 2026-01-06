@extends('layouts.app')

@section('title', 'Pets Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-[#1e3a5f]">Pets Management</h1>
        <a href="{{ route('pets.create') }}" class="inline-flex items-center px-4 py-2 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition">
            <i class="fas fa-plus mr-2"></i>Add New Pet
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pending Approvals Section -->
    @if($pendingPets->count() > 0)
    <div class="bg-white border-l-4 border-[#d4911e] shadow-lg rounded-lg p-6">
        <button onclick="toggleSection('pendingSection')" class="w-full flex justify-between items-center text-left focus:outline-none hover:opacity-80 transition">
            <h2 class="text-xl font-semibold text-[#1e3a5f]">
                <i class="fas fa-clock mr-2"></i>Pending Pet Registrations ({{ $pendingCount }})
            </h2>
            <i id="pendingIcon" class="fas fa-chevron-down text-[#1e3a5f] transition-transform duration-200 rotate-180"></i>
        </button>
        
        <div id="pendingSection" class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[#2c3e50]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Pet Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Species</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Breed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Registered</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pendingPets as $pet)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $pet->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $pet->species }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->breed ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->owner->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-4">
                                <button 
                                    onclick="viewPendingPet({{ $pet->id }})"
                                    class="text-[#0d47a1] hover:text-[#1565c0] transition"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('pets.approve', $pet->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-[#0d47a1] text-white text-sm font-medium rounded hover:bg-[#1565c0] transition">
                                        Approve
                                    </button>
                                </form>
                                <button 
                                    onclick="openRejectModal({{ $pet->id }})"
                                    class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition"
                                    title="Reject"
                                >
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Main Content Card -->
    <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f]">
        <!-- Search Bar -->
        <div class="p-6 pb-4">
            <form method="GET" action="{{ route('admin.pets') }}" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1]" 
                    placeholder="Search by pet name, species, breed, or owner..." 
                    value="{{ $search ?? '' }}"
                >
                <button type="submit" class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition">
                    <i class="fas fa-search text-sm"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.pets') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times text-sm"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        @if($approvedPets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1e3a5f]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Species</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Breed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Owner</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($approvedPets as $pet)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ ($approvedPets->currentPage() - 1) * $approvedPets->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $pet->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $pet->species }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->breed ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->owner->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <a href="{{ route('pets.show', $pet->id) }}" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('pets.edit', $pet->id) }}" class="text-[#d4911e] hover:text-[#b8860b] transition" title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.pets.destroy', $pet->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this pet?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($approvedPets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $approvedPets->appends(['search' => $search])->links() }}
            </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-paw text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Pets Found</h3>
                <p class="text-gray-500">{{ $search ? 'No pets match your search.' : 'Start by adding a new pet.' }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-[#1e3a5f]">Reject Pet Registration</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    &times;
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-2">Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]" placeholder="Please provide a reason..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const icon = document.getElementById(sectionId.replace('Section', 'Icon'));
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.classList.add('rotate-180');
        localStorage.setItem(sectionId + '_expanded', 'true');
    } else {
        section.classList.add('hidden');
        icon.classList.remove('rotate-180');
        localStorage.setItem(sectionId + '_expanded', 'false');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('pendingSection_expanded') === 'true') {
        const pendingSection = document.getElementById('pendingSection');
        const pendingIcon = document.getElementById('pendingIcon');
        if (pendingSection) {
            pendingSection.classList.remove('hidden');
            pendingIcon.classList.add('rotate-180');
        }
    }
});

function openRejectModal(petId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/pets/${petId}/reject`;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function viewPendingPet(petId) {
    window.location.href = `/pets/${petId}`;
}

// Close modal when clicking outside
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>

<style>
.fas {
    font-size: 1.125rem;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endsection