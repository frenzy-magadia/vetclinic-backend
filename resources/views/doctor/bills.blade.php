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
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Action</th>
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
                        <button onclick="viewBill({{ $bill->id }}); return false;" class="transition inline-block" style="color: #3498db;" title="View Details">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
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

<!-- Bill Details Modal -->
<div id="billModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 700px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background-color: #34495e;">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-invoice" style="color: #f4d03f;"></i>
                <h3 class="text-xl font-bold text-white">Bill Details</h3>
            </div>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer; padding: 0; line-height: 1;">
                ×
            </button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <!-- Pet, Service, and Date Info -->
            <div class="grid grid-cols-3 gap-6 mb-6">
                <div class="p-4 rounded-lg" style="background-color: #d6eaf8; border: 2px solid #3498db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Pet Name</p>
                    <p class="font-bold text-lg" style="color: #2c3e50;" id="billPetName"></p>
                </div>
                <div class="p-4 rounded-lg" style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Service</p>
                    <p class="font-semibold" style="color: #2c3e50;" id="billService"></p>
                </div>
                <div class="p-4 rounded-lg" style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #5d6d7e;">Date</p>
                    <p class="font-semibold flex items-center" style="color: #2c3e50;">
                        <i class="fas fa-calendar mr-2" style="color: #3498db;"></i>
                        <span id="billDate"></span>
                    </p>
                </div>
            </div>

            <!-- View Mode -->
            <div id="viewMode">
                <!-- Itemizing Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-bold flex items-center" style="color: #2c3e50;">
                            <i class="fas fa-list mr-2" style="color: #f4d03f;"></i>Itemizing
                        </h4>
                        <button onclick="enableEditMode()" class="px-3 py-1.5 text-white rounded-lg text-xs font-medium transition hover:opacity-90" style="background-color: #f39c12;">
                            <i class="fas fa-edit mr-1"></i>Edit Items
                        </button>
                    </div>
                    <div class="bg-white rounded-lg" style="border: 1px solid #e5e7eb;">
                        <div id="billItemsList" class="divide-y" style="border-color: #e5e7eb;"></div>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="p-4 rounded-lg mb-6" style="background-color: #f9fafb; border: 2px solid #e5e7eb;">
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: #e5e7eb;">
                        <span class="text-sm font-bold" style="color: #2c3e50;">Total Amount</span>
                        <span class="text-xl font-bold" style="color: #0d5cb6;" id="billSubtotal"></span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm font-bold" style="color: #2c3e50;">Balance Due</span>
                        <span class="text-xl font-bold" style="color: #e74c3c;" id="billBalance"></span>
                    </div>
                </div>

                <!-- Update Payment Form -->
                <div style="border-top: 2px solid #e5e7eb; padding-top: 24px;">
                    <h4 class="font-bold mb-4 flex items-center" style="color: #2c3e50;">
                        <i class="fas fa-money-bill-wave mr-2" style="color: #f4d03f;"></i>Update Payment
                    </h4>
                    <form id="updatePaymentForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Current Balance</label>
                            <div class="w-full px-4 py-3 border-2 rounded-lg font-bold" style="border-color: #0d5cb6; background-color: #d6eaf8; color: #0d5cb6;">
                                <span id="displayBalance">₱0.00</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Payment Amount</label>
                            <input type="number" id="paymentAmount" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:border-blue-500 font-semibold" style="border-color: #d1d5db;">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">New Balance</label>
                            <div class="w-full px-4 py-3 border-2 rounded-lg font-bold" style="border-color: #27ae60; background-color: #d4edda; color: #27ae60;">
                                <span id="calculatedBalance">₱0.00</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #5d6d7e;">Status</label>
                            <select name="status" id="updateStatus" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:border-blue-500 font-semibold" style="border-color: #d1d5db;">
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" onclick="closeModal()" class="px-6 py-2.5 rounded-lg transition text-sm font-medium" style="background-color: #95a5a6; color: #ffffff;">
                                Close
                            </button>
                            <button type="submit" class="px-6 py-2.5 text-white rounded-lg transition text-sm font-medium hover:opacity-90" style="background-color: #0d5cb6;">
                                <i class="fas fa-check mr-1"></i>Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="editMode" style="display: none;">
                <form id="editItemsForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h4 class="font-bold mb-4 flex items-center" style="color: #2c3e50;">
                        <i class="fas fa-edit mr-2" style="color: #f4d03f;"></i>Edit Billing Items
                    </h4>
                    <div id="editBillItems" class="mb-4 space-y-3"></div>
                    <button type="button" onclick="addEditItem()" class="px-4 py-2 text-white rounded-lg text-sm font-medium transition mb-4 hover:opacity-90" style="background-color: #27ae60;">
                        <i class="fas fa-plus mr-1"></i>Add Item
                    </button>
                    
                    <div class="flex justify-end gap-3 pt-4" style="border-top: 1px solid #e5e7eb;">
                        <button type="button" onclick="cancelEdit()" class="px-6 py-2.5 rounded-lg transition text-sm font-medium" style="background-color: #95a5a6; color: #ffffff;">
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
</div>

<script>
let currentBillData = null;
let editItemCount = 0;
let isModalOpen = false;

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const noResults = document.getElementById('noResults');
const billModal = document.getElementById('billModal');

function filterBills() {
    if (isModalOpen) return;
    
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

function viewBill(id) {
    isModalOpen = true;
    fetch('/doctor/bills/' + id)
        .then(response => response.json())
        .then(data => {
            currentBillData = data;
            document.getElementById('billPetName').textContent = data.pet.name;
            
            let serviceText = '';
            if (data.items.length > 0) {
                serviceText = data.items[0].description;
                if (data.items.length > 1) {
                    serviceText += ' +' + (data.items.length - 1) + ' more';
                }
            }
            document.getElementById('billService').textContent = serviceText || 'No services';
            document.getElementById('billDate').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A';
            
            let itemsHTML = '';
            data.items.forEach((item, index) => {
                itemsHTML += `
                    <div class="flex justify-between p-4 ${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'}">
                        <span class="text-sm font-medium" style="color: #5d6d7e;">${item.description}</span>
                        <span class="font-bold text-sm" style="color: #0d5cb6;">₱${parseFloat(item.amount).toFixed(2)}</span>
                    </div>
                `;
            });
            document.getElementById('billItemsList').innerHTML = itemsHTML;

            document.getElementById('billSubtotal').textContent = '₱' + parseFloat(data.total_amount).toFixed(2);
            document.getElementById('billBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            document.getElementById('displayBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
            const formAction = "{{ route('doctor.bills.update-status', ['bill' => ':billId']) }}".replace(':billId', data.id);
            document.getElementById('updatePaymentForm').action = formAction;
            document.getElementById('paymentAmount').value = '';
            document.getElementById('updateStatus').value = data.status;
            document.getElementById('calculatedBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
            document.getElementById('viewMode').style.display = 'block';
            document.getElementById('editMode').style.display = 'none';
            
            billModal.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('paymentAmount').addEventListener('input', function() {
    if (currentBillData) {
        const currentBalance = parseFloat(currentBillData.balance);
        const paymentAmount = parseFloat(this.value) || 0;
        const newBalance = Math.max(0, currentBalance - paymentAmount);
        document.getElementById('calculatedBalance').textContent = '₱' + newBalance.toFixed(2);
    }
});

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
    formData.append('status', document.getElementById('updateStatus').value);
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
            closeModal();
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

function enableEditMode() {
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
    
    let editHTML = '';
    editItemCount = 0;
    currentBillData.items.forEach(item => {
        editHTML += createEditItemHTML(editItemCount, item.description, item.amount);
        editItemCount++;
    });
    document.getElementById('editBillItems').innerHTML = editHTML;
    document.getElementById('editItemsForm').action = '/doctor/bills/' + currentBillData.id + '/update-items';
}

function createEditItemHTML(index, description = '', amount = '') {
    return `
        <div class="bill-item p-4 border rounded-lg" style="border-color: #d1d5db; background-color: #f9fafb;">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Description</label>
                    <input type="text" name="items[${index}][description]" value="${description}" placeholder="Service description" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm" style="border-color: #d1d5db;">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold mb-1" style="color: #5d6d7e;">Amount</label>
                        <input type="number" name="items[${index}][amount]" value="${amount}" placeholder="0.00" step="0.01" min="0" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm" style="border-color: #d1d5db;">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeEditItem(this)" class="px-3 py-2 text-white rounded-lg transition text-sm font-bold hover:opacity-90" style="background-color: #e74c3c;" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function addEditItem() {
    const container = document.getElementById('editBillItems');
    const newItem = document.createElement('div');
    newItem.innerHTML = createEditItemHTML(editItemCount);
    container.appendChild(newItem.firstElementChild);
    editItemCount++;
}

function removeEditItem(button) {
    button.closest('.bill-item').remove();
}

function cancelEdit() {
    document.getElementById('editMode').style.display = 'none';
    document.getElementById('viewMode').style.display = 'block';
}

function closeModal() {
    isModalOpen = false;
    billModal.style.display = 'none';
    filterBills();
}

billModal.addEventListener('click', function(e) {
    if (e.target === billModal) {
        closeModal();
    }
});

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