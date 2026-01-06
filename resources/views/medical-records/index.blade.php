@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold" style="color: #2c3e50;">Medical Records</h1>
            <p style="color: #5d6d7e;">Manage pet medical records and history</p>
        </div>
        @if(!Auth::user()->isAdmin())
        <a href="{{ route('medical-records.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #0d5cb6;">
            <i class="fas fa-plus mr-2"></i>
            Add Medical Record
        </a>
        @endif
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

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Status Filter -->
        <form method="GET" action="{{ route('medical-records.index') }}" id="filterForm">
            <select name="status" id="statusFilter" class="px-4 py-2 font-bold rounded-lg cursor-pointer transition text-sm border-0" style="background-color: #f4d03f; color: #2c3e50;">
                <option value="">All Status</option>
                <option value="follow-up" {{ request('status') == 'follow-up' ? 'selected' : '' }}>Follow-up Needed</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </form>

        <!-- Search Input -->
        <form method="GET" action="{{ route('medical-records.index') }}" class="flex items-center gap-4 ml-auto">
            <input 
                type="text" 
                name="search" 
                class="px-4 py-2 border rounded-lg focus:outline-none" 
                style="border-color: #d1d5db;"
                placeholder="Search pet, owner, diagnosis..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-3 py-2 rounded-lg transition"
                style="background-color: #34495e; color: #ffffff;"
            >
                <i class="fas fa-search"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('medical-records.index') }}" class="px-3 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Medical Records Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($medicalRecords->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                    <thead style="background-color: #34495e;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Owner</th>
                            @if(!Auth::user()->isAdmin())
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Doctor</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Diagnosis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Treatment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Date</th>
                            @if(!Auth::user()->isAdmin())
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Status</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                        @foreach($medicalRecords as $record)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm" style="color: #2c3e50;">
                                {{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                                <i class="fas fa-paw mr-2" style="color: #3498db;"></i>{{ $record->pet->name }}
                                <br><span class="text-xs" style="color: #5d6d7e;">{{ $record->pet->species }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $record->pet->owner->user->name }}</td>
                            @if(!Auth::user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $record->doctor->user->name }}</td>
                            @endif
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">
                                    {{ Str::limit($record->diagnosis, 30) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->treatment }}">
                                    {{ Str::limit($record->treatment, 30) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                                {{ $record->created_at->format('M d, Y') }}
                            </td>
                            @if(!Auth::user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($record->follow_up_date && $record->follow_up_date >= now()->toDateString())
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-yellow-100 text-yellow-800 border-2 border-yellow-400">
                                        <i class="fas fa-clock mr-1"></i>Follow-up
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-green-100 text-green-800 border-2 border-green-400">
                                        <i class="fas fa-check-circle mr-1"></i>Resolved
                                    </span>
                                @endif
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('medical-records.show', $record->id) }}" class="transition inline-block" style="color: #3498db;" title="View Details">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                @else
                                    <div class="flex gap-4 items-center">
                                        <a href="{{ route('medical-records.show', $record->id) }}" class="transition inline-block" style="color: #3498db;" title="View Details">
                                            <i class="fas fa-eye text-lg"></i>
                                        </a>
                                        <a href="{{ route('medical-records.edit', $record->id) }}" class="transition inline-block" style="color: #f39c12;" title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="{{ route('medical-records.destroy', $record->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this medical record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="transition inline-block" style="color: #e74c3c;" title="Delete">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($medicalRecords->hasPages())
            <div class="px-6 py-4 border-t" style="border-color: #e5e7eb;">
                {{ $medicalRecords->appends(request()->query())->links() }}
            </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                        <i class="fas fa-file-medical text-3xl" style="color: #3498db;"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-2" style="color: #2c3e50;">No Medical Records Found</h3>
                <p class="mb-6" style="color: #5d6d7e;">
                    {{ request('search') || request('status') ? 'No records match your search criteria.' : 'Get started by creating your first medical record.' }}
                </p>
                @if(!Auth::user()->isAdmin() && !request('search') && !request('status'))
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center gap-2 px-6 py-2 rounded transition text-white" style="background-color: #0d5cb6;">
                    <i class="fas fa-plus"></i>
                    Create First Record
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    // Auto-submit form when status dropdown changes
    document.getElementById('statusFilter')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>

<style>
    #statusFilter {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }
    
    #statusFilter:hover {
        background-color: #f9e79f;
    }
    
    tbody td {
        vertical-align: middle;
        overflow: visible;
    }

    .fas {
        font-size: 1.125rem;
    }

    a:hover, button:hover {
        opacity: 0.8;
    }

    button[type="submit"] {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
</style>
@endsection