<?php

namespace App\Http\Controllers;

use App\Services\MeetingTypeService;
use Illuminate\Http\Request;

class MeetingTypeController extends Controller
{
    protected MeetingTypeService $meetingTypeService;

    public function __construct(MeetingTypeService $meetingTypeService)
    {
        $this->meetingTypeService = $meetingTypeService;
    }

    public function index()
    {
        $meetingTypes = $this->meetingTypeService->getUserMeetingTypes(auth()->id());
        return view('meeting-types.index', compact('meetingTypes'));
    }

    public function create()
    {
        return view('meeting-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:5',
            'location_type' => 'required|in:google_meet,phone,custom_url,in_person',
        ]);

        $this->meetingTypeService->createMeetingType(auth()->id(), $request->all());

        return redirect()->route('meeting-types.index')->with('success', 'Meeting type created');
    }

    public function edit($id)
    {
        $meetingType = $this->meetingTypeService->getUserMeetingTypes(auth()->id());
        return view('meeting-types.edit', compact('meetingType'));
    }

    public function update(Request $request, $id)
    {
        $this->meetingTypeService->updateMeetingType(auth()->id(), $id, $request->all());
        return redirect()->route('meeting-types.index')->with('success', 'Meeting type updated');
    }

    public function destroy($id)
    {
        $this->meetingTypeService->deleteMeetingType(auth()->id(), $id);
        return redirect()->route('meeting-types.index')->with('success', 'Meeting type deleted');
    }
}
