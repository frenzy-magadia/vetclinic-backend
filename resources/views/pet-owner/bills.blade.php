@extends('layouts.app')

@section('title', 'My Bills')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold" style="color: #2c3e50;">My Bills</h1>
            <p style="color: #5d6d7e;">View your billing history and payment status</p>
        </div>
    </div>

    @if(session('success'))
        <div class="border px-4 py-3 rounded" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="border px-4 py-3 rounded" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Bills Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6 border-l-4" style="border-color: #d32f2f;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #5d6d7e;">Unpaid Bills</p>
                    <p class="text-2xl font-bold" style="color: #2c3e50;">{{ $bills->where('status', 'unpaid')->count() }}</p>
                </div>
                <div class="p-3 rounded-full" style="background-color: #fadbd8;">
                    <i class="fas fa-exclamation-circle text-xl" style="color: #d32f2f;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 border-l-4" style="border-color: #f39c12;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #5d6d7e;">Partially Paid</p>
                    <p class="text-2xl font-bold" style="color: #2c3e50;">{{ $bills->where('status', 'partial')->count() }}</p>
                </div>
                <div class="p-3 rounded-full" style="background-color: #fdebd0;">
                    <i class="fas fa-hourglass-half text-xl" style="color: #f39c12;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 border-l-4" style="border-color: #28a745;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #5d6d7e;">Paid Bills</p>
                    <p class="text-2xl font-bold" style="color: #2c3e50;">{{ $bills->where('status', 'paid')->count() }}</p>
                </div>
                <div class="p-3 rounded-full" style="background-color: #d5f4e6;">
                    <i class="fas fa-check-circle text-xl" style="color: #28a745;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($bills->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                    <thead style="background-color: #34495e;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Bill #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Paid</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Balance</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                        @foreach($bills as $bill)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: #2c3e50;">
                                #{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                <i class="fas fa-paw mr-2" style="color: #3498db;"></i>{{ $bill->pet->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                {{ $bill->doctor->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                {{ $bill->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium" style="color: #2c3e50;">
                                ₱{{ number_format($bill->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right" style="color: #28a745;">
                                ₱{{ number_format($bill->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" style="color: {{ $bill->balance > 0 ? '#d32f2f' : '#28a745' }};">
                                ₱{{ number_format($bill->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($bill->status === 'unpaid')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #fadbd8; color: #d32f2f;">
                                        Unpaid
                                    </span>
                                @elseif($bill->status === 'partial')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #fdebd0; color: #f39c12;">
                                        Partial
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #d5f4e6; color: #28a745;">
                                        Paid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="viewBill({{ $bill->id }})" class="transition" style="color: #0d5cb6;" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bills->hasPages())
            <div class="px-6 py-4 border-t" style="border-color: #e5e7eb;">
                {{ $bills->links() }}
            </div>
            @endif
        @else
            <div class="px-6 py-8 text-center">
                <i class="fas fa-file-invoice text-6xl mb-4" style="color: #d1d5db;"></i>
                <p class="text-lg font-medium" style="color: #5d6d7e;">No bills found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Bill Details Modal -->
<div id="billModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold" style="color: #2c3e50;">Bill Details</h3>
            <button onclick="closeBillModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="billModalContent" class="modal-body">
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i>
            </div>
        </div>
    </div>
</div>

<script>
function viewBill(billId) {
    const modal = document.getElementById('billModal');
    const modalContent = document.getElementById('billModalContent');
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i></div>';
    
    fetch(`/pet-owner/bills/${billId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const statusColors = {
                'unpaid': { bg: '#fadbd8', text: '#d32f2f' },
                'partial': { bg: '#fdebd0', text: '#f39c12' },
                'paid': { bg: '#d5f4e6', text: '#28a745' }
            };
            
            const color = statusColors[data.status] || statusColors['unpaid'];
            
            // Safely access nested properties
            const petName = data.pet?.name || 'Unknown';
            const ownerName = data.pet?.owner?.user?.name || 'Unknown';
            const doctorName = data.doctor?.user?.name || 'Unknown';
            
            modalContent.innerHTML = `
                <div class="space-y-6">
                    <!-- Bill Header -->
                    <div class="flex justify-between items-start pb-4 border-b" style="border-color: #e5e7eb;">
                        <div>
                            <h4 class="text-lg font-bold" style="color: #2c3e50;">Bill #${String(data.id).padStart(5, '0')}</h4>
                            <p class="text-sm" style="color: #5d6d7e;">Created: ${new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full" style="background-color: ${color.bg}; color: ${color.text};">
                            ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                        </span>
                    </div>

                    <!-- Pet and Doctor Info -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h5 class="text-sm font-semibold mb-2" style="color: #5d6d7e;">Pet Information</h5>
                            <p class="font-medium" style="color: #2c3e50;"><i class="fas fa-paw mr-2" style="color: #3498db;"></i>${petName}</p>
                            <p class="text-sm" style="color: #5d6d7e;">Owner: ${ownerName}</p>
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold mb-2" style="color: #5d6d7e;">Veterinarian</h5>
                            <p class="font-medium" style="color: #2c3e50;"><i class="fas fa-user-md mr-2" style="color: #0d5cb6;"></i>${doctorName}</p>
                        </div>
                    </div>

                    <!-- Bill Items -->
                    <div>
                        <h5 class="text-sm font-semibold mb-3" style="color: #5d6d7e;">Items & Services</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium" style="color: #5d6d7e;">Description</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium" style="color: #5d6d7e;">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium" style="color: #5d6d7e;">Unit Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium" style="color: #5d6d7e;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" style="border-color: #e5e7eb;">
                                    ${data.items.map(item => `
                                        <tr>
                                            <td class="px-4 py-2 text-sm" style="color: #2c3e50;">
                                                ${item.description}
                                                ${item.item_type === 'inventory' ? '<span class="ml-2 px-2 py-0.5 text-xs rounded" style="background-color: #e3f2fd; color: #1976d2;">Inventory</span>' : ''}
                                            </td>
                                            <td class="px-4 py-2 text-center text-sm" style="color: #5d6d7e;">${item.quantity}</td>
                                            <td class="px-4 py-2 text-right text-sm" style="color: #5d6d7e;">₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                            <td class="px-4 py-2 text-right text-sm font-medium" style="color: #2c3e50;">₱${(item.quantity * item.unit_price).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="border-t pt-4" style="border-color: #e5e7eb;">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span style="color: #5d6d7e;">Total Amount:</span>
                                <span class="font-medium" style="color: #2c3e50;">₱${parseFloat(data.total_amount).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span style="color: #5d6d7e;">Paid Amount:</span>
                                <span class="font-medium" style="color: #28a745;">₱${parseFloat(data.paid_amount).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t" style="border-color: #e5e7eb;">
                                <span style="color: #2c3e50;">Balance:</span>
                                <span style="color: ${data.balance > 0 ? '#d32f2f' : '#28a745'};">₱${parseFloat(data.balance).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    ${data.notes ? `
                        <div class="border-t pt-4" style="border-color: #e5e7eb;">
                            <h5 class="text-sm font-semibold mb-2" style="color: #5d6d7e;">Notes</h5>
                            <p class="text-sm" style="color: #2c3e50;">${data.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading bill:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl mb-3" style="color: #d32f2f;"></i>
                    <p class="font-medium mb-2" style="color: #d32f2f;">Error loading bill details</p>
                    <p class="text-sm" style="color: #5d6d7e;">${error.message}</p>
                    <p class="text-xs mt-2" style="color: #95a5a6;">Check browser console for details</p>
                </div>
            `;
        });
}

function closeBillModal() {
    document.getElementById('billModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('billModal');
    if (event.target == modal) {
        closeBillModal();
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBillModal();
    }
});
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
        overflow-y: auto;
    }
    
    .modal-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: modalFadeIn 0.3s ease-out;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 10;
    }
    
    .modal-close-btn {
        color: #95a5a6;
        transition: color 0.2s;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
    }
    
    .modal-close-btn:hover {
        color: #5d6d7e;
    }
    
    .modal-body {
        padding: 24px;
    }

    button:hover, a:hover {
        opacity: 0.8;
    }
</style>
@endsection