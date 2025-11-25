<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
        'assigned_to',
    ];

    /**
     * Get the user who created this todo.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this todo.
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if todo is assigned to a specific user.
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }
}
