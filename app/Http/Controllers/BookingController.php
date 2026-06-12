<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        return view('bookings.index');
    }

    public function show($id)
    {
        return view('bookings.show', compact('id'));
    }

    public function confirm($id)
    {
        $this->bookingService->confirmBooking(auth()->id(), $id);
        return redirect()->back()->with('success', 'Booking confirmed');
    }

    public function cancel($id, Request $request)
    {
        $this->bookingService->cancelBooking(auth()->id(), $id, $request->reason);
        return redirect()->back()->with('success', 'Booking cancelled');
    }
}
