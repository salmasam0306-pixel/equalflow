<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileSetupController extends Controller
{
    /**
     * Show the profile setup form
     */
    public function show()
    {
        $user = auth()->user();
        
        // If profile is already completed, redirect to dashboard
        if ($user->profile_completed) {
            return redirect()->route('dashboard');
        }

        return view('profile-setup', compact('user'));
    }

    /**
     * Save the profile setup
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_scope' => 'required|string|max:255',
            'specialties' => 'required|array|min:1|max:5',
            'specialties.*' => 'string|max:255',
        ]);

        $user = auth()->user();
        
        $user->update([
            'job_scope' => $request->job_scope,
            'specialties' => $request->specialties,
            'profile_completed' => true,
        ]);

        // Redirect to dashboard with success message
        return redirect()->route('dashboard')
            ->with('success', 'Profile setup complete! Welcome to ' . ($user->currentOrganization()->name ?? 'your team'));
    }

    /**
     * Skip profile setup (for later)
     */
    public function skip()
    {
        $user = auth()->user();
        
        // Set profile as completed so they don't see the setup again
        $user->update(['profile_completed' => true]);

        return redirect()->route('dashboard')
            ->with('info', 'You can complete your profile later in settings.');
    }
}