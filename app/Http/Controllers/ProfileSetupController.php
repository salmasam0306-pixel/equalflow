<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileSetupController extends Controller
{
    /**
     * Profile setup is no longer self-service.
     * The Principal sets job scope and specialties when assigning a department.
     * Anyone hitting this route gets redirected to the dashboard.
     */
    public function show()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Personal users and Principals never needed it.
        if ($user->isPersonal() || $user->isPrincipal()) {
            return redirect()->route('dashboard')
                ->with('info', 'Profile setup is not required for personal users or principals.');
        }

        // Members / Leaders: mark as ready so nothing keeps redirecting them here,
        // then send them to the dashboard.
        if (!$user->is_profile_ready) {
            $user->update([
                'is_profile_ready'     => true,
                'profile_completed_at' => $user->profile_completed_at ?? now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('info', 'Your job scope and specialties are set by the Principal.');
    }

    /**
     * Legacy POST target — no longer writes anything.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->is_profile_ready === false && !$user->isPersonal() && !$user->isPrincipal()) {
            $user->update([
                'is_profile_ready'     => true,
                'profile_completed_at' => $user->profile_completed_at ?? now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('info', 'Job scope and specialties are managed by the Principal.');
    }

    /**
     * Legacy "skip" — just marks the profile ready and sends the user home.
     */
    public function skip()
    {
        $user = auth()->user();

        if ($user && !$user->isPersonal() && !$user->isPrincipal()) {
            $user->update([
                'is_profile_ready'     => true,
                'profile_completed_at' => $user->profile_completed_at ?? now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('info', 'You can continue using EqualFlow.');
    }
}