<?php

namespace App\Http\Controllers;

use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index()
    {
        $schedule = $this->availabilityService->getUserWeeklySchedule(auth()->id());
        return view('availability.index', compact('schedule'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array',
        ]);

        $this->availabilityService->setWeeklyAvailability(auth()->id(), $request->schedules);

        return redirect()->back()->with('success', 'Schedule updated');
    }
}
