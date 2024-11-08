<?php

namespace App\Livewire\Actions;

// use App\Events\UserActivityUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
      
      if (Auth::check()) {
            // Update the user's status to inactive
            $user = Auth::user();
            $user->update(['is_active' => false]);

           //  // Trigger the event to broadcast the updated status
//             event(new UserActivityUpdated($user->id, false));
        }

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
