<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Enums\RolesEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function edit(Request $request): Response
    {
        // Check if user is customer and render web profile view
        if ($request->user()->hasRole(RolesEnum::CUSTOMER->value)) {
            return Inertia::render('Profile/CustomerEdit', [
                'mustVerifyEmail'   => $request->user() instanceof MustVerifyEmail,
                'status'            => session('status'),
            ]);
        }

        // For admin users, show dashboard profile view
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail'   => $request->user() instanceof MustVerifyEmail,
            'status'            => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     * 
     * @param \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
