<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'guest_name' => 'required|string',
            'guest_email' => 'required|email',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
            $booking = $this->bookingService->createBooking(
                auth()->id(),
                $request->meeting_type_id,
                $request->all()
            );

            return response()->json($booking, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
