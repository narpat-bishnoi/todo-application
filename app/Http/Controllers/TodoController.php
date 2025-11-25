<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $todos = Todo::with(['creator', 'assignee'])
                ->latest()
                ->paginate(15);
        } else {
            $todos = Todo::with(['creator', 'assignee'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->paginate(15);
        }

        return view('todos.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $employees = User::where('role', 'employee')->get();

        return view('todos.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $todo = Todo::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => Todo::STATUS_OPEN,
            'created_by' => auth()->id(),
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        // Send notification if assigned
        if ($todo->assigned_to) {
            $assignee = User::find($todo->assigned_to);
            $assignee->notify(new TodoAssignedNotification($todo));
        }

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = Todo::with(['creator', 'assignee'])->findOrFail($id);
        $user = auth()->user();

        // Employees can only view their assigned todos
        if (!$user->isAdmin() && !$todo->isAssignedTo($user)) {
            abort(403);
        }

        return view('todos.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $todo = Todo::findOrFail($id);
        $employees = User::where('role', 'employee')->get();

        return view('todos.edit', compact('todo', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::findOrFail($id);
        $user = auth()->user();

        // Check if user is admin or employee updating their own assigned todo status
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'status' => ['required', 'in:open,in_progress,completed'],
                'assigned_to' => ['nullable', 'exists:users,id'],
            ]);

            $oldAssignedTo = $todo->assigned_to;

            $todo->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'assigned_to' => $validated['assigned_to'] ?? null,
            ]);

            // Send notification if assignment changed
            if ($todo->assigned_to && $todo->assigned_to !== $oldAssignedTo) {
                $assignee = User::find($todo->assigned_to);
                $assignee->notify(new TodoAssignedNotification($todo));
            }
        } elseif ($todo->isAssignedTo($user)) {
            // Employee can only update status to in_progress or completed
            $validated = $request->validate([
                'status' => ['required', 'in:in_progress,completed'],
            ]);

            $todo->update([
                'status' => $validated['status'],
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('todos.index')
            ->with('success', 'Todo updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $todo = Todo::findOrFail($id);
        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Todo deleted successfully!');
    }
}
