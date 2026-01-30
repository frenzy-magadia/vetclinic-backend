@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#1e3a5f]">Create New Bill</h1>
        <a href="{{ route('doctor.bills') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="bg-[#2c3e50] px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-invoice text-yellow-400"></i>
                Create New Bill (Doctor View)
            </h2>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                    <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('doctor.bills.store') }}" method="POST" id="billForm">
                @csrf

                <!-- Select Pet -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Pet *
                    </label>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            id="pet_search" 
                            placeholder="Search pet by name or owner..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            autocomplete="off"
                        >
                        <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        
                        <div id="pet_results" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>

                    <input type="hidden" name="pet_id" id="pet_id" required>
                    
                    <div id="selected_pet" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 text-sm" id="selected_pet_name"></p>
                                <p class="text-xs text-gray-600" id="selected_pet_owner"></p>
                            </div>
                            <button type="button" onclick="clearPetSelection()" class="text-red-600 hover:text-red-800 ml-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Billing Items Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-[#1e3a5f] mb-4">Billing Items</h3>

                    <!-- Items List -->
                    <div id="itemsList" class="space-y-3 mb-4">
                        <!-- Added items will appear here -->
                    </div>

                    <!-- Add Item Form -->
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <!-- Item Type Selection -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Select Item Type...</label>
                                <select id="newItemType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">-- Select Type --</option>
                                    <option value="service">Service</option>
                                    <option value="inventory">Inventory Item</option>
                                </select>
                            </div>

                            <!-- Item Selection  - For Inventory -->
                            <div id="itemSelectionContainer" class="hidden">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Select Item...</label>
                                
                                <!-- Search Input Container -->
                                <div id="inventory_search_container" class="relative">
                                    <input 
                                        type="text" 
                                        id="inventory_search" 
                                        placeholder="Search inventory item..."
                                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                        autocomplete="off"
                                    >
                                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    
                                    <div id="inventory_results" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                                </div>
                                
                                <input type="hidden" id="itemSelection">
                                
                                <!-- Selected Inventory Badge -->
                                <div id="selected_inventory" class="hidden p-2 bg-purple-50 border border-purple-200 rounded-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 text-sm truncate" id="selected_inventory_name"></p>
                                        </div>
                                        <button type="button" onclick="clearInventorySelection()" class="text-red-600 hover:text-red-800 ml-2 flex-shrink-0">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Service Description -->
                            <div id="serviceDescriptionContainer" class="hidden">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                <input type="text" id="serviceDescription" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="e.g., Consultation, Surgery...">
                            </div>

                            <!-- Unit Price for Service  -->
                            <div id="servicePriceContainer" class="hidden">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Unit Price (₱)</label>
                                <input type="number" id="servicePriceInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>

                        <!-- Second Row for Inventory: Quantity and Unit Price -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <!-- Quantity (Only for Inventory) -->
                            <div id="quantityContainer" class="hidden">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Qty: <span id="qtyValue">1</span></label>
                                <input type="number" id="newQuantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" value="1" min="1">
                                <p class="text-xs text-gray-500 mt-1">Available: <span id="availableStock">-</span></p>
                            </div>

                            <!-- Unit Price for Inventory  -->
                            <div id="inventoryPriceContainer" class="hidden">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Unit Price (₱)</label>
                                <input type="number" id="inventoryPriceInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" step="0.01" min="0" readonly>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button type="button" onclick="addItemToList()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            <i class="fas fa-plus mr-2"></i> Add
                        </button>
                    </div>
                </div>

                <!-- Hidden inputs for form submission -->
                <div id="hiddenInputs"></div>

                <!-- Total Amount Display -->
                <div class="mb-6 bg-[#2c3e50] text-white rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold">Total Amount:</span>
                        <span id="totalAmount" class="text-3xl font-bold">₱7400.00</span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Notes (Optional)
                    </label>
                    <textarea name="notes" rows="3" placeholder="Add any additional notes or remarks..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('doctor.bills') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md">
                        <i class="fas fa-receipt mr-2"></i>Create Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const inventoryItems = @json($inventoryItems);
const petsData = @json($pets);
let itemsArray = [];
let itemCounter = 0;
let selectedInventoryData = null;

// ==================== PET SEARCH ====================
const petSearchInput = document.getElementById('pet_search');
const petResultsDiv = document.getElementById('pet_results');
const selectedPetDiv = document.getElementById('selected_pet');
const petIdInput = document.getElementById('pet_id');

petSearchInput.addEventListener('focus', function() {
    if (!petIdInput.value) {
        performPetSearch('');
    }
});

petSearchInput.addEventListener('input', function() {
    performPetSearch(this.value.toLowerCase());
});

function performPetSearch(query) {
    let results = petsData;

    if (query) {
        results = petsData.filter(pet => {
            const petName = pet.name.toLowerCase();
            const ownerName = pet.owner.user.name.toLowerCase();
            return petName.includes(query) || ownerName.includes(query);
        });
    }

    displayPetResults(results);
}

function displayPetResults(results) {
    if (results.length === 0) {
        petResultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No pets found</div>';
        petResultsDiv.classList.remove('hidden');
        return;
    }

    petResultsDiv.innerHTML = results.map(pet => `
        <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
             onclick='selectPet(${JSON.stringify(pet)})'>
            <p class="font-medium text-gray-900 text-sm">${pet.name}</p>
            <p class="text-xs text-gray-500 mt-0.5">
                Owner: ${pet.owner.user.name}
                <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">Approved</span>
            </p>
        </div>
    `).join('');
    
    petResultsDiv.classList.remove('hidden');
}

function selectPet(pet) {
    petIdInput.value = pet.id;
    document.getElementById('selected_pet_name').textContent = pet.name;
    document.getElementById('selected_pet_owner').textContent = `Owner: ${pet.owner.user.name}`;
    
    petSearchInput.value = '';
    petResultsDiv.classList.add('hidden');
    selectedPetDiv.classList.remove('hidden');
    petSearchInput.parentElement.classList.add('hidden');
}

function clearPetSelection() {
    petIdInput.value = '';
    selectedPetDiv.classList.add('hidden');
    petSearchInput.parentElement.classList.remove('hidden');
    petSearchInput.value = '';
    petSearchInput.focus();
}

document.addEventListener('click', function(e) {
    if (!petSearchInput.contains(e.target) && !petResultsDiv.contains(e.target)) {
        petResultsDiv.classList.add('hidden');
    }
});

// ==================== INVENTORY SEARCH ====================
const inventorySearchInput = document.getElementById('inventory_search');
const inventoryResultsDiv = document.getElementById('inventory_results');
const selectedInventoryDiv = document.getElementById('selected_inventory');
const itemSelectionInput = document.getElementById('itemSelection');

inventorySearchInput.addEventListener('focus', function() {
    if (!itemSelectionInput.value) {
        performInventorySearch('');
    }
});

inventorySearchInput.addEventListener('input', function() {
    performInventorySearch(this.value.toLowerCase());
});

function performInventorySearch(query) {
    let results = inventoryItems;

    if (query) {
        results = inventoryItems.filter(item => {
            const itemName = item.name.toLowerCase();
            const category = item.category.toLowerCase();
            return itemName.includes(query) || category.includes(query);
        });
    }

    displayInventoryResults(results);
}

function displayInventoryResults(results) {
    if (results.length === 0) {
        inventoryResultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No items found</div>';
        inventoryResultsDiv.classList.remove('hidden');
        return;
    }

    inventoryResultsDiv.innerHTML = results.map(item => {
        const stock = item.batches.reduce((sum, batch) => sum + batch.quantity, 0);
        return `
            <div class="px-4 py-2.5 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
                 onclick='selectInventoryItem(${JSON.stringify(item)})'>
                <p class="font-medium text-gray-900 text-sm">${item.name}</p>
                <p class="text-xs text-gray-500 mt-0.5">₱${parseFloat(item.selling_price).toFixed(2)} • Stock: ${stock} ${item.unit}</p>
            </div>
        `;
    }).join('');
    
    inventoryResultsDiv.classList.remove('hidden');
}

function selectInventoryItem(item) {
    const stock = item.batches.reduce((sum, batch) => sum + batch.quantity, 0);
    
    selectedInventoryData = {
        id: item.id,
        name: item.name,
        price: item.selling_price,
        stock: stock,
        unit: item.unit
    };
    
    itemSelectionInput.value = item.id;
    document.getElementById('selected_inventory_name').textContent = item.name;
    
    document.getElementById('inventoryPriceInput').value = parseFloat(item.selling_price).toFixed(2);
    document.getElementById('availableStock').textContent = `${stock} ${item.unit}`;
    document.getElementById('newQuantity').max = stock;
    
    inventorySearchInput.value = '';
    inventoryResultsDiv.classList.add('hidden');
    
    
    document.getElementById('inventory_search_container').classList.add('hidden');
    selectedInventoryDiv.classList.remove('hidden');
}

function clearInventorySelection() {
    itemSelectionInput.value = '';
    selectedInventoryData = null;
    document.getElementById('inventoryPriceInput').value = '';
    document.getElementById('availableStock').textContent = '-';
    inventorySearchInput.value = '';
    
    
    selectedInventoryDiv.classList.add('hidden');
    document.getElementById('inventory_search_container').classList.remove('hidden');
    inventorySearchInput.focus();
}

document.addEventListener('click', function(e) {
    if (!inventorySearchInput.contains(e.target) && !inventoryResultsDiv.contains(e.target)) {
        inventoryResultsDiv.classList.add('hidden');
    }
});

// ==================== ITEM TYPE SELECTION ====================

// Item type selection handler
document.getElementById('newItemType').addEventListener('change', function() {
    const itemType = this.value;
    const itemSelectionContainer = document.getElementById('itemSelectionContainer');
    const serviceDescriptionContainer = document.getElementById('serviceDescriptionContainer');
    const servicePriceContainer = document.getElementById('servicePriceContainer');
    const quantityContainer = document.getElementById('quantityContainer');
    const inventoryPriceContainer = document.getElementById('inventoryPriceContainer');
    
    if (!itemType) {
        itemSelectionContainer.classList.add('hidden');
        serviceDescriptionContainer.classList.add('hidden');
        servicePriceContainer.classList.add('hidden');
        quantityContainer.classList.add('hidden');
        inventoryPriceContainer.classList.add('hidden');
        return;
    }
    
    if (itemType === 'service') {
       
        itemSelectionContainer.classList.add('hidden');
        serviceDescriptionContainer.classList.remove('hidden');
        servicePriceContainer.classList.remove('hidden');
        quantityContainer.classList.add('hidden');
        inventoryPriceContainer.classList.add('hidden');
        document.getElementById('serviceDescription').value = '';
        document.getElementById('servicePriceInput').value = '';
        document.getElementById('serviceDescription').focus();
    } else if (itemType === 'inventory') {
      
        serviceDescriptionContainer.classList.add('hidden');
        servicePriceContainer.classList.add('hidden');
        itemSelectionContainer.classList.remove('hidden');
        quantityContainer.classList.remove('hidden');
        inventoryPriceContainer.classList.remove('hidden');
        
        // Reset inventory selection
        clearInventorySelection();
    }
});


// Quantity input handler
document.getElementById('newQuantity').addEventListener('input', function() {
    document.getElementById('qtyValue').textContent = this.value;
});

function addItemToList() {
    const itemType = document.getElementById('newItemType').value;
    const serviceDescription = document.getElementById('serviceDescription').value.trim();
    const quantity = parseInt(document.getElementById('newQuantity').value) || 1;
    let unitPrice;
    
    if (!itemType) {
        alert('Please select an item type.');
        return;
    }
    
    if (itemType === 'service') {
        if (!serviceDescription) {
            alert('Please enter a service description (e.g., Consultation, Surgery, etc.).');
            return;
        }
        unitPrice = parseFloat(document.getElementById('servicePriceInput').value);
    } else {
        if (!itemSelectionInput.value || !selectedInventoryData) {
            alert('Please select an inventory item.');
            return;
        }
        unitPrice = parseFloat(document.getElementById('inventoryPriceInput').value);
    }
    
    if (!unitPrice || unitPrice <= 0) {
        alert('Please enter a valid unit price.');
        return;
    }
    
    let itemData = {
        id: itemCounter++,
        item_type: itemType,
        quantity: itemType === 'service' ? 1 : quantity,
        unit_price: unitPrice,
        amount: (itemType === 'service' ? 1 : quantity) * unitPrice
    };
    
    if (itemType === 'service') {
        itemData.description = serviceDescription;
        itemData.inventory_item_id = null;
        itemData.display_name = serviceDescription;
        itemData.type_label = 'Service';
    } else {
        if (quantity > selectedInventoryData.stock) {
            alert(`Insufficient stock. Available: ${selectedInventoryData.stock}`);
            return;
        }
        
        itemData.description = selectedInventoryData.name;
        itemData.inventory_item_id = selectedInventoryData.id;
        itemData.display_name = selectedInventoryData.name;
        itemData.type_label = 'Inventory Item';
        itemData.unit = selectedInventoryData.unit;
    }
    
    itemsArray.push(itemData);
    renderItemsList();
    resetForm();
    updateTotal();
}

function renderItemsList() {
    const container = document.getElementById('itemsList');
    
    if (itemsArray.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No items added yet</p>';
        return;
    }
    
    container.innerHTML = itemsArray.map(item => `
        <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs px-2 py-1 rounded font-semibold ${
                            item.item_type === 'service' 
                                ? 'bg-blue-100 text-blue-700' 
                                : 'bg-purple-100 text-purple-700'
                        }">
                            ${item.type_label}
                        </span>
                        <h4 class="font-bold text-gray-900">${item.display_name}</h4>
                    </div>
                    <div class="text-sm text-gray-600">
                        ${item.item_type === 'inventory' ? `Qty: ${item.quantity} × ` : ''}₱${parseFloat(item.unit_price).toFixed(2)}
                    </div>
                </div>
                <div class="text-right ml-4">
                    <div class="text-xl font-bold text-[#1e3a5f] mb-2">₱${parseFloat(item.amount).toFixed(2)}</div>
                    <button type="button" onclick="removeItem(${item.id})" class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    updateHiddenInputs();
}

function removeItem(id) {
    itemsArray = itemsArray.filter(item => item.id !== id);
    renderItemsList();
    updateTotal();
}

function resetForm() {
    document.getElementById('newItemType').value = '';
    document.getElementById('serviceDescription').value = '';
    document.getElementById('servicePriceInput').value = '';
    document.getElementById('inventoryPriceInput').value = '';
    document.getElementById('newQuantity').value = '1';
    itemSelectionInput.value = '';
    selectedInventoryData = null;
    
    document.getElementById('itemSelectionContainer').classList.add('hidden');
    document.getElementById('serviceDescriptionContainer').classList.add('hidden');
    document.getElementById('servicePriceContainer').classList.add('hidden');
    document.getElementById('quantityContainer').classList.add('hidden');
    document.getElementById('inventoryPriceContainer').classList.add('hidden');
    document.getElementById('availableStock').textContent = '-';
    document.getElementById('qtyValue').textContent = '1';
    
    // Reset inventory search UI
    selectedInventoryDiv.classList.add('hidden');
    document.getElementById('inventory_search_container').classList.remove('hidden');
    inventorySearchInput.value = '';
}

function updateTotal() {
    const total = itemsArray.reduce((sum, item) => sum + item.amount, 0);
    document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);
}

function updateHiddenInputs() {
    const container = document.getElementById('hiddenInputs');
    container.innerHTML = itemsArray.map((item, index) => `
        <input type="hidden" name="items[${index}][item_type]" value="${item.item_type}">
        <input type="hidden" name="items[${index}][inventory_item_id]" value="${item.inventory_item_id || ''}">
        <input type="hidden" name="items[${index}][description]" value="${item.description}">
        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
    `).join('');
}

// Form validation
document.getElementById('billForm').addEventListener('submit', function(e) {
    if (itemsArray.length === 0) {
        e.preventDefault();
        alert('Please add at least one billing item.');
        return false;
    }
    
    if (!petIdInput.value) {
        e.preventDefault();
        alert('Please select a pet.');
        return false;
    }
});

// Initialize
updateTotal();
</script>

<style>
#itemsList:empty::after {
    content: 'No items added yet';
    display: block;
    text-align: center;
    padding: 2rem;
    color: #9CA3AF;
}
</style>
@endsection