<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'bio' => 'nullable|string',
            'timezone' => 'required|timezone',
        ]);

        $this->userService->updateProfile(auth()->id(), $request->all());

        return redirect()->back()->with('success', 'Profile updated');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $path = $request->file('avatar')->store('avatars', 'public');
        $this->userService->updateAvatar(auth()->id(), $path);

        return redirect()->back()->with('success', 'Avatar updated');
    }
}
