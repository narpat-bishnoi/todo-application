<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    /**
     * Display a listing of all invitations.
     */
    public function index()
    {
        $invitations = Invitation::with('inviter')
            ->latest()
            ->paginate(15);

        // Eager load users for all invitations to avoid N+1 queries
        $emails = $invitations->pluck('email');
        $users = User::whereIn('email', $emails)->get()->keyBy('email');
        
        // Attach users to invitations for easy access in the view
        $invitations->getCollection()->each(function ($invitation) use ($users) {
            $invitation->invitedUser = $users->get($invitation->email);
        });

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Show the form for creating a new invitation.
     */
    public function create()
    {
        return view('invitations.create');
    }

    /**
     * Store a newly created invitation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email'),
            ],
        ]);

        $token = Str::random(32);

        // Ensure token is unique
        while (Invitation::where('token', $token)->exists()) {
            $token = Str::random(32);
        }

        $invitation = Invitation::create([
            'email' => $validated['email'],
            'token' => $token,
            'invited_by' => auth()->id(),
        ]);

        Mail::to($validated['email'])->send(new InvitationMail($invitation));

        return redirect()->route('invitations.index')
            ->with('success', 'Invitation sent successfully!');
    }

    /**
     * Show the form for accepting an invitation.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            abort(404, 'This invitation has already been accepted.');
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Process the invitation acceptance.
     */
    public function processAccept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            abort(404, 'This invitation has already been accepted.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
        ]);

        // Assign employee role
        $user->assignRole('employee');

        $invitation->update([
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully! Welcome!');
    }
}
