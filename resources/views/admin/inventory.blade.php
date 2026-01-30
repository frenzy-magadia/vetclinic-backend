@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-[#1e3a5f]">Inventory</h1>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.inventory') }}" class="bg-white border-l-4 border-[#0d47a1] p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Total Items</h3>
        <p class="text-2xl font-bold text-[#0d47a1] mt-2">{{ $totalItems }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'low-stock') }}" class="bg-white border-l-4 border-[#d4911e] p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Low Stock Items</h3>
        <p class="text-2xl font-bold text-[#d4911e] mt-2">{{ $lowStockCount }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'top-used') }}" class="bg-white border-l-4 border-[#2c3e50] p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Top Used Items</h3>
        <p class="text-2xl font-bold text-[#2c3e50] mt-2">{{ $topUsedItems->count() }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'expired') }}" class="bg-white border-l-4 border-red-600 p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Expired Items</h3>
        <p class="text-2xl font-bold text-red-600 mt-2">{{ $expiredCount }}</p>
    </a>
</div>

<!-- Inventory Items Section -->
<div class="bg-white shadow-lg rounded-lg p-6 border-t-4 border-[#1e3a5f]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-[#1e3a5f]">Inventory Items</h2>
        <button onclick="openAddModal()" class="px-4 py-2 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition">
            <i class="fas fa-plus mr-2"></i>Add Item
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('admin.inventory') }}" id="filterForm">
            <div class="relative inline-block">
                <select name="category" id="categoryFilter" class="px-4 py-2 bg-[#ffd700] text-gray-900 font-bold rounded-lg cursor-pointer hover:bg-[#ffc107] transition text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <form method="GET" action="{{ route('admin.inventory') }}" class="flex items-center gap-2">
            <input type="hidden" name="category" value="{{ $category }}">
            <input type="text" name="search" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1] w-48" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition">
                <i class="fas fa-search text-sm"></i>
            </button>
            @if(request('search') || request('category'))
            <a href="{{ route('admin.inventory') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times text-sm"></i>
            </a>
            @endif
        </form>
    </div>

    @if($items->count())
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[#1e3a5f]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Total Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Batches</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase tracking-wider">Quick Adjust</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 bg-[#0d47a1] bg-opacity-10 text-[#0d47a1] rounded text-sm font-medium">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="@if($item->hasExpiredBatches()) text-red-600 font-bold @elseif($item->isLowStock()) text-[#d4911e] font-bold @else text-gray-900 @endif">
                                {{ $item->total_stock }} {{ $item->unit }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openBatchesModal({{ $item->id }})" class="text-[#0d47a1] hover:underline text-sm">
                                <i class="fas fa-boxes mr-1"></i>{{ $item->batches->count() }} batch(es)
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-3">
                                <form action="{{ route('admin.inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="adjustment_type" value="reduce">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 font-bold text-lg flex items-center justify-center transition">-</button>
                                </form>
                                <span class="font-bold text-lg min-w-[3rem] text-center text-[#1e3a5f]">{{ $item->total_stock }}</span>
                                <form action="{{ route('admin.inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="adjustment_type" value="add">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-8 h-8 bg-green-500 text-white rounded-lg hover:bg-green-600 font-bold text-lg flex items-center justify-center transition">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openMassAdjustModal({{ $item->id }}, '{{ $item->name }}', {{ $item->total_stock }}, '{{ $item->unit }}')" class="px-3 py-1.5 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition" title="Mass Adjustment">
                                    <i class="fas fa-sliders-h mr-1"></i>Mass Adjust
                                </button>
                                <button onclick="openAddBatchModal({{ $item->id }})" class="px-3 py-1.5 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 transition">
                                    <i class="fas fa-plus mr-1"></i>Add Batch
                                </button>
                                <div class="flex items-center gap-1">
                                    <button onclick="openEditModal({{ $item->id }})" class="w-8 h-8 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition flex items-center justify-center" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center justify-center" title="Delete">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    @else
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <i class="fas fa-box-open text-gray-400 text-4xl mb-3"></i>
            <p class="text-gray-500 text-lg mb-2">No inventory items found.</p>
        </div>
    @endif
</div>

<!-- Add/Edit Item Modal -->
<div id="itemModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 58, 95, 0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 700px; position: relative; border-top: 4px solid #1e3a5f;">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-[#1e3a5f]">Add New Item</h3>
            <button onclick="closeModal('itemModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="itemForm" method="POST">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Item Name</label>
                <input type="text" name="name" id="itemName" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Category</label>
                <select name="category" id="itemCategory" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                    <option value="Medicine">Medicine</option>
                    <option value="Consumables">Consumables</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Pet Food">Pet Food</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Description</label>
                <textarea name="description" id="itemDescription" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]" rows="2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Minimum Stock Level</label>
                    <input type="number" name="minimum_stock_level" id="itemMinStock" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Unit</label>
                    <input type="text" name="unit" id="itemUnit" required placeholder="pieces, bottles, etc." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Selling Price</label>
                    <input type="number" name="selling_price" id="itemPrice" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Supplier Name</label>
                    <input type="text" name="supplier_name" id="itemSupplier" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
            </div>

            <div id="batchSection" class="border-t pt-4 mt-4">
                <h4 class="font-semibold text-[#1e3a5f] mb-3">Initial Batch</h4>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Batch Number</label>
                        <input type="text" name="batch_number" id="batchNumber" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Quantity</label>
                        <input type="number" name="quantity" id="batchQuantity" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Manufacture Date</label>
                        <input type="date" name="manufacture_date" id="manufactureDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiryDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('itemModal')" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Batch Modal -->
<div id="addBatchModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 58, 95, 0.5);">
    <div style="background-color: white; margin: 10% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 600px; position: relative; border-top: 4px solid #1e3a5f;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#1e3a5f]">Add New Batch</h3>
            <button onclick="closeModal('addBatchModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="addBatchForm" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Batch Number</label>
                <input type="text" name="batch_number" id="newBatchNumber" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Quantity</label>
                <input type="number" name="quantity" id="newBatchQuantity" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Manufacture Date</label>
                    <input type="date" name="manufacture_date" id="newManufactureDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" id="newExpiryDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('addBatchModal')" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Add Batch</button>
            </div>
        </form>
    </div>
</div>

<!-- Mass Adjust Stock Modal -->
<div id="massAdjustModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 58, 95, 0.5);">
    <div style="background-color: white; margin: 10% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 600px; position: relative; border-top: 4px solid #9333ea;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-purple-700">Mass Stock Adjustment</h3>
            <button onclick="closeModal('massAdjustModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="massAdjustForm" method="POST">
            @csrf
            
            <div class="mb-4 bg-purple-50 p-4 rounded-lg border border-purple-200">
                <p class="text-sm text-gray-700 mb-2">
                    <span class="font-semibold">Item:</span> <span id="massAdjustItemName" class="text-purple-700"></span>
                </p>
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Current Stock:</span> <span id="massAdjustCurrentStock" class="text-purple-700 font-bold"></span> <span id="massAdjustUnit"></span>
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-2">Adjustment Type</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                        <input type="radio" name="adjustment_type" value="add" class="mr-3" checked onchange="updateAdjustmentType()">
                        <div>
                            <div class="font-semibold text-green-600">Add Stock</div>
                            <div class="text-xs text-gray-500">Increase quantity</div>
                        </div>
                    </label>
                    <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition">
                        <input type="radio" name="adjustment_type" value="reduce" class="mr-3" onchange="updateAdjustmentType()">
                        <div>
                            <div class="font-semibold text-red-600">Reduce Stock</div>
                            <div class="text-xs text-gray-500">Decrease quantity</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Quantity</label>
                <input type="number" name="quantity" id="massAdjustQuantity" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600" placeholder="Enter quantity">
                <p class="text-xs text-gray-500 mt-1">Specify the amount to add or reduce</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Notes (Optional)</label>
                <textarea name="notes" id="massAdjustNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600" placeholder="Add notes about this adjustment..."></textarea>
            </div>

            <div id="previewSection" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200" style="display: none;">
                <p class="text-sm font-semibold text-gray-700 mb-2">Preview:</p>
                <p class="text-sm text-gray-600">
                    <span id="previewText"></span>
                </p>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('massAdjustModal')" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                    <i class="fas fa-check mr-2"></i>Confirm Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Batches Modal -->
<div id="batchesModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 58, 95, 0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 900px; position: relative; border-top: 4px solid #1e3a5f;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#1e3a5f]">Batch Details</h3>
            <button onclick="closeModal('batchesModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div id="batchesContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

<script>
let currentItemStock = 0;
let currentItemUnit = '';

document.getElementById('categoryFilter').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Item';
    document.getElementById('itemForm').action = "{{ route('admin.inventory.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('batchSection').style.display = 'block';
    document.getElementById('itemForm').reset();
    document.getElementById('itemModal').style.display = 'block';
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Item';
    document.getElementById('itemForm').action = "{{ url('admin/inventory') }}/" + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('batchSection').style.display = 'none';
    
    // Remove batch field requirements for edit
    document.getElementById('batchNumber').removeAttribute('required');
    document.getElementById('batchQuantity').removeAttribute('required');
    
    fetch('/admin/inventory/' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('itemName').value = data.name;
            document.getElementById('itemCategory').value = data.category;
            document.getElementById('itemDescription').value = data.description || '';
            document.getElementById('itemMinStock').value = data.minimum_stock_level;
            document.getElementById('itemUnit').value = data.unit;
            document.getElementById('itemPrice').value = data.selling_price;
            document.getElementById('itemSupplier').value = data.supplier_name || '';
            
            document.getElementById('itemModal').style.display = 'block';
        });
}

function openAddBatchModal(id) {
    document.getElementById('addBatchForm').action = "/admin/inventory/" + id + "/add-batch";
    document.getElementById('addBatchForm').reset();
    document.getElementById('addBatchModal').style.display = 'block';
}

function openMassAdjustModal(id, name, stock, unit) {
    currentItemStock = stock;
    currentItemUnit = unit;
    
    document.getElementById('massAdjustForm').action = "/admin/inventory/" + id + "/mass-adjust-stock";
    document.getElementById('massAdjustItemName').textContent = name;
    document.getElementById('massAdjustCurrentStock').textContent = stock;
    document.getElementById('massAdjustUnit').textContent = unit;
    document.getElementById('massAdjustForm').reset();
    document.getElementById('previewSection').style.display = 'none';
    
    // Reset to "Add" option
    document.querySelector('input[name="adjustment_type"][value="add"]').checked = true;
    
    document.getElementById('massAdjustModal').style.display = 'block';
}

function updateAdjustmentType() {
    updatePreview();
}

// Update preview when quantity changes
document.getElementById('massAdjustQuantity')?.addEventListener('input', function() {
    updatePreview();
});

function updatePreview() {
    const quantity = parseInt(document.getElementById('massAdjustQuantity').value) || 0;
    const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked').value;
    const previewSection = document.getElementById('previewSection');
    const previewText = document.getElementById('previewText');
    
    if (quantity > 0) {
        let newStock;
        let message;
        
        if (adjustmentType === 'add') {
            newStock = currentItemStock + quantity;
            message = `Adding ${quantity} ${currentItemUnit}: ${currentItemStock} → <span class="font-bold text-green-600">${newStock}</span> ${currentItemUnit}`;
        } else {
            newStock = currentItemStock - quantity;
            if (newStock < 0) {
                message = `<span class="text-red-600 font-bold">Error: Cannot reduce ${quantity} ${currentItemUnit}. Only ${currentItemStock} ${currentItemUnit} available.</span>`;
            } else {
                message = `Reducing ${quantity} ${currentItemUnit}: ${currentItemStock} → <span class="font-bold text-orange-600">${newStock}</span> ${currentItemUnit}`;
            }
        }
        
        previewText.innerHTML = message;
        previewSection.style.display = 'block';
    } else {
        previewSection.style.display = 'none';
    }
}

function openBatchesModal(id) {
    document.getElementById('batchesModal').style.display = 'block';
    document.getElementById('batchesContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></div>';
    
    fetch('/admin/inventory/' + id)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="overflow-x-auto"><table class="min-w-full border border-gray-200">';
            html += '<thead class="bg-gray-100"><tr>';
            html += '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Batch Number</th>';
            html += '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantity</th>';
            html += '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Manufacture Date</th>';
            html += '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Expiry Date</th>';
            html += '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>';
            html += '</tr></thead><tbody>';
            
            if (data.batches && data.batches.length > 0) {
                data.batches.forEach(batch => {
                    const expiryDate = batch.expiry_date ? new Date(batch.expiry_date) : null;
                    const today = new Date();
                    let statusClass = 'text-green-600';
                    let statusText = 'Active';
                    
                    if (expiryDate) {
                        if (expiryDate < today) {
                            statusClass = 'text-red-600 font-bold';
                            statusText = 'Expired';
                        } else if ((expiryDate - today) / (1000 * 60 * 60 * 24) <= 30) {
                            statusClass = 'text-orange-600 font-bold';
                            statusText = 'Expiring Soon';
                        }
                    }
                    
                    html += '<tr class="border-t hover:bg-gray-50">';
                    html += '<td class="px-4 py-3">' + batch.batch_number + '</td>';
                    html += '<td class="px-4 py-3">' + batch.quantity + '</td>';
                    html += '<td class="px-4 py-3">' + (batch.manufacture_date || 'N/A') + '</td>';
                    html += '<td class="px-4 py-3">' + (batch.expiry_date || 'N/A') + '</td>';
                    html += '<td class="px-4 py-3 ' + statusClass + '">' + statusText + '</td>';
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No batches found</td></tr>';
            }
            
            html += '</tbody></table></div>';
            document.getElementById('batchesContent').innerHTML = html;
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>

<style>
#categoryFilter {
    font-weight: bold;
    background-color: #ffd700;
    color: #111827;
    padding-right: 36px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 20px;
}

/* Radio button styling for adjustment type */
input[type="radio"]:checked + div {
    font-weight: bold;
}

input[type="radio"]:checked[value="add"] ~ div {
    color: #16a34a;
}

input[type="radio"]:checked[value="reduce"] ~ div {
    color: #dc2626;
}

label:has(input[type="radio"]:checked) {
    border-color: #9333ea !important;
    background-color: #faf5ff;
}
</style>
@endsection