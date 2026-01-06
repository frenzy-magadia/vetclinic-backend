@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold" style="color: #2c3e50;">Medical Records</h1>
            <p style="color: #5d6d7e;">Manage and view patient medical records</p>
        </div>
        <a href="{{ route('medical-records.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #0d5cb6;">
            <i class="fas fa-plus mr-2"></i>
            Add Medical Record
        </a>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Search Section -->
    <div class="flex items-center justify-end gap-4">
        <!-- Search Input -->
        <form method="GET" action="{{ route('doctor.medical-records') }}" class="flex items-center gap-2">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    class="pl-4 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    style="border-color: #d1d5db; min-width: 300px;"
                    placeholder="Search pet, owner, diagnosis..." 
                    value="{{ request('search') }}"
                >
                <button 
                    type="submit" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                >
                    <i class="fas fa-search"></i>
                </button>
            </div>
            @if(request('search'))
            <a href="{{ route('doctor.medical-records') }}" class="px-3 py-2 rounded-lg transition hover:bg-gray-200" style="background-color: #e5e7eb; color: #374151;" title="Clear search">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Records Table -->
    @if($medicalRecords->count())
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                    <thead style="background-color: #34495e;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Diagnosis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Treatment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Date</th>
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
                                <br><span class="text-xs" style="color: #5d6d7e;">{{ $record->pet->species ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $record->pet->owner->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                                {{ $record->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                        <i class="fas fa-file-medical text-3xl" style="color: #3498db;"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-2" style="color: #2c3e50;">No Medical Records Found</h3>
                <p class="mb-6" style="color: #5d6d7e;">
                    {{ request('search') ? 'No records match your search criteria.' : 'Get started by creating your first medical record.' }}
                </p>
                @if(!request('search'))
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center gap-2 px-6 py-2 rounded transition text-white" style="background-color: #0d5cb6;">
                    <i class="fas fa-plus"></i>
                    Create First Record
                </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
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

    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }
</style>
@endsection