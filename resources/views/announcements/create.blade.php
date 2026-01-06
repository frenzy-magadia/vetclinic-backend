@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8 form-card">
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-bullhorn mr-2"></i>Create New Announcement
                </h2>
                <p class="text-sm mt-1">Share important information with your community</p>
            </div>
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 btn-back rounded-lg transition-all" style="text-decoration: none;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="error-box text-red-700 px-4 py-3 rounded-lg mb-4">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('announcements.store') }}" method="POST">
        @csrf

        <!-- Title -->
        <div class="mb-4">
            <label for="title" class="form-label">
                <i class="fas fa-heading"></i>Announcement Title *
            </label>
            <input type="text" name="title" id="title"
                value="{{ old('title') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Enter announcement title"
                required>
            @error('title') 
                <span class="text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span> 
            @enderror
        </div>

        <!-- Content -->
        <div class="mb-4">
            <label for="content" class="form-label">
                <i class="fas fa-align-left"></i>Content *
            </label>
            <textarea name="content" id="content" rows="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Enter announcement content"
                required>{{ old('content') }}</textarea>
            @error('content') 
                <span class="text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span> 
            @enderror
        </div>

        <!-- Expiration Date -->
        <div class="mb-6">
            <label for="expires_at" class="form-label">
                <i class="fas fa-calendar-times"></i>Expiration Date <span style="font-weight: 400; color: #6b7280;">(Optional)</span>
            </label>
            <input type="date" name="expires_at" id="expires_at"
                value="{{ old('expires_at') }}"
                min="{{ date('Y-m-d') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-info-circle"></i> Leave blank for no expiration
            </p>
            @error('expires_at') 
                <span class="text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span> 
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3" style="padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('announcements.index') }}" class="px-6 py-2 btn-back rounded-lg font-medium transition-all" style="text-decoration: none;">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-6 py-2 btn-primary text-white rounded-lg font-medium transition-all">
                <i class="fas fa-paper-plane mr-2"></i>Publish Announcement
            </button>
        </div>
    </form>
</div>

<style>
:root {
    --navy: #1e3a5f;
    --gold: #d4931d;
    --yellow: #fcd34d;
    --charcoal: #2d3748;
    --blue-primary: #003d82;
    --blue-bright: #0066cc;
}

/* Form Enhancements */
.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
}

.form-label i {
    color: #f59e0b;
    margin-right: 8px;
}

/* Input Focus Effects */
input:focus,
textarea:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

/* Card styling */
.form-card {
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

/* Header styling */
.page-header {
    background: #2c3e50;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}

.page-header h2 {
    color: white;
}

.page-header h2 i {
    color: #fbbf24;
}

.page-header p {
    color: rgba(255, 255, 255, 0.9);
}

/* Button styling */
.btn-back {
    background: #f3f4f6;
    color: var(--charcoal);
    border: 1px solid #d1d5db;
}

.btn-back:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: #0066cc;
    border: none;
}

.btn-primary:hover {
    background: #0052a3;
}

/* Error messages */
.error-box {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
}
</style>
@endsection