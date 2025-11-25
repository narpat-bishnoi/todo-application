@extends('layouts.app')

@section('title', 'Invite Employee')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Invite Employee</h1>

        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <p class="text-sm text-blue-800">
                Enter the email address of the employee you want to invite. They will receive an invitation link via email to create their account.
            </p>
        </div>

        <form method="POST" action="{{ route('invitations.store') }}">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required
                    placeholder="employee@example.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Send Invitation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

