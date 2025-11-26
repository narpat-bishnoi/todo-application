@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome, {{ auth()->user()->name }}!</h1>
        
        <div class="mb-6">
            <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded">
                {{ auth()->user()->getRoleName() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            <a href="{{ route('todos.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg shadow-md transition">
                <h3 class="text-xl font-semibold mb-2">View Todos</h3>
                <p class="text-blue-100">Manage your todos</p>
            </a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('todos.create') }}" class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg shadow-md transition">
                    <h3 class="text-xl font-semibold mb-2">Create Todo</h3>
                    <p class="text-green-100">Create a new todo</p>
                </a>

                <a href="{{ route('invitations.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg shadow-md transition">
                    <h3 class="text-xl font-semibold mb-2">View Invitations</h3>
                    <p class="text-indigo-100">View all invited users and status</p>
                </a>

                <a href="{{ route('invitations.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg shadow-md transition">
                    <h3 class="text-xl font-semibold mb-2">Invite Employee</h3>
                    <p class="text-purple-100">Send invitation to new employee</p>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

