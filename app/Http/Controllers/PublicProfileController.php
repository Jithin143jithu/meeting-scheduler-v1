<?php

namespace App\Http\Controllers;

use App\Services\PublicProfileService;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    protected PublicProfileService $publicProfileService;

    public function __construct(PublicProfileService $publicProfileService)
    {
        $this->publicProfileService = $publicProfileService;
    }

    public function show($username)
    {
        $profile = $this->publicProfileService->getPublicProfileData($username);

        if (!$profile) {
            abort(404);
        }

        return view('public.profile', $profile);
    }

    public function getAvailableSlots($username, Request $request)
    {
        $slots = $this->publicProfileService->getAvailableSlots(
            $username,
            $request->date,
            $request->meeting_type_id
        );

        return response()->json($slots);
    }
}
