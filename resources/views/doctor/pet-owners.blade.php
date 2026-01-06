@extends('layouts.app')

@section('title', 'Pet Owners')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1e3a5f]">Pet Owners</h1>
</div>

<div class="bg-white shadow-lg rounded-lg p-6 border-t-4 border-[#1e3a5f]">
    <!-- Search Bar -->
    <div class="mb-4 flex items-center gap-2">
        <form method="GET" action="{{ route('doctor.pet-owners') }}" class="flex items-center gap-2 flex-1">
            <input 
                type="text" 
                name="search" 
                class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1]" 
                placeholder="Search by name or email..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition"
            >
                <i class="fas fa-search text-sm"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('doctor.pet-owners') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times text-sm"></i>
                </a>
            @endif
        </form>
    </div>

    @if($petOwners->count())
        <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-[#1e3a5f]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Email</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Total Pets</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($petOwners as $owner)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ ($petOwners->currentPage() - 1) * $petOwners->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $owner->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $owner->user->email }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $owner->pets->count() }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-4">
                            <a href="{{ route('doctor.pet-owners.show', $owner->id) }}" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $petOwners->appends(['search' => request('search')])->links() }}</div>
    @else
        <p class="text-gray-500 text-center py-8">No pet owners found.</p>
    @endif
</div>
@endsection