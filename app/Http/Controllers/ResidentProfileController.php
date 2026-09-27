<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateResidentPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResidentProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('resident');

        return view('resident-profile', compact('user'));
    }

    public function updatePassword(UpdateResidentPasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => Hash::make($request->validated('password')),
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        return redirect()->route('account.profile.edit')
            ->with('success', 'Your password has been changed. Use your new password the next time you sign in.');
    }
}
