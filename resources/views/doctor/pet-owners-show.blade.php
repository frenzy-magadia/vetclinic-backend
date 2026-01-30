@extends('layouts.app')

@section('title', 'Pet Owner Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #2c3e50;">
            <i class="fas fa-user mr-2" style="color: #f4d03f;"></i>Pet Owner Details
        </h1>
        <a href="{{ route('doctor.pet-owners') }}" 
           class="px-4 py-2 rounded-lg transition" 
           style="background-color: #95a5a6; color: #ffffff;">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Owner Information Card -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4" style="background-color: #34495e;">
            <h2 class="text-xl font-bold text-white">
                <i class="fas fa-info-circle mr-2" style="color: #f4d03f;"></i>Owner Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Name</label>
                    <p class="text-lg font-semibold" style="color: #2c3e50;">{{ $petOwner->user->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Email</label>
                    <p class="text-lg" style="color: #2c3e50;">{{ $petOwner->user->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Phone</label>
                    <p class="text-lg" style="color: #2c3e50;">{{ $petOwner->user->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Address</label>
                    <p class="text-lg" style="color: #2c3e50;">{{ $petOwner->user->address ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Emergency Contact</label>
                    <p class="text-lg" style="color: #2c3e50;">{{ $petOwner->emergency_contact ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium" style="color: #5d6d7e;">Emergency Phone</label>
                    <p class="text-lg" style="color: #2c3e50;">{{ $petOwner->emergency_phone ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pets List -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4" style="background-color: #34495e;">
            <h2 class="text-xl font-bold text-white">
                <i class="fas fa-paw mr-2" style="color: #f4d03f;"></i>
                Pets ({{ $petOwner->pets->count() }})
            </h2>
        </div>
        <div class="p-6">
            @if($petOwner->pets->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($petOwner->pets as $pet)
                    <div class="border-2 rounded-lg p-4 hover:shadow-md transition" style="border-color: #e5e7eb;">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3" 
                                 style="background-color: #d6eaf8;">
                                <i class="fas fa-paw text-xl" style="color: #0d5cb6;"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg" style="color: #2c3e50;">{{ $pet->name }}</h3>
                                <p class="text-sm" style="color: #5d6d7e;">{{ ucfirst($pet->species) }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span style="color: #5d6d7e;">Breed:</span>
                                <span style="color: #2c3e50;">{{ $pet->breed ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span style="color: #5d6d7e;">Age:</span>
                                <span style="color: #2c3e50;">{{ $pet->age }} years</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span style="color: #5d6d7e;">Gender:</span>
                                <span style="color: #2c3e50;">{{ ucfirst($pet->gender) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <button onclick="openPetModal({{ $pet->id }})" 
                                    class="flex-1 px-3 py-2 text-white rounded-lg text-sm transition" 
                                    style="background-color: #0d5cb6;">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            <!-- <button onclick="openEditModal({{ $pet->id }})" 
                                    class="flex-1 px-3 py-2 text-white rounded-lg text-sm transition" 
                                    style="background-color: #28a745;">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button> -->
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-paw text-5xl mb-4" style="color: #95a5a6;"></i>
                    <p class="text-lg" style="color: #5d6d7e;">This owner has no registered pets yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>


<!-- Pet Details Modal -->
<div id="petModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold" style="color: #2c3e50;">Pet Details</h3>
            <button onclick="closePetModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="modal-body">
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pet Edit Modal -->
<div id="editModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold" style="color: #2c3e50;">Edit Pet Details</h3>
            <button onclick="closeEditModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPetForm" class="modal-body">
            @csrf
            @method('PUT')
            <div id="editFormContent">
                <div class="flex justify-center items-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

function openPetModal(petId) {
    const modal = document.getElementById('petModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i></div>';
    
    fetch(`/doctor/patients/${petId}/details`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            const owner = data.owner || {};
            const ownerUser = owner.user || {};
            
            modalContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Pet Information</h4>
                        <div class="space-y-3">
                            <div><label class="text-sm" style="color: #5d6d7e;">Name</label><p class="font-medium" style="color: #2c3e50;">${data.name || 'N/A'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Species</label><p class="font-medium capitalize" style="color: #2c3e50;">${data.species || 'N/A'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Breed</label><p class="font-medium" style="color: #2c3e50;">${data.breed || 'N/A'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Age</label><p class="font-medium" style="color: #2c3e50;">${data.age ? data.age + ' years old' : 'Not specified'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Gender</label><p class="font-medium capitalize" style="color: #2c3e50;">${data.gender || 'Not specified'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Weight</label><p class="font-medium" style="color: #2c3e50;">${data.weight ? data.weight + ' kg' : 'Not specified'}</p></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Owner Information</h4>
                        <div class="space-y-3">
                            <div><label class="text-sm" style="color: #5d6d7e;">Owner Name</label><p class="font-medium" style="color: #2c3e50;">${ownerUser.name || 'Not specified'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Email</label><p class="font-medium" style="color: #2c3e50;">${ownerUser.email || 'Not specified'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Emergency Contact</label><p class="font-medium" style="color: #2c3e50;">${owner.emergency_contact || 'Not specified'}</p></div>
                            <div><label class="text-sm" style="color: #5d6d7e;">Emergency Phone</label><p class="font-medium" style="color: #2c3e50;">${owner.emergency_phone || 'Not specified'}</p></div>
                        </div>
                    </div>
                </div>
                ${data.medical_notes ? `<div class="mt-6 pt-6 border-t"><h4 class="text-lg font-semibold mb-3" style="color: #2c3e50;">Medical Notes</h4><p class="text-sm" style="color: #5d6d7e;">${data.medical_notes}</p></div>` : ''}
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="text-sm" style="color: #5d6d7e;">Total Appointments</label>
                            <p class="text-lg font-bold" style="color: #0d5cb6;">${data.appointments_count || 0}</p>
                        </div>
                        <div>
                            <label class="text-sm" style="color: #5d6d7e;">Medical Records</label>
                            <p class="text-lg font-bold" style="color: #0d5cb6;">${data.medical_records_count || 0}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closePetModal();openEditModal(${data.id})" class="px-4 py-2 text-white rounded-lg transition" style="background-color: #28a745;">
                            <i class="fas fa-edit mr-2"></i>Edit Pet
                        </button>
                        <a href="/medical-records/create?pet_id=${data.id}" class="px-4 py-2 text-white rounded-lg transition inline-block" style="background-color: #0d5cb6;">
                            <i class="fas fa-file-medical mr-2"></i>Add Medical Record
                        </a>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            modalContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-4xl mb-3" style="color: #d32f2f;"></i><p class="font-medium" style="color: #d32f2f;">Error loading pet details</p></div>';
        });
}

function closePetModal() {
    document.getElementById('petModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openEditModal(petId) {
 
    const modal = document.getElementById('editModal');
    const formContent = document.getElementById('editFormContent');
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    formContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i></div>';
    
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

window.onclick = function(event) {
    const petModal = document.getElementById('petModal');
    const editModal = document.getElementById('editModal');
    if (event.target == petModal) closePetModal();
    if (event.target == editModal) closeEditModal();
}
</script>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(44, 62, 80, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-between: space-between;
    align-items: center;
}

.modal-close-btn {
    color: #95a5a6;
    transition: color 0.2s;
    background: none;
    border: none;
    cursor: pointer;
}

.modal-body {
    padding: 24px;
}
</style>
@endsection