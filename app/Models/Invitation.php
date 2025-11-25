<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    /**
     * Generate a unique token for the invitation.
     */
    public static function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Check if invitation has been accepted.
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Get the user who sent the invitation.
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the user account created from this invitation (if accepted).
     */
    public function getInvitedUserAttribute()
    {
        return User::where('email', $this->email)->first();
    }
}
