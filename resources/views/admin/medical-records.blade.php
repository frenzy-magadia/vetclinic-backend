@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header  -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Medical Records</h1>
            <p class="text-gray-600 mt-1">View and manage all patient medical records</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex justify-between items-center gap-4">
        <select class="bg-yellow-300 hover:bg-yellow-400 text-gray-800 px-6 py-2 rounded font-semibold transition cursor-pointer border-0">
            <option>All Status</option>
            <option>Active</option>
            <option>Follow-up Needed</option>
            <option>Resolved</option>
        </select>

        <div class="relative">
            <input type="text" placeholder="Search pet, owner, service..."
                class="gap-4 px-4 py-2 border border-gray-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent w-80">
            <i class="fas fa-search absolute right-5 top-2.5 text-gray-500"></i>
        </div>
    </div>

    <!-- Medical Records Table -->
    @if($medicalRecords->count())
        <div class="bg-gray-50 rounded-lg shadow overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Medical Records</h2>
            </div>
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pet</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Owner</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Diagnosis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Treatment</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($medicalRecords as $record)
                    <tr class="hover:bg-white transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-paw text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $record->pet->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $record->pet->owner->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $record->doctor->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 40) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 40) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('medical-records.show', $record->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded transition text-sm font-medium" title="View Details">
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6 px-6">
                {{ $medicalRecords->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gray-50 rounded-lg shadow">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Medical Records</h2>
            </div>
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-medical text-3xl text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Medical Records Found</h3>
                <p class="text-gray-600 mb-6">No medical records available at this time.</p>
            </div>
        </div>
    @endif
</div>
@endsection