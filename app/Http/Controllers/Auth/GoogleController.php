<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Google sign-in failed. Please try again.');
        }

        // ============================================================
        // 1. Try to find by google_id (returning Google user)
        // ============================================================
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {

            // ========================================================
            // 2. Fall back to email match (existing manual user)
            // ========================================================
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Existing account — link Google ID + backfill verification.
                // Google already proved they own this email, so mark verified.
                $user->update([
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {

                // ====================================================
                // 3. Brand new user — Google already verified email,
                //    so set email_verified_at immediately.
                // ====================================================
                $user = User::create([
                    'name'              => $googleUser->getName() ?? 'Google User',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'password'          => null,
                    'role'              => 'personal',
                    'is_profile_ready'  => false,
                    'email_verified_at' => now(),   // ← KEY LINE
                ]);
            }
        }

        // ============================================================
        // Safety net: if for any reason the user is still unverified,
        // fix it now before login. Google always returns a verified
        // email, so there's no scenario where this is wrong.
        // ============================================================
        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome, ' . $user->name . '!');
    }
}