@extends('layouts.app')

@section('title', 'Todos')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Todos</h1>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('todos.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Create Todo
            </a>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    @else
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($todos as $todo)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $todo->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $todo->description ? \Illuminate\Support\Str::limit($todo->description, 50) : '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'open' => 'bg-gray-100 text-gray-800',
                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$todo->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $todo->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if(auth()->user()->isAdmin())
                                {{ $todo->assignee ? $todo->assignee->name : 'Unassigned' }}
                            @else
                                {{ $todo->creator->name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(auth()->user()->isAdmin())
                                <div x-data="{ open{{ $todo->id }}: false }">
                                    <a href="{{ route('todos.edit', $todo) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <button 
                                        @click="open{{ $todo->id }} = true"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                    
                                    <!-- Delete Modal -->
                                    <div 
                                        x-show="open{{ $todo->id }}"
                                        x-cloak
                                        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                                        @click.away="open{{ $todo->id }} = false"
                                    >
                                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                            <div class="mt-3">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
                                                <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this todo?</p>
                                                <div class="flex justify-end space-x-3">
                                                    <button 
                                                        @click="open{{ $todo->id }} = false"
                                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                                                    >
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @if($todo->isAssignedTo(auth()->user()) && $todo->status !== 'completed')
                                    <div x-data="{ 
                                        showConfirm{{ $todo->id }}: false, 
                                        currentStatus{{ $todo->id }}: '{{ $todo->status }}',
                                        newStatus{{ $todo->id }}: '',
                                        handleStatusChange(event) {
                                            const selectEl = event.target;
                                            const newValue = selectEl.value;
                                            const currentValue = '{{ $todo->status }}';
                                            
                                            // Don't show confirmation if selecting current status
                                            if (newValue === '' || newValue === currentValue) {
                                                selectEl.value = currentValue;
                                                return;
                                            }
                                            
                                            // Store the new status
                                            this.newStatus{{ $todo->id }} = newValue;
                                            
                                            // Reset select to current value
                                            selectEl.value = currentValue;
                                            
                                            // Show confirmation modal
                                            this.showConfirm{{ $todo->id }} = true;
                                        },
                                        cancelChange() {
                                            this.showConfirm{{ $todo->id }} = false;
                                            this.newStatus{{ $todo->id }} = '';
                                            // Ensure select is reset
                                            const selectEl = document.getElementById('status-select-{{ $todo->id }}');
                                            if (selectEl) {
                                                selectEl.value = '{{ $todo->status == 'open' ? '' : $todo->status }}';
                                            }
                                        },
                                        confirmChange() {
                                            this.showConfirm{{ $todo->id }} = false;
                                            // Set the status value and submit
                                            const selectEl = document.getElementById('status-select-{{ $todo->id }}');
                                            if (selectEl) {
                                                selectEl.value = this.newStatus{{ $todo->id }};
                                            }
                                            document.getElementById('status-form-{{ $todo->id }}').submit();
                                        }
                                    }">
                                        <form id="status-form-{{ $todo->id }}" method="POST" action="{{ route('todos.update', $todo) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <select 
                                                id="status-select-{{ $todo->id }}"
                                                name="status" 
                                                class="text-sm border-gray-300 rounded-md"
                                                @change="handleStatusChange($event)"
                                            >
                                                <option value="" {{ $todo->status === 'open' ? 'selected' : '' }}>Select Status</option>
                                                <option value="in_progress" {{ $todo->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $todo->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                        
                                        <!-- Confirmation Modal -->
                                        <div 
                                            x-show="showConfirm{{ $todo->id }}"
                                            x-cloak
                                            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                                            @click.away="cancelChange()"
                                        >
                                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                                <div class="mt-3">
                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Status Change</h3>
                                                    <p class="text-sm text-gray-500 mb-4" style="white-space: normal;">
                                                        Are you sure you want to change the status to 
                                                        <span class="font-semibold" x-text="newStatus{{ $todo->id }} === 'in_progress' ? 'In Progress' : 'Completed'"></span>?
                                                    </p>
                                                    <div class="flex justify-end space-x-3">
                                                        <button 
                                                            type="button"
                                                            @click="cancelChange()"
                                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                                                        >
                                                            Cancel
                                                        </button>
                                                        <button 
                                                            type="button"
                                                            @click="confirmChange()"
                                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                                        >
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">No actions</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No todos found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $todos->links() }}
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

