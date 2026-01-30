@extends('layouts.app')
@section('title', 'Billing')
@section('content')

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold" style="color: #2c3e50;">Billing</h1>
    <p class="mt-2" style="color: #5d6d7e;">Manage all billing records and payments</p>
</div>

<div class="mb-6">
    <div class="flex justify-between items-center gap-4">
        <div class="flex gap-4 flex-1">
            <!-- Status Filter Dropdown -->
            <select id="statusFilter" class="px-4 py-2 font-bold rounded-lg cursor-pointer transition text-sm border-0" style="background-color: #f4d03f; color: #2c3e50; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%232c3e50\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 8px center; background-size: 20px; padding-right: 32px;">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
                <option value="unpaid">Unpaid</option>
            </select>
            
            <!-- Search Input -->
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search pet, owner, service..." 
                    class="w-full pl-4 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    style="border-color: #d1d5db;"
                >
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <a href="{{ route('doctor.bills.create') }}" class="inline-flex items-center px-4 py-2 text-white rounded-lg whitespace-nowrap font-medium text-sm transition hover:opacity-90" style="background-color: #0d5cb6;">
            <i class="fas fa-plus mr-2"></i>Add Bill
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
        {{ session('success') }}
    </div>
@endif

@if($bills->count())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full" id="billsTable">
            <thead style="background-color: #34495e;">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                @foreach($bills as $bill)
                <tr class="bill-row hover:bg-gray-50 transition" 
                    data-bill-id="{{ $bill->id }}" 
                    data-pet="{{ strtolower($bill->pet->name) }}" 
                    data-owner="{{ strtolower($bill->pet->owner->user->name) }}" 
                    data-status="{{ $bill->status }}">
                    <td class="px-6 py-4 text-sm font-medium" style="color: #2c3e50;">
                        <i class="fas fa-paw mr-2" style="color: #3498db;"></i>{{ $bill->pet->name }}
                    </td>
                    <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                        @if($bill->items->count() > 0)
                            {{ $bill->items->first()->description }}
                            @if($bill->items->count() > 1)
                                <span style="color: #0d5cb6;">+{{ $bill->items->count() - 1 }} more</span>
                            @endif
                        @else
                            No items
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold" style="color: #2c3e50;">₱{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold" style="color: #2c3e50;">₱{{ number_format($bill->balance, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($bill->status == 'paid')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-green-100 text-green-800 border-2 border-green-400">
                                <i class="fas fa-check-circle mr-1"></i>Paid
                            </span>
                        @elseif($bill->status == 'partial')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-yellow-100 text-yellow-800 border-2 border-yellow-400">
                                <i class="fas fa-clock mr-1"></i>Partial
                            </span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-red-100 text-red-800 border-2 border-red-400">
                                <i class="fas fa-exclamation-circle mr-1"></i>Unpaid
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <!-- View Details -->
                            <button onclick="viewBillDetails({{ $bill->id }})" class="transition inline-block" style="color: #3498db;" title="View Details">
                                <i class="fas fa-eye text-lg"></i>
                            </button>
                            
                            <!-- Edit Items -->
                            <button onclick="openEditItemsModal({{ $bill->id }})" class="transition inline-block" style="color: #f39c12;" title="Edit Items">
                                <i class="fas fa-edit text-lg"></i>
                            </button>
                            
                            <!-- Update Payment -->
                            <button onclick="openUpdatePaymentModal({{ $bill->id }})" class="transition inline-block" style="color: #27ae60;" title="Update Payment">
                                <i class="fas fa-money-bill-wave text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="noResults" class="text-center py-8" style="color: #5d6d7e; display: none;">
            <i class="fas fa-search text-3xl mb-2" style="color: #d1d5db;"></i>
            <p class="text-sm">No bills match your search criteria.</p>
        </div>
    </div>

    <div class="mt-4">
        {{ $bills->links() }}
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                    <i class="fas fa-file-invoice-dollar text-3xl" style="color: #3498db;"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color: #2c3e50;">No Bills Found</h3>
            <p class="mb-6" style="color: #5d6d7e;">Get started by creating your first bill.</p>
            <a href="{{ route('doctor.bills.create') }}" class="inline-flex items-center gap-2 px-6 py-2 rounded transition text-white" style="background-color: #0d5cb6;">
                <i class="fas fa-plus"></i>Create First Bill
            </a>
        </div>
    </div>
@endif

<!-- View Bill Details Modal -->
<div id="viewBillModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 700px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background-color: #34495e;">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-invoice" style="color: #f4d03f;"></i>
                <h3 class="text-xl font-bold text-white">Bill Details</h3>
            </div>
            <button onclick="closeViewBillModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer; padding: 0; line-height: 1;">×</button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <!-- Pet, Service, and Date Info -->
            <div class="grid grid-cols-3 gap-6 mb-6">
                <div class="p-4 rounded-lg" style="background-color: #d6eaf8; border: 2px solid #3498db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Pet Name</p>
                    <p class="font-bold text-lg" style="color: #2c3e50;" id="viewBillPetName"></p>
                </div>
                <div class="p-4 rounded-lg" style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Owner</p>
                    <p class="font-semibold" style="color: #2c3e50;" id="viewBillOwner"></p>
                </div>
                <div class="p-4 rounded-lg" style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Date</p>
                    <p class="font-semibold flex items-center" style="color: #2c3e50;">
                        <i class="fas fa-calendar mr-2" style="color: #3498db;"></i>
                        <span id="viewBillDate"></span>
                    </p>
                </div>
            </div>

            <!-- Items List -->
            <div class="mb-6">
                <h4 class="font-bold mb-3 flex items-center" style="color: #2c3e50;">
                    <i class="fas fa-list mr-2" style="color: #f4d03f;"></i>Itemizing
                </h4>
                <div class="bg-white rounded-lg" style="border: 1px solid #e5e7eb;">
                    <div id="viewBillItemsList" class="divide-y" style="border-color: #e5e7eb;"></div>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="p-4 rounded-lg" style="background-color: #f9fafb; border: 2px solid #e5e7eb;">
                <div class="flex justify-between items-center py-2 border-b" style="border-color: #e5e7eb;">
                    <span class="text-sm font-bold" style="color: #2c3e50;">Total Amount</span>
                    <span class="text-xl font-bold" style="color: #0d5cb6;" id="viewBillTotal"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b" style="border-color: #e5e7eb;">
                    <span class="text-sm font-bold" style="color: #2c3e50;">Paid Amount</span>
                    <span class="text-lg font-semibold" style="color: #27ae60;" id="viewBillPaid"></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-bold" style="color: #2c3e50;">Balance Due</span>
                    <span class="text-xl font-bold" style="color: #e74c3c;" id="viewBillBalance"></span>
                </div>
            </div>

            <!-- Notes Section -->
            <div id="viewBillNotesSection" class="mt-4" style="display: none;">
                <h4 class="font-bold mb-2 flex items-center" style="color: #2c3e50;">
                    <i class="fas fa-sticky-note mr-2" style="color: #f4d03f;"></i>Notes
                </h4>
                <div class="p-3 rounded-lg" style="background-color: #fffbeb; border: 1px solid #fde68a;">
                    <p class="text-sm" style="color: #78350f;" id="viewBillNotes"></p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t" style="border-color: #e5e7eb; background-color: #f9fafb;">
            <button onclick="closeViewBillModal()" class="w-full px-6 py-2.5 rounded-lg transition text-sm font-medium" style="background-color: #95a5a6; color: #ffffff;">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Items Modal -->
<div id="editItemsModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 800px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background-color: #34495e;">
            <div class="flex items-center gap-2">
                <i class="fas fa-edit" style="color: #f4d03f;"></i>
                <h3 class="text-xl font-bold text-white">Edit Billing Items</h3>
            </div>
            <button onclick="closeEditItemsModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer; padding: 0; line-height: 1;">×</button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <form id="editItemsForm" method="POST">
                @csrf
                @method('PUT')
                
                <div id="editBillItems" class="mb-4 space-y-3"></div>
                
                <button type="button" onclick="addEditItem()" class="w-full px-4 py-2 text-white rounded-lg text-sm font-medium transition mb-4 hover:opacity-90" style="background-color: #27ae60;">
                    <i class="fas fa-plus mr-1"></i>Add Item
                </button>
                
                <div class="flex justify-end gap-3 pt-4" style="border-top: 1px solid #e5e7eb;">
                    <button type="button" onclick="closeEditItemsModal()" class="px-6 py-2.5 rounded-lg transition text-sm font-medium" style="background-color: #95a5a6; color: #ffffff;">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-white rounded-lg transition text-sm font-medium hover:opacity-90" style="background-color: #0d5cb6;">
                        <i class="fas fa-save mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Payment Modal -->
<div id="updatePaymentModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 500px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background-color: #34495e;">
            <div class="flex items-center gap-2">
                <i class="fas fa-money-bill-wave" style="color: #f4d03f;"></i>
                <h3 class="text-xl font-bold text-white">Update Payment</h3>
            </div>
            <button onclick="closeUpdatePaymentModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer; padding: 0; line-height: 1;">×</button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <form id="updatePaymentForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Current Balance</label>
                    <div class="w-full px-4 py-3 border-2 rounded-lg font-bold" style="border-color: #0d5cb6; background-color: #d6eaf8; color: #0d5cb6;">
                        <span id="paymentCurrentBalance">₱0.00</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Payment Amount</label>
                    <input type="number" id="paymentAmount" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:border-blue-500 font-semibold" style="border-color: #d1d5db;">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">New Balance</label>
                    <div class="w-full px-4 py-3 border-2 rounded-lg font-bold" style="border-color: #27ae60; background-color: #d4edda; color: #27ae60;">
                        <span id="paymentNewBalance">₱0.00</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Status</label>
                    <select name="status" id="paymentStatus" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:border-blue-500 font-semibold" style="border-color: #d1d5db;">
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeUpdatePaymentModal()" class="px-6 py-2.5 rounded-lg transition text-sm font-medium" style="background-color: #95a5a6; color: #ffffff;">
                        Close
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-white rounded-lg transition text-sm font-medium hover:opacity-90" style="background-color: #0d5cb6;">
                        <i class="fas fa-check mr-1"></i>Update Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentBillData = null;
let editItemCount = 0;
let inventoryItems = [];
let editItemInventorySearches = {};

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const noResults = document.getElementById('noResults');

// Fetch inventory items on page load
fetch('/doctor/inventory-items-list')
    .then(response => response.json())
    .then(data => {
        inventoryItems = data;
    })
    .catch(error => console.error('Error fetching inventory:', error));

function filterBills() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const statusValue = statusFilter.value.trim().toLowerCase();
    
    const rows = document.querySelectorAll('.bill-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const petName = (row.getAttribute('data-pet') || '').trim();
        const ownerName = (row.getAttribute('data-owner') || '').trim();
        const rowStatus = (row.getAttribute('data-status') || '').trim().toLowerCase();
        
        const matchesSearch = searchTerm === '' || petName.includes(searchTerm) || ownerName.includes(searchTerm);
        const matchesStatus = statusValue === '' || rowStatus === statusValue;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = 'table-row';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', filterBills);
statusFilter.addEventListener('change', filterBills);

// View Bill Details
function viewBillDetails(id) {
    fetch('/doctor/bills/' + id)
        .then(response => response.json())
        .then(data => {
            currentBillData = data;
            
            document.getElementById('viewBillPetName').textContent = data.pet.name;
            document.getElementById('viewBillOwner').textContent = data.pet.owner.user.name;
            document.getElementById('viewBillDate').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A';
            
            let itemsHTML = '';
            data.items.forEach((item, index) => {
                const itemTypeBadge = item.item_type === 'service' 
                    ? '<span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 font-semibold">Service</span>'
                    : '<span class="text-xs px-2 py-1 rounded bg-purple-100 text-purple-700 font-semibold">Inventory</span>';
                
                itemsHTML += `
                    <div class="flex justify-between items-center p-4 ${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'}">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                ${itemTypeBadge}
                                <span class="text-sm font-medium" style="color: #5d6d7e;">${item.description}</span>
                            </div>
                            ${item.item_type === 'inventory' ? `<p class="text-xs" style="color: #95a5a6;">Qty: ${item.quantity} × ₱${parseFloat(item.unit_price).toFixed(2)}</p>` : ''}
                        </div>
                        <span class="font-bold text-sm" style="color: #0d5cb6;">₱${parseFloat(item.amount).toFixed(2)}</span>
                    </div>
                `;
            });
            document.getElementById('viewBillItemsList').innerHTML = itemsHTML;

            document.getElementById('viewBillTotal').textContent = '₱' + parseFloat(data.total_amount).toFixed(2);
            document.getElementById('viewBillPaid').textContent = '₱' + parseFloat(data.paid_amount).toFixed(2);
            document.getElementById('viewBillBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
            if (data.notes && data.notes.trim() !== '') {
                document.getElementById('viewBillNotes').textContent = data.notes;
                document.getElementById('viewBillNotesSection').style.display = 'block';
            } else {
                document.getElementById('viewBillNotesSection').style.display = 'none';
            }
            
            document.getElementById('viewBillModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeViewBillModal() {
    document.getElementById('viewBillModal').style.display = 'none';
}

// Edit Items Modal
function openEditItemsModal(id) {
    fetch('/doctor/bills/' + id)
        .then(response => response.json())
        .then(data => {
            currentBillData = data;
            
            let editHTML = '';
            editItemCount = 0;
            editItemInventorySearches = {};
            
            data.items.forEach(item => {
                editHTML += createEditItemHTML(editItemCount, item.item_type, item.inventory_item_id, item.description, item.quantity, item.unit_price);
                editItemCount++;
            });
            document.getElementById('editBillItems').innerHTML = editHTML;
            document.getElementById('editItemsForm').action = '/doctor/bills/' + data.id + '/update-items';
            
            // Initialize inventory search for existing items
            data.items.forEach((item, idx) => {
                if (item.item_type === 'inventory') {
                    setupInventorySearch(idx);
                }
            });
            
            document.getElementById('editItemsModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function createEditItemHTML(index, itemType = 'service', inventoryItemId = '', description = '', quantity = 1, unitPrice = '') {
    const showInventoryControls = itemType === 'inventory';
    const showServiceControls = itemType === 'service';
    
    return `
        <div class="bill-item p-4 border rounded-lg" style="border-color: #d1d5db; background-color: #f9fafb;" data-item-index="${index}">
            <div class="grid grid-cols-1 gap-3">
                <!-- Item Type Selection -->
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Item Type</label>
                    <select name="items[${index}][item_type]" 
                            class="item-type-select w-full px-3 py-2 border rounded-lg text-sm" 
                            style="border-color: #d1d5db;" 
                            required
                            onchange="handleItemTypeChange(${index}, this.value)">
                        <option value="service" ${itemType === 'service' ? 'selected' : ''}>Service</option>
                        <option value="inventory" ${itemType === 'inventory' ? 'selected' : ''}>Inventory</option>
                    </select>
                </div>

                <!-- Service Description (shown for service type) -->
                <div class="service-description-container-${index}" style="display: ${showServiceControls ? 'block' : 'none'};">
                    <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Service Description</label>
                    <input type="text" 
                           name="items[${index}][description]" 
                           value="${description}" 
                           placeholder="e.g., Consultation, Surgery..." 
                           class="service-description-${index} w-full px-3 py-2 border rounded-lg text-sm" 
                           style="border-color: #d1d5db;"
                           ${showServiceControls ? 'required' : ''}>
                </div>

                <!-- Inventory Item Search (shown for inventory type) -->
                <div class="inventory-search-container-${index}" style="display: ${showInventoryControls ? 'block' : 'none'};">
                    <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Select Inventory Item</label>
                    <div class="relative">
                        <input type="text" 
                               id="inventory_search_${index}" 
                               placeholder="Search inventory item..."
                               class="w-full px-3 py-2 pr-10 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               style="border-color: #d1d5db;"
                               autocomplete="off">
                        <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        
                        <div id="inventory_results_${index}" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    
                    <input type="hidden" name="items[${index}][inventory_item_id]" id="inventory_item_id_${index}" value="${inventoryItemId || ''}">
                    <input type="hidden" name="items[${index}][description]" id="inventory_description_${index}" value="${description}">
                    
                    <div id="selected_inventory_${index}" class="hidden mt-2 p-2 bg-purple-50 border border-purple-200 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate" id="selected_inventory_name_${index}"></p>
                                <p class="text-xs text-gray-500" id="selected_inventory_stock_${index}"></p>
                            </div>
                            <button type="button" onclick="clearInventorySelection(${index})" class="text-red-600 hover:text-red-800 ml-2 flex-shrink-0">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quantity and Unit Price Row -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Quantity -->
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Quantity</label>
                        <input type="number" 
                               name="items[${index}][quantity]" 
                               id="quantity_${index}"
                               value="${quantity}" 
                               min="1" 
                               required 
                               class="w-full px-3 py-2 border rounded-lg text-sm" 
                               style="border-color: #d1d5db;"
                               ${showInventoryControls ? '' : 'readonly'}>
                        <p class="text-xs text-gray-500 mt-1 available-stock-${index}" style="display: ${showInventoryControls ? 'block' : 'none'};">
                            Available: <span id="available_stock_${index}">-</span>
                        </p>
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Unit Price (₱)</label>
                        <input type="number" 
                               name="items[${index}][unit_price]" 
                               id="unit_price_${index}"
                               value="${unitPrice}" 
                               step="0.01" 
                               min="0" 
                               required 
                               class="w-full px-3 py-2 border rounded-lg text-sm ${showInventoryControls ? 'bg-gray-100' : ''}" 
                               style="border-color: #d1d5db;"
                               ${showInventoryControls ? 'readonly' : ''}>
                    </div>

                    <!-- Remove Button -->
                    <div class="flex items-end">
                        <button type="button" onclick="removeEditItem(this)" class="w-full px-3 py-2 text-white rounded-lg transition text-sm font-bold hover:opacity-90" style="background-color: #e74c3c;" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function setupInventorySearch(index) {
    const searchInput = document.getElementById(`inventory_search_${index}`);
    const resultsDiv = document.getElementById(`inventory_results_${index}`);
    
    if (!searchInput || !resultsDiv) return;
    
    searchInput.addEventListener('focus', function() {
        const itemId = document.getElementById(`inventory_item_id_${index}`).value;
        if (!itemId) {
            performInventorySearch(index, '');
        }
    });
    
    searchInput.addEventListener('input', function() {
        performInventorySearch(index, this.value.toLowerCase());
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
}

function performInventorySearch(index, query) {
    const resultsDiv = document.getElementById(`inventory_results_${index}`);
    let results = inventoryItems;
    
    if (query) {
        results = inventoryItems.filter(item => {
            const itemName = item.name.toLowerCase();
            const category = item.category.toLowerCase();
            return itemName.includes(query) || category.includes(query);
        });
    }
    
    displayInventoryResults(index, results);
}

function displayInventoryResults(index, results) {
    const resultsDiv = document.getElementById(`inventory_results_${index}`);
    
    if (results.length === 0) {
        resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm text-center">No items found</div>';
        resultsDiv.classList.remove('hidden');
        return;
    }
    
    resultsDiv.innerHTML = results.map(item => {
        const stock = item.total_stock || 0;
        return `
            <div class="px-4 py-2.5 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" 
                 onclick='selectInventoryItem(${index}, ${JSON.stringify(item)})'>
                <p class="font-medium text-gray-900 text-sm">${item.name}</p>
                <p class="text-xs text-gray-500 mt-0.5">₱${parseFloat(item.selling_price).toFixed(2)} • Stock: ${stock} ${item.unit}</p>
            </div>
        `;
    }).join('');
    
    resultsDiv.classList.remove('hidden');
}

function selectInventoryItem(index, item) {
    const stock = item.total_stock || 0;
    
    // Set hidden fields
    document.getElementById(`inventory_item_id_${index}`).value = item.id;
    document.getElementById(`inventory_description_${index}`).value = item.name;
    document.getElementById(`unit_price_${index}`).value = parseFloat(item.selling_price).toFixed(2);
    
    // Update display
    document.getElementById(`selected_inventory_name_${index}`).textContent = item.name;
    document.getElementById(`selected_inventory_stock_${index}`).textContent = `₱${parseFloat(item.selling_price).toFixed(2)} • Stock: ${stock} ${item.unit}`;
    document.getElementById(`available_stock_${index}`).textContent = `${stock} ${item.unit}`;
    
    // Set max quantity
    const quantityInput = document.getElementById(`quantity_${index}`);
    quantityInput.max = stock;
    
    // Hide search, show selected
    document.getElementById(`inventory_search_${index}`).value = '';
    document.getElementById(`inventory_results_${index}`).classList.add('hidden');
    document.querySelector(`.inventory-search-container-${index} .relative`).style.display = 'none';
    document.getElementById(`selected_inventory_${index}`).classList.remove('hidden');
}

function clearInventorySelection(index) {
    document.getElementById(`inventory_item_id_${index}`).value = '';
    document.getElementById(`inventory_description_${index}`).value = '';
    document.getElementById(`unit_price_${index}`).value = '';
    document.getElementById(`available_stock_${index}`).textContent = '-';
    document.getElementById(`inventory_search_${index}`).value = '';
    
    document.getElementById(`selected_inventory_${index}`).classList.add('hidden');
    document.querySelector(`.inventory-search-container-${index} .relative`).style.display = 'block';
    document.getElementById(`inventory_search_${index}`).focus();
}

function handleItemTypeChange(index, itemType) {
    const serviceContainer = document.querySelector(`.service-description-container-${index}`);
    const inventoryContainer = document.querySelector(`.inventory-search-container-${index}`);
    const quantityInput = document.getElementById(`quantity_${index}`);
    const unitPriceInput = document.getElementById(`unit_price_${index}`);
    const availableStockDiv = document.querySelector(`.available-stock-${index}`);
    
    if (itemType === 'service') {
        // Show service fields
        serviceContainer.style.display = 'block';
        inventoryContainer.style.display = 'none';
        availableStockDiv.style.display = 'none';
        
        // Reset and configure for service
        quantityInput.value = '1';
        quantityInput.readOnly = true;
        unitPriceInput.readOnly = false;
        unitPriceInput.classList.remove('bg-gray-100');
        unitPriceInput.value = '';
        
        // Make service description required
        const serviceDesc = document.querySelector(`.service-description-${index}`);
        serviceDesc.required = true;
        
        // Clear inventory fields
        clearInventorySelection(index);
        
    } else if (itemType === 'inventory') {
        // Show inventory fields
        serviceContainer.style.display = 'none';
        inventoryContainer.style.display = 'block';
        availableStockDiv.style.display = 'block';
        
        // Configure for inventory
        quantityInput.value = '1';
        quantityInput.readOnly = false;
        unitPriceInput.readOnly = true;
        unitPriceInput.classList.add('bg-gray-100');
        
        // Remove service description requirement
        const serviceDesc = document.querySelector(`.service-description-${index}`);
        serviceDesc.required = false;
        
        // Setup inventory search if not already done
        setupInventorySearch(index);
    }
}

function addEditItem() {
    const container = document.getElementById('editBillItems');
    const newItem = document.createElement('div');
    newItem.innerHTML = createEditItemHTML(editItemCount, 'service', '', '', 1, '');
    container.appendChild(newItem.firstElementChild);
    
    // Setup event handlers for new item
    setupInventorySearch(editItemCount);
    
    editItemCount++;
}

function removeEditItem(button) {
    button.closest('.bill-item').remove();
}

function closeEditItemsModal() {
    document.getElementById('editItemsModal').style.display = 'none';
    editItemInventorySearches = {};
}

// Edit Items Form Submit
document.getElementById('editItemsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate inventory items have selections
    const items = document.querySelectorAll('.bill-item');
    let isValid = true;
    
    items.forEach((item, idx) => {
        const itemType = item.querySelector('select[name*="[item_type]"]').value;
        
        if (itemType === 'inventory') {
            const inventoryId = item.querySelector('input[name*="[inventory_item_id]"]').value;
            const quantity = parseInt(item.querySelector('input[name*="[quantity]"]').value);
            const maxQuantity = parseInt(item.querySelector('input[name*="[quantity]"]').max);
            
            if (!inventoryId) {
                alert('Please select an inventory item for all inventory type items.');
                isValid = false;
                return false;
            }
            
            if (quantity > maxQuantity) {
                alert(`Insufficient stock. Maximum available: ${maxQuantity}`);
                isValid = false;
                return false;
            }
        } else if (itemType === 'service') {
            const description = item.querySelector('input[name*="[description]"]').value.trim();
            if (!description) {
                alert('Please enter a description for all service items.');
                isValid = false;
                return false;
            }
        }
    });
    
    if (!isValid) return;
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => {
        if (response.ok) {
            alert('Bill items updated successfully');
            closeEditItemsModal();
            location.reload();
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Error updating items');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating items: ' + error.message);
    });
});

// Update Payment Modal
function openUpdatePaymentModal(id) {
    fetch('/doctor/bills/' + id)
        .then(response => response.json())
        .then(data => {
            currentBillData = data;
            
            document.getElementById('paymentCurrentBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            document.getElementById('paymentAmount').value = '';
            document.getElementById('paymentNewBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            document.getElementById('paymentStatus').value = data.status;
            
            const formAction = "{{ route('doctor.bills.update-status', ['bill' => ':billId']) }}".replace(':billId', data.id);
            document.getElementById('updatePaymentForm').action = formAction;
            
            document.getElementById('updatePaymentModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeUpdatePaymentModal() {
    document.getElementById('updatePaymentModal').style.display = 'none';
}

// Payment amount calculator
document.getElementById('paymentAmount').addEventListener('input', function() {
    if (currentBillData) {
        const currentBalance = parseFloat(currentBillData.balance);
        const paymentAmount = parseFloat(this.value) || 0;
        const newBalance = Math.max(0, currentBalance - paymentAmount);
        document.getElementById('paymentNewBalance').textContent = '₱' + newBalance.toFixed(2);
    }
});

// Update Payment Form Submit
document.getElementById('updatePaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
    
    if (paymentAmount <= 0) {
        alert('Please enter a valid payment amount');
        return;
    }

    const newPaidAmount = parseFloat(currentBillData.paid_amount) + paymentAmount;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                      document.querySelector('input[name="_token"]')?.value;

    const formData = new FormData();
    formData.append('paid_amount', newPaidAmount.toFixed(2));
    formData.append('status', document.getElementById('paymentStatus').value);
    formData.append('_method', 'PUT');
    formData.append('_token', csrfToken);

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment updated successfully');
            closeUpdatePaymentModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment');
    });
});

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewBillModal');
    const editModal = document.getElementById('editItemsModal');
    const paymentModal = document.getElementById('updatePaymentModal');
    
    if (event.target === viewModal) {
        closeViewBillModal();
    }
    if (event.target === editModal) {
        closeEditItemsModal();
    }
    if (event.target === paymentModal) {
        closeUpdatePaymentModal();
    }
};

filterBills();
</script>

<style>
#statusFilter:hover {
    background-color: #f9e79f;
}

tbody td {
    vertical-align: middle;
}

.fas {
    font-size: 1.125rem;
}

button:hover, a:hover {
    opacity: 0.8;
}
</style>

@endsection