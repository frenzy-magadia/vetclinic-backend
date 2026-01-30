@extends('layouts.app')

@section('title', 'Manage Services')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#2d3748]">
                <i class="fas fa-concierge-bell mr-2"></i>Manage Services
            </h1>
            <p class="text-gray-600 mt-1">Add, edit, or remove clinic services</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinic.edit') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                <i class="fas fa-cog mr-2"></i>Clinic Settings
            </a>
            <button onclick="openAddModal()" class="px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82]">
                <i class="fas fa-plus mr-2"></i>Add Service
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="mb-4 text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Drag and drop to reorder services. Changes are saved automatically.
        </div>

        <div id="services-list" class="space-y-3">
            @foreach($services as $service)
                <div class="service-item bg-gray-50 border-l-4 border-[#0066cc] rounded-lg p-4 flex items-center justify-between hover:shadow-md transition-shadow cursor-move" data-id="{{ $service->id }}">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="drag-handle text-gray-400 cursor-grab active:cursor-grabbing">
                            <i class="fas fa-grip-vertical text-xl"></i>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#0066cc] flex items-center justify-center">
                            <i class="{{ $service->icon }} text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900">{{ $service->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $service->description }}</p>
                            <p class="text-sm font-semibold text-[#0066cc] mt-1">{{ $service->price_range }}</p>
                        </div>
                        <div>
                            @if($service->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Active</span>
                            @else
                                <span class="px-3 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-full">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button onclick='openEditModal(@json($service))' class="px-3 py-2 bg-[#d4931d] text-white rounded hover:bg-[#fcd34d]">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('clinic.services.destroy', $service) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this service?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Add New Service</h2>
        <form method="POST" action="{{ route('clinic.services.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range *</label>
                    <input type="text" name="price_range" placeholder="e.g., ₱500 - ₱1,000" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose Icon *</label>
                    <input type="hidden" name="icon" id="add_icon_input" value="fas fa-paw" required>
                    
                    <!-- Selected Icon Preview -->
                    <div class="mb-3 p-4 bg-gray-50 rounded-lg border-2 border-gray-300 flex items-center gap-3">
                        <div class="w-16 h-16 rounded-full bg-[#0066cc] flex items-center justify-center">
                            <i id="add_selected_icon" class="fas fa-paw text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Selected Icon</p>
                            <p class="text-xs text-gray-500">Click an icon below to change</p>
                        </div>
                    </div>

                    <!-- Icon Grid -->
                    <div class="grid grid-cols-6 gap-2 max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3">
                        <button type="button" onclick="selectIcon('add', 'fas fa-paw')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-paw text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-stethoscope')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-stethoscope text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-syringe')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-syringe text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-heartbeat')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-heartbeat text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-pills')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-pills text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-tooth')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-tooth text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-cut')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-cut text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-x-ray')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-x-ray text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-microscope')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-microscope text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-capsules')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-capsules text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-prescription-bottle')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-prescription-bottle text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-user-md')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-user-md text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-notes-medical')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-notes-medical text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-first-aid')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-first-aid text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-briefcase-medical')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-briefcase-medical text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-medkit')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-medkit text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-bone')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-bone text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-dog')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-dog text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-cat')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-cat text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-bath')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-bath text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-spa')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-spa text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-calendar-check')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-calendar-check text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-clipboard-list')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-clipboard-list text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('add', 'fas fa-hospital')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-hospital text-2xl text-gray-700"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82]">Add Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Edit Service</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                    <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range *</label>
                    <input type="text" name="price_range" id="edit_price_range" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose Icon *</label>
                    <input type="hidden" name="icon" id="edit_icon_input" required>
                    
                    <!-- Selected Icon Preview -->
                    <div class="mb-3 p-4 bg-gray-50 rounded-lg border-2 border-gray-300 flex items-center gap-3">
                        <div class="w-16 h-16 rounded-full bg-[#0066cc] flex items-center justify-center">
                            <i id="edit_selected_icon" class="text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Selected Icon</p>
                            <p class="text-xs text-gray-500">Click an icon below to change</p>
                        </div>
                    </div>

                    <!-- Icon Grid -->
                    <div class="grid grid-cols-6 gap-2 max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3">
                        <button type="button" onclick="selectIcon('edit', 'fas fa-paw')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-paw text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-stethoscope')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-stethoscope text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-syringe')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-syringe text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-heartbeat')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-heartbeat text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-pills')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-pills text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-tooth')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-tooth text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-cut')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-cut text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-x-ray')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-x-ray text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-microscope')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-microscope text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-capsules')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-capsules text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-prescription-bottle')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-prescription-bottle text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-user-md')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-user-md text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-notes-medical')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-notes-medical text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-first-aid')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-first-aid text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-briefcase-medical')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-briefcase-medical text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-medkit')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-medkit text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-bone')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-bone text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-dog')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-dog text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-cat')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-cat text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-bath')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-bath text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-spa')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-spa text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-calendar-check')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-calendar-check text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-clipboard-list')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-clipboard-list text-2xl text-gray-700"></i>
                        </button>
                        <button type="button" onclick="selectIcon('edit', 'fas fa-hospital')" class="icon-option p-3 rounded hover:bg-blue-100 border-2 border-transparent hover:border-[#0066cc] transition">
                            <i class="fas fa-hospital text-2xl text-gray-700"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82]">Update Service</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize drag and drop
const el = document.getElementById('services-list');
const sortable = new Sortable(el, {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'opacity-50',
    onEnd: function(evt) {
        const items = document.querySelectorAll('.service-item');
        const servicesData = Array.from(items).map((item, index) => ({
            id: item.dataset.id,
            order: index + 1
        }));

        fetch('{{ route("clinic.services.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ services: servicesData })
        });
    }
});

function selectIcon(modal, iconClass) {
    const inputId = modal === 'add' ? 'add_icon_input' : 'edit_icon_input';
    const previewId = modal === 'add' ? 'add_selected_icon' : 'edit_selected_icon';
    
    document.getElementById(inputId).value = iconClass;
    document.getElementById(previewId).className = iconClass + ' text-white text-2xl';
}

function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openEditModal(service) {
    document.getElementById('edit_name').value = service.name;
    document.getElementById('edit_description').value = service.description || '';
    document.getElementById('edit_price_range').value = service.price_range;
    document.getElementById('edit_icon_input').value = service.icon;
    document.getElementById('edit_selected_icon').className = service.icon + ' text-white text-2xl';
    document.getElementById('edit_is_active').checked = service.is_active;
    document.getElementById('editForm').action = `/clinic-services/${service.id}`;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection